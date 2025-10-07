<?php
class db_manegar
{
    public $db;
    public $table_usage;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table_usage = $wpdb->prefix . 'table_usage';
    }

    // Create usage table
    public function create_usage_table()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $this->db->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_usage} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name_usage VARCHAR(255) NOT NULL,
            value_usage VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    // Insert usage data
    public function insert_usage_data($data)
    {
        if (empty($data) || !is_array($data)) return false;


        if (!$this->db->get_var("SHOW TABLES LIKE '{$this->table_usage}'")) {
            $this->create_usage_table();
        }

        return $this->db->insert($this->table_usage, $data);
    }

    // Get usage data
    public function get_usage_data($name_usage)
    {
        return $this->db->get_results(
            $this->db->prepare(
                "SELECT * FROM {$this->table_usage} WHERE name_usage = %s ORDER BY created_at DESC LIMIT 20",
                $name_usage,

            ),
            ARRAY_A
        );
    }

    //delete usage table
    public function delete_usage_table()
    {
        $this->db->query("DROP TABLE IF EXISTS {$this->table_usage}");
    }
}
