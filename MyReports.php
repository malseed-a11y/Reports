<?php

/**
 * Plugin Name: Reports
 * Description: Reports of the website.
 * Version: 1.2
 * Author: mosaab
 * Author URI: https://github.com/malseed-a11y
 * Text Domain: reports
 * Domain Path: /languages
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Tested up to: 7.0
 */

namespace Reports;

if (!defined('ABSPATH')) die('-1');

define('REPORTS_PLUGIN_URL', plugin_dir_url(__FILE__));
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use Reports\classes\LogsHistory;
use Reports\classes\EnqueueReports;
use Reports\classes\RamCpuUsage;

use Reports\db\DbLogs;

use Reports\view\ViewReports;
use Reports\view\ViewLogs;
use Reports\view\ViewSettings;

class MyReports
{
    public $report_view;
    public $enqueue;
    public $logs_view;
    public $settings_view;

    public function __construct()
    {
        $this->report_view   = new ViewReports();
        $this->logs_view     = new ViewLogs();
        $this->settings_view = new ViewSettings();
        $this->enqueue       = new EnqueueReports();
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

        add_submenu_page(
            'server-reports',
            'Reports Settings',
            'Settings',
            'manage_options',
            'reports-settings',
            [$this->settings_view, 'render_settings_page']
        );
    }

    public function render_reports_page()
    {
        echo $this->report_view->render_admin_page();
    }
}

new MyReports();

add_action('wp_ajax_reports_usage', function () {
    $usage = new RamCpuUsage();
    $usage->output_json();
});

add_action('wp_ajax_nopriv_reports_usage', function () {
    $usage = new RamCpuUsage();
    $usage->output_json();
});


add_action('reports_cleanup_logs', function () {

    $days = (int) get_option('reports_logs_retention_days', 30);
    if ($days < 1) {
        $days = 30;
    }

    $db_logs = new \Reports\db\DbLogs();
    $db_logs->delete_old_logs($days);
});

register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('reports_cleanup_logs')) {
        wp_schedule_event(time(), 'daily', 'reports_cleanup_logs');
    }
});

register_deactivation_hook(__FILE__, function () {
    $timestamp = wp_next_scheduled('reports_cleanup_logs');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'reports_cleanup_logs');
    }
});
