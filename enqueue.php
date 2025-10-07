<?php

require_once plugin_dir_path(__FILE__) . 'classes/DB-class.php';
require_once plugin_dir_path(__FILE__) . 'classes/Disk-usage.php';
require_once plugin_dir_path(__FILE__) . 'classes/RAM-CPU-usage.php';

class reports_enqueue
{
    public $db;
    public $disk;
    public $usage;
    public function __construct()
    {
        $this->usage = new ram_cpu_usage();
        $this->db = new db_manegar();
        $this->disk = new disk_usage();

        $this->usage->save_usage();
    }

    public function enqueue($hook)
    {
        if ($hook !== 'toplevel_page_server-reports') return;

        //==styles==
        wp_enqueue_style('reports-styles', plugin_dir_url(__FILE__) . 'assets/css/reports.css');
        wp_enqueue_style('logs-styles', plugin_dir_url(__FILE__) . 'assets/css/logs.css');

        //==js==
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js');

        // RAM chart
        $ram_usage = $this->db->get_usage_data('RAM');

        wp_enqueue_script('ram-chart-js', plugin_dir_url(__FILE__) . 'assets/js/ram_chart.js', ['chart-js'], null, true);

        wp_localize_script('ram-chart-js', 'ramData', [
            'times' => array_column($ram_usage, 'created_at'),
            'values' => array_column($ram_usage, 'value_usage'),
        ]);

        // CPU chart
        $cpu_usage = $this->db->get_usage_data('CPU');

        wp_enqueue_script('cpu-chart-js', plugin_dir_url(__FILE__) . 'assets/js/cpu_chart.js', ['chart-js'], null, true);

        wp_localize_script('cpu-chart-js', 'cpuData', [
            'times' => array_column($cpu_usage, 'created_at'),
            'values' => array_column($cpu_usage, 'value_usage'),
        ]);

        // Disk chart
        $disk_total = $this->disk->get_full_size();
        $disk_free  = $this->disk->get_free_size();
        $disk_used  = $disk_total - $disk_free;

        wp_enqueue_script('disk-chart-js', plugin_dir_url(__FILE__) . 'assets/js/disk_chart.js', ['chart-js'], null, true);

        wp_localize_script('disk-chart-js', 'diskData', [
            'used' => round($disk_used / (1024 * 1024 * 1024), 2),
            'free' => round($disk_free / (1024 * 1024 * 1024), 2),
        ]);
    }
}
