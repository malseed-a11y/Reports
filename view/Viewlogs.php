<?php

namespace SimpleReportsNamespace\view;

use SimpleReportsNamespace\classes\EditorsActs;
use SimpleReportsNamespace\db\DbLogs;

class ViewLogs
{
    public $acts;
    public $db_logs;
    public function __construct() {}

    public function render_logs_page()
    {
        $this->acts = new EditorsActs();
        $this->db_logs = new DbLogs();

?>
        <div class="wrap-logs">
            <h1>Logs Reports</h1>
            <div class="editors-card">
                <h2>Editors Activity</h2>
                <canvas id="editorsChart" width="400" height="200"></canvas>
            </div>

            <h1>Activity Reports</h1>

            <?php
            $login_activity = $this->db_logs->get_logs_data();

            if ($login_activity) {
            ?>
                <h2>Login Activity</h2>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User name</th>
                            <th>ID</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>IP</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($login_activity as $row) {
                        ?>
                            <tr>
                                <td><?php echo esc_html($row['username']); ?></td>
                                <td><?php echo esc_html($row['user_id']); ?></td>
                                <td><?php echo esc_html($row['role_name']); ?></td>
                                <td><?php echo esc_html($row['status_name']); ?></td>
                                <td><?php echo esc_html($row['ip_address']); ?></td>
                                <td><?php echo esc_html($row['login_time']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No login activity data found.</p>
            <?php } ?>
        </div>
<?php
    }
}
