<?php

namespace Reports\classes;

use Reports\db\DbLogs;

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

    public function get_user_role($user_id)
    {
        if (!$user_id) return 'Guest';

        $user = new WP_User($user_id);
        if (empty($user->roles)) return 'N/A';

        return reset($user->roles);
    }


    public function get_ip_address()
    {
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {

                $ip_list = explode(',', $_SERVER[$key]);
                foreach ($ip_list as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }

        return '0.0.0.0';
    }


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

    public function login_success($user_login, $user)
    {
        $ip = $this->get_ip_address();
        $user_id = $user->ID;
        $role = $this->get_user_role($user_id);
        $this->login_activity_add($user_login, $user_id, '🟩 success', $ip, $role);
    }

    public function login_failed($username)
    {
        $ip = $this->get_ip_address();
        $user_id = 0;
        $role = 'N/A';
        $this->login_activity_add($username, $user_id, '🟥 failed', $ip, $role);
    }
}
