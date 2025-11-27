<?php

namespace SimpleReportsNamespace\classes;

use SimpleReportsNamespace\db\DbEditorActivities;

if (!defined('ABSPATH')) {
    die('-1');
}

class EditorsActs
{
    private $db;

    private static $just_published = [];

    public function __construct()
    {
        $this->db = new DbEditorActivities();

        add_action('transition_post_status', [$this, 'handle_post_published'], 10, 3);

        add_action('save_post', [$this, 'handle_post_edited'], 10, 3);

        add_action('deleted_post', [$this, 'handle_post_deleted'], 10, 2);
    }


    private function is_editor($user_id)
    {
        if (!$user_id) {
            return false;
        }

        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }

        $allowed_roles = ['editor', 'administrator'];

        foreach ($allowed_roles as $role) {
            if (in_array($role, (array) $user->roles, true)) {
                return true;
            }
        }

        return false;
    }


    public function handle_post_published($new_status, $old_status, $post)
    {
        if ($post->post_type !== 'post') {
            return;
        }

        if (wp_is_post_autosave($post->ID) || wp_is_post_revision($post->ID)) {
            return;
        }

        if ($old_status === 'publish' || $new_status !== 'publish') {
            return;
        }

        static $published_once = [];
        if (isset($published_once[$post->ID])) {
            return;
        }
        $published_once[$post->ID] = true;

        $user_id = get_current_user_id();
        if (!$this->is_editor($user_id)) {
            return;
        }

        self::$just_published[$post->ID] = true;

        $user = get_userdata($user_id);
        $username = $user->user_login;

        $this->db->add_activity($user_id, $username, 'post');
    }


    public function handle_post_edited($post_id, $post, $update)
    {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }

        if ($post->post_type !== 'post') {
            return;
        }

        if (!$update) {
            return;
        }

        if ($post->post_status !== 'publish') {
            return;
        }

        $user_id = get_current_user_id();
        if (!$this->is_editor($user_id)) {
            return;
        }

        if (isset(self::$just_published[$post_id])) {
            return;
        }

        static $processed_edits = [];
        if (isset($processed_edits[$post_id])) {
            return;
        }
        $processed_edits[$post_id] = true;

        $user = get_userdata($user_id);
        $username = $user->user_login;

        $this->db->add_activity($user_id, $username, 'edit');
    }


    public function handle_post_deleted($post_id, $post)
    {
        if ($post->post_type !== 'post') {
            return;
        }

        $user_id = get_current_user_id();
        if (!$this->is_editor($user_id)) {
            return;
        }

        static $processed_deletes = [];
        if (isset($processed_deletes[$post_id])) {
            return;
        }
        $processed_deletes[$post_id] = true;

        $user = get_userdata($user_id);
        $username = $user->user_login;

        $this->db->add_activity($user_id, $username, 'delete');
    }


    public function editors_activity()
    {
        $rows = $this->db->get_activity();

        $labels  = [];
        $totals  = [];
        $details = [];

        foreach ($rows as $row) {
            $posts   = (int) $row['posts_number'];
            $edits   = (int) $row['edits_number'];
            $deletes = (int) $row['deletes_number'];

            $labels[] = $row['username'];
            $totals[] = $posts + $edits + $deletes;

            $details[] = [
                'posts'   => $posts,
                'edits'   => $edits,
                'deletes' => $deletes,
            ];
        }

        return [
            'labels'  => $labels,
            'totals'  => $totals,
            'details' => $details,
        ];
    }
}
