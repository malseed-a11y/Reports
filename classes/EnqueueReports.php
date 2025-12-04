<?php

namespace SimpleReportsNamespace\classes;

if (!defined('ABSPATH')) die('-1');

use SimpleReportsNamespace\classes\DiskUsage;
use SimpleReportsNamespace\classes\RamCpuUsage;
use SimpleReportsNamespace\classes\EditorsActs;


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
        $this->disk = new DiskUsage();
        $this->editors = new EditorsActs();
    }

    private function bytes_to_mb($bytes)
    {
        if ($bytes <= 0) {
            return 0;
        }
        return round($bytes / (1024  * 1024), 2);
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
        // Disk chart
        // =======================================================
        $report = $this->disk->get_main_folders_report();

        // main folders (bytes)
        $_themes  = isset($report['themes'])  ? (int) $report['themes']  : 0;
        $_plugins = isset($report['plugins']) ? (int) $report['plugins'] : 0;
        $_uploads = isset($report['uploads']) ? (int) $report['uploads'] : 0;
        $_admin  = isset($report['wp_admin']) ? (int) $report['wp_admin'] : 0;
        // to MB


        wp_enqueue_script(
            'disk-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/disk_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('disk-chart-js', 'diskData', [
            'themes'  => $this->bytes_to_mb($_themes),
            'plugins' => $this->bytes_to_mb($_plugins),
            'uploads' => $this->bytes_to_mb($_uploads),
            'admin'   => $this->bytes_to_mb($_admin),

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
