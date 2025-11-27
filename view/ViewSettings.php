<?php

namespace SimpleReports\view;

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
            $interval = isset($_POST['usage_interval']) && $_POST['usage_interval'] >= 1000 ? (int) $_POST['usage_interval'] : 1000;

            update_option('usage_interval', $interval);

            $retention = isset($_POST['reports_logs_days']) && $_POST['reports_logs_days'] >= 5 ? (int) $_POST['reports_logs_days'] : 30;
            update_option('reports_logs_days', $retention);

            echo '<div class="updated"><p>Settings saved.</p></div>';
        }

        $current_interval  = (int) get_option('usage_interval', 1000) >= 1000 ? (int) get_option('usage_interval', 1000) : 1000;


        $current_retention = (int) get_option('reports_logs_days', 30) >= 5 ? (int) get_option('reports_logs_days', 30) : 5;

?>
        <div class="wrap">
            <h1>Reports Settings</h1>

            <form method="post">
                <?php wp_nonce_field('reports_settings_save', 'reports_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="usage_interval">
                                Update interval (ms)
                            </label>
                        </th>
                        <td>
                            <input
                                type="number"
                                id="usage_interval"
                                name="usage_interval"
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
                            <label for="reports_logs_days">
                                Login logs retention (days)
                            </label>
                        </th>
                        <td>
                            <input
                                type="number"
                                id="reports_logs_days"
                                name="reports_logs_days"
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
