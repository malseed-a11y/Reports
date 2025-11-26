<?php

namespace Reports\db;

if (!defined('ABSPATH')) die('-1');

class DbLogs
{
    public $db;
    public $table_logs;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table_logs = $wpdb->prefix . 'report_table_logs';
    }

    public function create_logs_table()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $this->db->get_charset_collate();
        $sql = "CREATE TABLE {$this->table_logs} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            username VARCHAR(60) NOT NULL,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            role_name VARCHAR(50) NOT NULL,
            status_name VARCHAR(50) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            login_time DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    public function insert_logs_data($data)
    {
        if (empty($data) || !is_array($data)) return false;


        if (!$this->db->get_var("SHOW TABLES LIKE '{$this->table_logs}'")) {
            $this->create_logs_table();
        }

        return $this->db->insert($this->table_logs, $data);
    }

    public function get_logs_data()
    {
        return $this->db->get_results(
            "SELECT * FROM {$this->table_logs} ORDER BY id DESC LIMIT 15",
            ARRAY_A
        );
    }

    public function delete_logs_table()
    {
        $this->db->query("DROP TABLE IF EXISTS {$this->table_logs}");
    }


    public function delete_old_logs($days)
    {
        $days = (int) $days;
        if ($days <= 0) {
            return;
        }

        $threshold = gmdate('Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS);

        $sql = $this->db->prepare(
            "DELETE FROM {$this->table_logs} WHERE login_time < %s",
            $threshold
        );

        $this->db->query($sql);
    }
}
