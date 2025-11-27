<?php

namespace SimpleReports\classes;

if (!defined('ABSPATH')) {
    die('-1');
}

class RamCpuUsage
{
    public function get_current_ram()
    {
        if ($this->isWindows()) {
            return $this->getRamUsageWindows();
        }

        $data = @file('/proc/meminfo');
        if ($data === false) {
            return 0;
        }

        $memTotal = 0;
        $memAvailable = 0;

        foreach ($data as $line) {
            if (strpos($line, 'MemTotal:') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $memTotal = (int)($parts[1] ?? 0); // kB
            } elseif (strpos($line, 'MemAvailable:') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $memAvailable = (int)($parts[1] ?? 0); // kB
            }

            if ($memTotal > 0 && $memAvailable > 0) {
                break;
            }
        }

        if ($memTotal <= 0) {
            return 0;
        }

        $used = $memTotal - $memAvailable;
        $percent = ($used / $memTotal) * 100;

        return round($percent, 2);
    }

    public function get_current_cpu()
    {
        if ($this->isWindows()) {
            return $this->getCpuUsageWindows();
        }

        return $this->getCpuUsageLinux();
    }

    public function output_json()
    {
        $cpu = $this->get_current_cpu();
        $ram = $this->get_current_ram();


        echo json_encode([
            'cpu' => $cpu ?: 0,
            'ram' => $ram ?: 0,
        ]);
    }

    // =========================
    // Helpers
    // =========================

    protected function isWindows()
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }

    protected function getCpuUsageLinux()
    {
        $stat1 = @file('/proc/stat');
        if ($stat1 === false || !isset($stat1[0])) {
            return 0;
        }

        $cpu1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0]));
        $total1 = array_sum($cpu1);
        $idle1  = $cpu1[3] ?? 0;

        usleep(100000);

        $stat2 = @file('/proc/stat');
        if ($stat2 === false || !isset($stat2[0])) {
            return 0;
        }

        $cpu2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0]));
        $total2 = array_sum($cpu2);
        $idle2  = $cpu2[3] ?? 0;

        $totalDiff = $total2 - $total1;
        $idleDiff  = $idle2 - $idle1;

        if ($totalDiff <= 0) {
            return 0;
        }

        $cpuUsage = (1 - ($idleDiff / $totalDiff)) * 100;
        return round($cpuUsage, 2);
    }

    protected function getCpuUsageWindows()
    {
        if (!function_exists('shell_exec')) {
            return 0;
        }

        $output = @shell_exec('wmic cpu get LoadPercentage /Value 2>&1');
        if ($output === null || trim($output) === '') {
            return 0;
        }

        if (preg_match('/LoadPercentage=(\d+)/i', $output, $matches)) {
            return (float)$matches[1];
        }

        return 0;
    }

    protected function getRamUsageWindows()
    {
        if (!function_exists('shell_exec')) {
            return 0;
        }

        $output = @shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value 2>&1');
        if ($output === null || trim($output) === '') {
            return 0;
        }

        $free = 0;
        $total = 0;

        if (preg_match('/FreePhysicalMemory=(\d+)/i', $output, $m)) {
            $free = (float)$m[1];
        }

        if (preg_match('/TotalVisibleMemorySize=(\d+)/i', $output, $m2)) {
            $total = (float)$m2[1];
        }
        if ($total <= 0) {
            return 0;
        }

        $used = $total - $free;
        $percent = ($used / $total) * 100;

        return round($percent, 2);
    }
}
