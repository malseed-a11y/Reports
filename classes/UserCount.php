<?php

namespace Reports\classes;

if (!defined('ABSPATH')) die('-1');

use Reports\db\DbUsage;

class UserCount
{
    public $db;
    public function __construct()
    {
        $this->db = new DbUsage();
    }

    public function get_users_count()
    {
        $count_users = count_users();
        return $count_users['total_users'];
    }
    public function save_users_count()
    {
        $data = [
            'name_usage' => 'users_count',
            'value_usage' => $this->get_users_count(),
        ];
        $this->db->insert_usage_data($data);
    }
}
