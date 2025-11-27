<?php

/**
 * Plugin Name: Simple Reports
 * Description: Reports of the website.
 * Version: 1.2
 * Author: mosaab
 * Author URI: https://github.com/malseed-a11y
 * Text Domain: simple-reports
 * Domain Path: /languages
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Tested up to: 7.0
 */

namespace SimpleReportsNamespace;

if (!defined('ABSPATH')) die('-1');

define('REPORTS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use SimpleReportsNamespace\classes\LogsHistory;
use SimpleReportsNamespace\classes\EnqueueReports;
use SimpleReportsNamespace\classes\RamCpuUsage;

use SimpleReportsNamespace\db\DbEditorActivities;
use SimpleReportsNamespace\db\DbLogs;

use SimpleReportsNamespace\view\ViewReports;
use SimpleReportsNamespace\view\ViewLogs;
use SimpleReportsNamespace\view\ViewSettings;

class SimpleReports
{
    public $report_view;
    public $enqueue;
    public $logs_view;
    public $settings_view;
    public $db_logs;

    public function __construct()
    {
        $this->report_view   = new ViewReports();
        $this->logs_view     = new ViewLogs();
        $this->settings_view = new ViewSettings();
        $this->db_logs       = new DbLogs();
        $this->enqueue       = new EnqueueReports();
        new LogsHistory();

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this->enqueue, 'enqueue']);

        add_action('wp', [$this, 'register_my_cronjob']);
        add_action('reports_cleanup_logs', [$this, 'register_my_cronjob_function']);
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


    public function register_my_cronjob()
    {
        if (!wp_next_scheduled('reports_cleanup_logs')) {

            wp_schedule_event(time(), 'hourly', 'reports_cleanup_logs');
        }
    }
    public function register_my_cronjob_function()
    {
        $days = (int) get_option('reports_logs_days', 30) >= 1 ? (int) get_option('reports_logs_days', 30) : 30;

        $this->db_logs->delete_old_logs($days);
    }
}

// active plugin
if (!class_exists('SimpleReports')) {
    new SimpleReports();
}

// Ajax handler for RAM and CPU usage
add_action('wp_ajax_reports_usage', function () {
    $usage = new RamCpuUsage();
    $usage->output_json();
    wp_die();
});

add_action('wp_ajax_nopriv_reports_usage', function () {
    $usage = new RamCpuUsage();
    $usage->output_json();
    wp_die();
});


// Register activation and deactivation hooks
register_activation_hook(__FILE__, function () {
    $db_acts = new DbEditorActivities();
    $db_acts->create_table();

    $db_logs = new DbLogs();
    $db_logs->create_logs_table();
});

register_deactivation_hook(__FILE__, function () {
    $db_acts = new DbEditorActivities();
    $db_acts->delete_table();

    $db_logs = new DbLogs();
    $db_logs->delete_logs_table();
});
