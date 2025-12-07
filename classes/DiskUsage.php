<?php

namespace SimpleReportsNamespace\classes;

if (!defined('ABSPATH')) die('-1');

use RecursiveIteratorIterator;

class DiskUsage
{

    //============================
    // Check if it windows or linux
    private function get_dir_size($dir)
    {
        //============================
        // Validate directory
        if (!is_dir($dir) || !is_readable($dir)) {
            return 0;
        }
        //============================

        if (stripos(PHP_OS, 'WIN') === 0) {
            return $this->get_dir_size_windows($dir);
        } else {
            return $this->get_dir_size_linux($dir);
        }
    }
    //============================


    //============================
    // Get dir size in windows
    private function get_dir_size_windows($dir)
    {
        $size = 0;

        //============================
        // make a flat list of all the files inside the directory
        //1) Create a directory iterator to read all files and folders in $dir
        $directoryIterator = new \RecursiveDirectoryIterator(
            $dir,
            \FilesystemIterator::SKIP_DOTS
        );
        // 2) Wrap the directory iterator in a recursive iterator to traverse subdirectories
        $iterator = new \RecursiveIteratorIterator(
            $directoryIterator
        );
        //============================

        //============================
        // loop through the files
        foreach ($iterator as $file) {

            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        //============================

        return $size;
    }
    //============================






    //============================
    //Get dir size in linux
    private function get_dir_size_linux($dir)
    {
        //============================
        // validate directory
        if (!function_exists('shell_exec')) {
            return 0;
        }

        $safe_dir = escapeshellarg($dir);
        //============================

        //============================
        // get dir size and return it in bytes
        $output = shell_exec("du -sk " . $safe_dir);

        if (empty($output)) {
            return 0;
        }

        $size_kb = (float)explode("\t", $output)[0];

        return $size_kb * 1024;
    }
    //============================



    public function get_main_folders_report()
    {

        //============================
        // Get sizes of main WP folders
        $report = [];
        $report['root'] = $this->get_dir_size(ABSPATH);

        // wp-content
        if (defined('WP_CONTENT_DIR')) {
            $report['wp_content'] = $this->get_dir_size(WP_CONTENT_DIR);

            // themes
            $themes_dir = WP_CONTENT_DIR . '/themes';
            $report['themes'] = $this->get_dir_size($themes_dir);

            // plugins
            $plugins_dir = WP_CONTENT_DIR . '/plugins';
            $report['plugins'] = $this->get_dir_size($plugins_dir);

            // uploads
            $uploads_dir = WP_CONTENT_DIR . '/uploads';
            $report['uploads'] = $this->get_dir_size($uploads_dir);
        }
        // wp-admin
        if (defined('ABSPATH')) {
            $admin_dir = ABSPATH . 'wp-admin';
            $report['wp_admin'] = $this->get_dir_size($admin_dir);
        }

        $report['disk_total'] = disk_total_space(ABSPATH);
        $report['disk_free']  = disk_free_space(ABSPATH);
        //============================

        return $report;
    }
}
