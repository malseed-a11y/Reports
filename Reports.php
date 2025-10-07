<?php

/**
 * Plugin Name: Reports
 * Description: Reports of the website.
 * Version: 1.2
 * Author: mosaab
 */

if (!defined('ABSPATH')) die('-1');

require_once plugin_dir_path(__FILE__) . 'classes/DB-class.php';
require_once plugin_dir_path(__FILE__) . 'enqueue.php';
require_once plugin_dir_path(__FILE__) . 'view/reports-view.php';
require_once plugin_dir_path(__FILE__) . 'view/logs-view.php';

class wp_server_stats
{

    public $report_view;
    public $enqueue;
    public $logs_view;

    public function __construct()
    {
        $this->report_view = new report_view();
        $this->enqueue = new reports_enqueue();
        $this->logs_view = new logs_view();

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this->enqueue, 'enqueue']);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Reports',
            'Reports',
            // 'Server Reports',
            'manage_options',
            'server-reports',
            [$this->report_view, 'render_admin_page'],
            'dashicons-chart-area',
            4
        );
        add_submenu_page(
            'server-reports',
            'Logs Reports',
            'Logs Reports',
            'manage_options',
            'logs-reports',
            [$this->logs_view, 'render_logs_page']
        );
    }
}


if (is_admin() && !class_exists("wp_server_stats")) {
    new wp_server_stats();
}

register_activation_hook(__FILE__, function () {
    $db = new db_manegar();
    $db->create_usage_table();
});

register_deactivation_hook(__FILE__, function () {
    $db = new db_manegar();
    $db->delete_usage_table();
});
