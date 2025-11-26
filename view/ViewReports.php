<?php

namespace Reports\view;

use Reports\classes\DiskUsage;

if (!defined('ABSPATH')) die('-1');

class ViewReports
{
    public $disk;

    public function __construct()
    {
        $this->disk = new DiskUsage();
    }

    public function render_admin_page()
    {

        ob_start();
?>
        <div class="wrap-reports">
            <h1>Server Reports</h1>

            <div class="report-section">
                <div class="ram-card">
                    <h2>RAM Usage Table</h2>
                    <canvas id="ramChart"></canvas>
                </div>

                <div class="cpu-card">
                    <h2>CPU Usage Table</h2>
                    <canvas id="cpuChart"></canvas>
                </div>
            </div>

            <div class="disk-section">
                <h2>Disk Usage</h2>
                <canvas id="diskChart"></canvas>
                <?php
                //to gb
                $bytes_to_gb = function ($bytes) {
                    if ($bytes <= 0) {
                        return 0;
                    }
                    return round($bytes / (1024 * 1024 * 1024), 2);
                };
                $report = $this->disk->get_main_folders_report();

                // raw bytes
                $disk_total = isset($report['disk_total']) ? (int) $report['disk_total'] : 0;
                $disk_free  = isset($report['disk_free'])  ? (int) $report['disk_free']  : 0;
                $disk_used = max($disk_total - $disk_free, 0);



                echo "<div class='disk-stats'>";
                echo "<p>Total : " .  $bytes_to_gb($disk_total) . " GB</p>";
                echo "<p>Free : " . $bytes_to_gb($disk_free) . " GB</p>";
                echo "<p>Used : " . $bytes_to_gb($disk_used) . " GB</p>";
                echo "</div>";
                ?>


            </div>
        </div>
<?php

        return ob_get_clean();
    }
}
