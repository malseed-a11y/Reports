<?php

namespace Reports\view;



if (!defined('ABSPATH')) die('-1');

use Reports\classes\EditorsActs;
use Reports\db\DbUsage;
use Reports\db\DbLogs;

class ViewLogs
{
    public $acts;
    public $db_usage;
    public $db_logs;

    public function __construct()
    {
        $this->acts = new EditorsActs();
        $this->db_usage = new DbUsage();
        $this->db_logs = new DbLogs();
    }

    public function render_logs_page()
    {
        echo '<div class="wrap-logs">';
        echo '<h1>Logs Reports</h1>';
        echo '<div class="editors-card">';
        echo '<h2>Editors Activity</h2>';
        echo '<canvas id="editorsChart" width="400" height="200"></canvas>';
        echo '</div>';

        //===============================
        // user count
        echo '<canvas id="userChart" height="200"></canvas>';

        //===============================
        // login activity


        echo '<h1>تقارير النشاطات</h1>';

        $login_activity = $this->db_logs->get_logs_data();
        if ($login_activity) {
            echo '<h2>نشاط تسجيل الدخول</h2>';
            echo '<table border="1" cellpadding="10" cellspacing="0">';
            echo '<thead>
                    <tr>
                        <th>User name</th>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>IP</th>
                        <th>Time</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
            foreach ($login_activity as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row['username']) . '</td>';
                echo '<td>' . esc_html($row['user_id']) . '</td>';
                echo '<td>' . esc_html($row['role_name']) . '</td>';
                echo '<td>' . esc_html($row['status_name']) . '</td>';
                echo '<td>' . esc_html($row['ip_address']) . '</td>';
                echo '<td>' . esc_html($row['login_time']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No login activity data found.</p>';
        }
    }
}
