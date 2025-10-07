<?php
class report_view
{
    public function render_admin_page()
    {
        echo '<div class="wrap-reports">';
        echo '<h1>Server Reports</h1>';
        //2 cards are for ram and cpu usage    type: "line",
        echo '<div class="report-section"> <div class="ram-card">';
        echo '<h2>RAM Usage Table</h2>';
        echo '<canvas id="ramChart"></canvas>';
        echo '</div> <div class="cpu-card">';
        echo '<h2>CPU Usage Table</h2>';
        echo '<canvas id="cpuChart" ></canvas>';
        echo '</div> </div>';
        echo '<div class="disk-section">';
        echo '<h2>Disk Usage </h2>';
        echo '<canvas id="diskChart"></canvas>';

        echo '</div>';
    }
}
