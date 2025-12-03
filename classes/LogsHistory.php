<?php

namespace SimpleReportsNamespace\classes;

use SimpleReportsNamespace\db\DbLogs;

if (!defined('ABSPATH')) die('-1');

use WP_User;


class LogsHistory
{
    public $db;

    public function __construct()
    {
        $this->db = new DbLogs();
        add_action('wp_login', [$this, 'login_success'], 10, 2);
        add_action('wp_login_failed', [$this, 'login_failed']);
    }


    //============================
    //Get user role 
    public function get_user_role($user_id)
    {
        if (!$user_id) return 'Guest';

        $user = new WP_User($user_id);
        if (empty($user->roles)) return 'Guest';

        return reset($user->roles);
    }
    //===========================



    //============================
    //Get IP address
    public function get_ip_address()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    //============================



    //============================
    //Handle login insert to db
    public function login_activity_add($username, $user_id, $status, $ip, $role)
    {
        $time = current_time('mysql');
        $data = [
            'username' => $username,
            'user_id' => $user_id,
            'role_name' => $role,
            'status_name' => $status,
            'ip_address' => $ip,
            'login_time' => $time,
        ];

        return $this->db->insert_logs_data($data);
    }
    //============================


    //============================
    //Handle successful login
    public function login_success($user_login, $user)
    {
        $ip = $this->get_ip_address();
        $user_id = $user->ID;
        $role = $this->get_user_role($user_id);
        $this->login_activity_add($user_login, $user_id, '🟩 success', $ip, $role);
    }
    //============================


    //============================
    //Handle failed login
    public function login_failed($username)
    {
        $ip = $this->get_ip_address();
        $user_id = 0;
        $role = 'N/A';
        $this->login_activity_add($username, $user_id, '🟥 failed', $ip, $role);
    }
    //============================
}
