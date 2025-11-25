<?php

namespace Reports\classes;


if (!defined('ABSPATH')) die('-1');

class DiskUsage
{
    public function get_full_size()
    {
        return disk_total_space("/");
    }
    public function get_free_size()
    {
        return disk_free_space("/");
    }
}
