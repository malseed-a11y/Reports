<?php

namespace Reports\view;

if (!defined('ABSPATH')) die('-1');

class ViewReports
{

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
            </div>
        </div>
<?php

        return ob_get_clean();
    }
}
