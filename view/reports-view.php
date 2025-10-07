<?php
class report_view
{
    public function render_admin_page()
    {

        echo '<canvas id="ramChart" width="600" height="300"></canvas>';
        echo '<canvas id="cpuChart" width="600" height="300"></canvas>';
        echo '<canvas id="diskChart" width="400" height="400"></canvas>';
    }
}
