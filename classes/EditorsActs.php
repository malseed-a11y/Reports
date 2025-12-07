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

        add_action('transition_post_status', [$this, 'handle_post_saved'], 10, 3);
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


        $this->db = new DbEditorActivities();

        //============================
        // Validate post type = 'post' AND user role AND only when published
        if ($post->post_type !== 'post') {
            return;
        }

        $user_id = get_current_user_id();
        if (!$this->is_valid($user_id)) {
            return;
        }

        if ($new_status !== 'publish' && $new_status !== 'trash') {
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
        // Get current data from DB
        $current_data = $this->db->get_activity($user_id);
        //=============================


        //=============================
        // Update edit count in DB

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
        if (($old_status === 'auto-draft' || $old_status === 'new' || $old_status === 'draft') && $new_status === 'publish') {

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


        //=============================
        // Update delete count in DB
        if ($new_status === 'trash') {

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
        }
        //=============================
    }


    public function editors_activity()
    {
        //============================
        // To insert data into chart

        $this->db = new DbEditorActivities();

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
