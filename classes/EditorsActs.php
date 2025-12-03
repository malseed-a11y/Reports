<?php

namespace SimpleReportsNamespace\classes;

use SimpleReportsNamespace\db\DbEditorActivities;

if (!defined('ABSPATH')) {
    die('-1');
}

class EditorsActs
{
    private $db;

    public function __construct()
    {
        $this->db = new DbEditorActivities();

        add_action('transition_post_status', [$this, 'handle_post_saved'], 10, 3);

        add_action('deleted_post', [$this, 'handle_post_deleted'], 10, 2);
    }


    private function is_valid($user_id)
    {
        //============================
        // Validate user id and name 
        if (!$user_id) {
            return false;
        }

        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }
        //============================

        //============================
        // Validate user role
        $allowed_roles = ['editor', 'administrator'];

        foreach ($allowed_roles as $role) {
            if (in_array($role, (array) $user->roles, true)) {
                return true;
            }
        }
        //============================

        return false;
    }


    public function handle_post_saved($new_status, $old_status, $post)
    {
        //============================
        // Validate post type = 'post' AND user role AND only when published
        if ($post->post_type !== 'post') {
            return;
        }

        $user_id = get_current_user_id();
        if (!$this->is_valid($user_id)) {
            return;
        }

        if ($new_status !== 'publish') {
            return;
        }
        //============================

        //============================
        // make sure we only count onse
        static $processed = [];
        $key = $user_id . ':' . $post->ID;

        if (isset($processed[$key])) {
            return;
        }
        $processed[$key] = true;
        //============================

        //=============================
        // Update edit count in DB
        $current_data = $this->db->get_activity($user_id);

        if ($old_status === 'publish' && $new_status === 'publish') {

            $edit_number = isset($current_data['edits_number'])
                ? (int) $current_data['edits_number'] + 1
                : 1;

            $this->db->update_editor(
                $user_id,
                wp_get_current_user()->user_login,
                $current_data['posts_number'],
                $edit_number,
                $current_data['deletes_number']
            );
        }
        //============================


        //=============================
        // Update publish count in DB
        if ($old_status !== 'publish' && $new_status === 'publish') {

            $posts_number = count_user_posts($user_id, 'post');

            $this->db->update_editor(
                $user_id,
                wp_get_current_user()->user_login,
                $posts_number,
                $current_data['edits_number'],
                $current_data['deletes_number']
            );
        }
        //=============================
    }


    public function handle_post_deleted($post_id, $post)
    {

        //============================
        // Validate post type = 'post' AND user role
        if ($post->post_type !== 'post') {
            return;
        }
        $user_id = get_current_user_id();
        if (!$this->is_valid($user_id)) {
            return;
        }
        //============================


        // ===========================
        // make sure we only count onse
        static $processed_deletes = [];
        $key = $user_id . ':' . $post_id;

        if (isset($processed_deletes[$key])) {
            return;
        }
        $processed_deletes[$key] = true;
        // ===========================


        //=============================
        // Update delete count in DB
        $current_data = $this->db->get_activity($user_id);

        $delete_number = isset($current_data['deletes_number'])
            ? (int) $current_data['deletes_number'] + 1
            : 1;

        $this->db->update_editor(
            $user_id,
            wp_get_current_user()->user_login,
            $current_data['posts_number'],
            $current_data['edits_number'],
            $delete_number
        );
        //=============================
    }


    public function editors_activity()
    {
        //============================
        // To insert data into chart
        $rows = $this->db->get_all_activities();
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
        //============================
    }
}
