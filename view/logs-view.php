<?php

require_once plugin_dir_path(__DIR__) . 'classes/Editors-acts.php';
require_once plugin_dir_path(__DIR__) . 'classes/DB-class.php';

class logs_view
{
    public $acts;
    public $db;

    public function __construct()
    {
        $this->acts = new editors_acts();
        $this->db = new db_manegar();
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
        $users_count =  $this->db->get_usage_data('users_count');
        if ($users_count) {
            echo '<table border="1" cellpadding="10" cellspacing="0">';
            echo '<thead>
            <tr>
            <th>Total Users</th>
            <th>Date</th>
            </tr>
            </thead>';
            echo '<tbody>';
            foreach ($users_count as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row['value_usage']) . '</td>';
                echo '<td>' . esc_html($row['created_at']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No user count data found.</p>';
        }
    }
}
