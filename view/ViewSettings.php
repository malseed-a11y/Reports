<?php

namespace Reports\view;

if (!defined('ABSPATH')) {
    die('-1');
}

class ViewSettings
{
    public function render_settings_page()
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        if (
            isset($_POST['reports_settings_nonce']) &&
            wp_verify_nonce($_POST['reports_settings_nonce'], 'reports_settings_save')
        ) {
            $interval = isset($_POST['reports_usage_interval']) ? (int) $_POST['reports_usage_interval'] : 1000;
            if ($interval < 1000) {
                $interval = 1000;
            }
            update_option('reports_usage_interval', $interval);

            $retention = isset($_POST['reports_logs_retention_days']) ? (int) $_POST['reports_logs_retention_days'] : 30;
            if ($retention < 1) {
                $retention = 1;
            }
            update_option('reports_logs_retention_days', $retention);

            echo '<div class="updated"><p>Settings saved.</p></div>';
        }

        $current_interval  = (int) get_option('reports_usage_interval', 1000);
        if ($current_interval < 1000) {
            $current_interval = 1000;
        }

        $current_retention = (int) get_option('reports_logs_retention_days', 30);
        if ($current_retention < 5) {
            $current_retention = 5;
        }
?>
        <div class="wrap">
            <h1>Reports Settings</h1>

            <form method="post">
                <?php wp_nonce_field('reports_settings_save', 'reports_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="reports_usage_interval">
                                Update interval (ms)
                            </label>
                        </th>
                        <td>
                            <input
                                type="number"
                                id="reports_usage_interval"
                                name="reports_usage_interval"
                                min="1000"
                                step="500"
                                value="<?php echo esc_attr($current_interval); ?>" />
                            <p class="description">
                                Minimum is 1000 ms (1 second). Controls how often CPU/RAM charts update.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="reports_logs_retention_days">
                                Login logs retention (days)
                            </label>
                        </th>
                        <td>
                            <input
                                type="number"
                                id="reports_logs_retention_days"
                                name="reports_logs_retention_days"
                                min="1"
                                step="1"
                                value="<?php echo esc_attr($current_retention); ?>" />
                            <p class="description">
                                Old login activity older than this number of days will be removed automatically by a daily cron job.
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
<?php
    }
}
