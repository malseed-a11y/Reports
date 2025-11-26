<?php

namespace Reports\db;

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



    public function add_activity($user_id, $username, $type)
    {
        if ($this->db->get_var("SHOW TABLES LIKE '{$this->table_name}'") !== $this->table_name) {
            $this->create_table();
        }

        if ($type === 'post') {
            $column = 'posts_number';
        } elseif ($type === 'edit') {
            $column = 'edits_number';
        } elseif ($type === 'delete') {
            $column = 'deletes_number';
        } else {
            return false;
        }

        $now = current_time('mysql');

        $sql = $this->db->prepare(
            "INSERT INTO {$this->table_name}
                (user_id, username, {$column}, last_updated)
             VALUES (%d, %s, 1, %s)
             ON DUPLICATE KEY UPDATE
                {$column} = {$column} + 1,
                username = VALUES(username),
                last_updated = VALUES(last_updated)",
            $user_id,
            $username,
            $now
        );

        return $this->db->query($sql);
    }

    public function get_activity()
    {

        return $this->db->get_results(
            "SELECT user_id, username, posts_number, edits_number, deletes_number
             FROM {$this->table_name}",
            ARRAY_A
        );
    }

    public function delete_activity_table()
    {
        $this->db->query("DROP TABLE IF EXISTS {$this->table_name}");
    }


    public function delete_old_logs($days)
    {
        $days = (int) $days;
        if ($days <= 0) {
            return;
        }

        $threshold = gmdate('Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS);

        $sql = $this->db->prepare(
            "DELETE FROM {$this->table_name} WHERE last_updated < %s",
            $threshold
        );

        $this->db->query($sql);
    }
}
