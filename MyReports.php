<?php

/**
 * Plugin Name: Reports
 * Description: Reports of the website.
 * Version: 1.2
 * Author: mosaab
 */

namespace Reports;

if (!defined('ABSPATH')) die('-1');

define('REPORTS_PLUGIN_URL', plugin_dir_url(__FILE__));
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use Reports\classes\LogsHistory;


use Reports\db\DbUsage;
use Reports\db\DbLogs;
use Reports\view\ViewReports;
use Reports\view\ViewLogs;
use Reports\classes\EnqueueReports;

class MyReports
{

    public $report_view;
    public $enqueue;
    public $logs_view;

    public function __construct()
    {
        $this->report_view = new ViewReports();
        $this->logs_view   = new ViewLogs();
        $this->enqueue     = new EnqueueReports();
        new LogsHistory();


        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this->enqueue, 'enqueue']);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'Reports',
            'Reports',
            'manage_options',
            'server-reports',
            [$this, 'render_reports_page'],
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

    public function render_reports_page()
    {
        echo $this->report_view->render_admin_page();
    }
}

if (!class_exists('MyReports')) {
    new MyReports();
}


register_activation_hook(__FILE__, function () {
    $db_usage = new DbUsage();
    $db_usage->create_usage_table();

    $db_logs = new DbLogs();
    $db_logs->create_logs_table();
});

register_deactivation_hook(__FILE__, function () {
    $db_usage = new DbUsage();
    $db_usage->delete_usage_table();

    $db_logs = new DbLogs();
    $db_logs->delete_logs_table();
});
