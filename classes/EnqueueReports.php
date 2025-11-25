<?php

namespace Reports\classes;

if (!defined('ABSPATH')) die('-1');

use Reports\classes\DiskUsage;
use Reports\classes\RamCpuUsage;
use Reports\classes\UserCount;
use Reports\db\DbUsage;
use Reports\classes\EditorsActs;


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
        $this->usage->save_usage();

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
        // RAM usage 
        // =======================================================
        $ram_usage = $this->db->get_usage_data('RAM');
        $ram_labels = array_column($ram_usage, 'created_at');
        $ram_data = array_column($ram_usage, 'value_usage');

        wp_enqueue_script(
            'ram-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/ram_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('ram-chart-js', 'ramData', [
            'labels' => $ram_labels,
            'data' => $ram_data,
        ]);

        // =======================================================
        // CPU usage 
        // =======================================================
        $cpu_usage = $this->db->get_usage_data('CPU');
        $cpu_labels = array_column($cpu_usage, 'created_at');
        $cpu_data = array_column($cpu_usage, 'value_usage');
        wp_enqueue_script(
            'cpu-chart-js',
            REPORTS_PLUGIN_URL . 'assets/js/cpu_chart.js',
            ['chart-js'],
            null,
            true
        );

        wp_localize_script('cpu-chart-js', 'cpuData', [
            'labels' => $cpu_labels,
            'data' => $cpu_data,
        ]);

        // =======================================================
        // Users count 
        // =======================================================
        $user_count = $this->db->get_usage_data('users_count');
        $user_labels = array_column($user_count, 'created_at');
        $user_data = array_column($user_count, 'value_usage');

        wp_enqueue_script('user-chart-js', REPORTS_PLUGIN_URL . 'assets/js/user_chart.js', ['chart-js'], null, true);

        wp_localize_script('user-chart-js', 'userData', [
            'labels' => $user_labels,
            'data' => $user_data,
        ]);

        // =======================================================
        // Disk chart
        // =======================================================
        $disk_total = $this->disk->get_full_size();
        $disk_free  = $this->disk->get_free_size();
        $disk_used  = $disk_total - $disk_free;

        wp_enqueue_script('disk-chart-js', REPORTS_PLUGIN_URL . 'assets/js/disk_chart.js', ['chart-js'], null, true);

        wp_localize_script('disk-chart-js', 'diskData', [
            'used' => round($disk_used / (1024 * 1024 * 1024), 2),
            'free' => round($disk_free / (1024 * 1024 * 1024), 2),
        ]);

        // =======================================================
        // Editors activity 
        // =======================================================
        $editors_activity = $this->editors->editors_activity();
        wp_enqueue_script('editors-chart-js', REPORTS_PLUGIN_URL . 'assets/js/editors_chart.js', ['chart-js'], null, true);

        wp_localize_script('editors-chart-js', 'editorsData', [
            'labels' => $editors_activity['labels'],
            'data' => $editors_activity['data'],
        ]);
    }
}
