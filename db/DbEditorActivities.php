<?php

namespace SimpleReportsNamespace\db;

if (!defined('ABSPATH')) {
    die('-1');
}

class DbEditorActivities
{
    public $db;
    public $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->db        = $wpdb;
        $this->table_name = $wpdb->prefix . 'reports_editors_activity';
    }

    public function create_table()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $this->db->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            user_id BIGINT(20) UNSIGNED NOT NULL,
            username VARCHAR(60) NOT NULL,
            posts_number INT(11) NOT NULL DEFAULT 0,
            edits_number INT(11) NOT NULL DEFAULT 0,
            deletes_number INT(11) NOT NULL DEFAULT 0,
            last_updated DATETIME NOT NULL,
            PRIMARY KEY (user_id)
        ) $charset_collate;";

        dbDelta($sql);
    }



    public function insert_new_editor($user_id, $username)
    {
        if ($this->db->get_var("SHOW TABLES LIKE '{$this->table_name}'") !== $this->table_name) {
            $this->create_table();
        }

        $data = [
            'user_id'      => $user_id,
            'username'     => $username,
            'posts_number' => 0,
            'edits_number' => 0,
            'deletes_number' => 0,
            'last_updated' => current_time('mysql'),
        ];
        $this->db->insert($this->table_name, $data);
    }


    public function update_editor($user_id, $username, $post_number = 0, $edit_number = 0, $delete_number = 0)
    {

        $existing = $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->table_name} WHERE user_id = %d",
                $user_id
            ),
            ARRAY_A
        );
        if (!$existing) {
            $this->insert_new_editor($user_id, $username);
        }


        $data = [
            'user_id'      => $user_id,
            'username'     => $username,
            'posts_number' => $post_number,
            'edits_number' => $edit_number,
            'deletes_number' => $delete_number,
            'last_updated' => current_time('mysql'),
        ];

        $this->db->update(
            $this->table_name,
            $data,
            ['user_id' => $user_id]
        );
    }


    public function get_activity($user_id)
    {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT user_id, username, posts_number, edits_number, deletes_number
             FROM {$this->table_name}
             WHERE user_id = %d",
                $user_id
            ),
            ARRAY_A
        );
    }
    public function get_all_activities()
    {
        return $this->db->get_results(
            "SELECT user_id, username, posts_number, edits_number, deletes_number
         FROM {$this->table_name}",
            ARRAY_A
        );
    }

    public function delete_table()
    {
        $this->db->query("DROP TABLE IF EXISTS {$this->table_name}");
    }


    public function delete_old_logs($days)
    {
        $days = (int) $days;
        if ($days <= 0) {
            return;
        }

        $delete_date = gmdate('Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS);

        $sql = $this->db->prepare(
            "DELETE FROM {$this->table_name} WHERE last_updated < %s",
            $delete_date
        );

        $this->db->query($sql);
    }
}
