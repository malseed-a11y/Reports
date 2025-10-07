<?php

require_once plugin_dir_path(__FILE__) . '/DB-class.php';

class ram_cpu_usage
{
    public $ram;
    public $cpu;
    public $db;

    public function __construct()
    {
        $this->db = new db_manegar();
    }

    public function get_current_ram()
    {
        if (function_exists('memory_get_usage')) {
            $this->ram = round(memory_get_usage(true) / (1024 * 1024), 0);
        }
        $rame = $this->ram;
        return $rame;
    }

    public function get_current_cpu()
    {
        if (function_exists('getrusage')) {
            $this->cpu = getrusage()["ru_utime.tv_sec"] + getrusage()["ru_stime.tv_sec"];
        }
        return $this->cpu;
    }
    public function save_usage()
    {
        $ram_data = [
            'name_usage' => 'RAM',
            'value_usage' => $this->get_current_ram(),
        ];
        $cpu_data = [
            'name_usage' => 'CPU',
            'value_usage' => $this->get_current_cpu(),
        ];

        return $this->db->insert_usage_data($ram_data) && $this->db->insert_usage_data($cpu_data);
    }
}
