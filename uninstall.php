<?php


if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


// Delete custom database tables created by the plugin.

global $wpdb;
$table_logs              = $wpdb->prefix . 'report_table_logs';
$table_editor_activities = $wpdb->prefix . 'reports_editors_activity';

$wpdb->query("DROP TABLE IF EXISTS {$table_logs}");
$wpdb->query("DROP TABLE IF EXISTS {$table_editor_activities}");


// Delete all options created by the plugin.
delete_option('reports_logs_days');
delete_option('usage_interval');


// Clear the scheduled cron event
$hook_name = 'reports_cleanup_logs';
$timestamp = wp_next_scheduled($hook_name);

if ($timestamp) {
    wp_unschedule_event($timestamp, $hook_name);
}
