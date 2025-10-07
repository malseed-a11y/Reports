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
        $activity = $this->acts->editors_activity();

        echo '<h1>Editors Activity</h1>';

        if (!empty($activity['labels'])) {
            echo '<table border="1" cellpadding="10" cellspacing="0">';
            echo '<thead><tr><th>Editor Name</th><th>Number of Posts</th></tr></thead>';
            echo '<tbody>';
            foreach ($activity['labels'] as $index => $editor_name) {
                $post_count = $activity['data'][$index];
                echo '<tr>';
                echo '<td>' . esc_html($editor_name) . '</td>';
                echo '<td>' . esc_html($post_count) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No editors found.</p>';
        }
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
