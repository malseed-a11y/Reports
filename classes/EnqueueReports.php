<?php

namespace SimpleReports\classes;

if (!defined('ABSPATH')) die('-1');

use SimpleReports\classes\DiskUsage;
use SimpleReports\classes\RamCpuUsage;
use SimpleReports\classes\UserCount;
use SimpleReports\db\DbUsage;
use SimpleReports\classes\EditorsActs;


class EnqueueReports
{
    public $db;
    public $disk;
    public $usage;
    public $editors;
    public $users;

    public function __construct()
    {
        $this->usage = new RamCpuUsage();
        $this->db = new DbUsage();
        $this->disk = new DiskUsage();
        $this->editors = new EditorsActs();
        // $this->usage->save_usage();

        $this->users = new UserCount();
        $this->users->save_users_count();
    }

    public function enqueue()
    {
        //==styles==
        wp_enqueue_style('reports-styles', REPORTS_PLUGIN_URL . 'assets/css/reports.css');
        wp_enqueue_style('logs-styles', REPORTS_PLUGIN_URL . 'assets/css/logs.css');

        //==js==
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js', [], null);

        // =======================================================
        // RAM and CPU
        // =======================================================

        $interval = (int) get_option('usage_interval', 1000);
        if ($interval < 1000) {
            $interval = 1000;
        }
        // CPU
        wp_enqueue_script(
            'cpu-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/cpu_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('cpu-chart-js', 'CpuAjax', [
            'url'      => admin_url('admin-ajax.php'),
            'action'   => 'reports_usage',
            'interval' => $interval,
        ]);

        // RAM
        wp_enqueue_script(
            'ram-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/ram_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('ram-chart-js', 'RamAjax', [
            'url'      => admin_url('admin-ajax.php'),
            'action'   => 'reports_usage',
            'interval' => $interval,
        ]);


        // =======================================================
        // Users count 
        // =======================================================
        // $user_count = $this->db->get_usage_data('users_count');
        // $user_labels = array_column($user_count, 'created_at');
        // $user_data = array_column($user_count, 'value_usage');

        // wp_enqueue_script('user-chart-js', REPORTS_PLUGIN_URL . 'assets/js/user_chart.js', ['chart-js'], null, true);

        // wp_localize_script('user-chart-js', 'userData', [
        //     'labels' => $user_labels,
        //     'data' => $user_data,
        // ]);

        // =======================================================
        // Disk chart
        // =======================================================
        $report = $this->disk->get_main_folders_report();

        // main folders (bytes)
        $_themes  = isset($report['themes'])  ? (int) $report['themes']  : 0;
        $_plugins = isset($report['plugins']) ? (int) $report['plugins'] : 0;
        $_uploads = isset($report['uploads']) ? (int) $report['uploads'] : 0;
        $_admin  = isset($report['wp_admin']) ? (int) $report['wp_admin'] : 0;
        // to MB
        function bytes_to_mb($bytes)
        {
            if ($bytes <= 0) {
                return 0;
            }
            return round($bytes / (1024  * 1024), 2);
        }

        wp_enqueue_script(
            'disk-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/disk_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('disk-chart-js', 'diskData', [
            'themes'  => bytes_to_mb($_themes),
            'plugins' => bytes_to_mb($_plugins),
            'uploads' => bytes_to_mb($_uploads),
            'admin'   => bytes_to_mb($_admin),

        ]);


        // =======================================================
        // Editors activity 
        // =======================================================
        $editors_activity = $this->editors->editors_activity();

        wp_enqueue_script(
            'editors-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/editors_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('editors-chart-js', 'editorsData', [
            'labels'  => $editors_activity['labels'],
            'totals'  => $editors_activity['totals'],
            'details' => $editors_activity['details'],
        ]);
    }
}
