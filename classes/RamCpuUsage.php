<?php

namespace SimpleReportsNamespace\classes;

if (!defined('ABSPATH')) {
    die('-1');
}

class RamCpuUsage
{



    /**
     * Outputs the CPU and RAM usage as JSON.
     */
    // is it a Windows OS

    protected function isWindows()
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }
    // output json
    public function output_json()
    {
        $cpu = $this->isWindows() ? $this->getCpuUsageWindows() : $this->getCpuUsageLinux();
        $ram = $this->isWindows() ? $this->getRamUsageWindows() : $this->get_linux_ram();

        header('Content-Type: application/json');

        echo json_encode([
            'cpu' => $cpu ?: 0.0,
            'ram' => $ram ?: 0.0,
        ]);

        // Terminates WordPress AJAX execution
        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_die();
        }
    }

    /**
     * Gets RAM usage on Linux by reading /proc/meminfo.
     */

    public function get_linux_ram()
    {
        if (!function_exists('shell_exec')) {
            return 0.0;
        }

        $output = @shell_exec('free -k 2>&1');
        if (empty($output)) {
            return 0.0;
        }

        if (preg_match('/^Mem:\s+(\d+)\s+(\d+)\s+(\d+)/m', $output, $matches)) {

            $memTotal = (float)$matches[1];
            $memUsed  = (float)$matches[2];

            if ($memTotal <= 0) {
                return 0.0;
            }
            $percent = ($memUsed / $memTotal) * 100;
            return round($percent, 2);
        }

        return 0.0;
    }
    /**
     * Gets CPU usage on Linux by reading /proc/stat twice.
     */
    protected function getCpuUsageLinux()
    {
        if (!function_exists('shell_exec')) {
            return 0.0;
        }

        $output = @shell_exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $8}' 2>&1");

        $idle_time_str = trim($output);
        if (empty($idle_time_str) || !is_numeric($idle_time_str)) {
            return 0.0;
        }

        $idle_percent = (float)$idle_time_str;

        $cpuUsage = 100.0 - $idle_percent;

        return round($cpuUsage, 2);
    }

    /**
     * Gets CPU usage on Windows using WMIC.
     */
    protected function getCpuUsageWindows()
    {
        if (!function_exists('shell_exec')) {
            return 0.0;
        }

        $output = @shell_exec('wmic cpu get LoadPercentage /Value 2>&1');

        if ($output === null || trim($output) === '') {
            return 0.0;
        }

        if (preg_match('/LoadPercentage=(\d+)/i', $output, $matches)) {
            return (float)$matches[1];
        }

        return 0.0;
    }

    /**
     * Gets RAM usage on Windows using WMIC.
     */
    protected function getRamUsageWindows()
    {
        if (!function_exists('shell_exec')) {
            return 0.0;
        }

        $output = @shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value 2>&1');

        if ($output === null || trim($output) === '') {
            return 0.0;
        }

        $free = 0.0; // kB
        $total = 0.0; // kB

        if (preg_match('/FreePhysicalMemory=(\d+)/i', $output, $m)) {
            $free = (float)$m[1];
        }

        if (preg_match('/TotalVisibleMemorySize=(\d+)/i', $output, $m2)) {
            $total = (float)$m2[1];
        }

        if ($total <= 0) {
            return 0.0;
        }

        $used = $total - $free;
        $percent = ($used / $total) * 100;

        return round($percent, 2);
    }
}
