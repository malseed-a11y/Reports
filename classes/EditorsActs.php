<?php

namespace Reports\classes;

if (!defined('ABSPATH')) die('-1');

class EditorsActs
{
    public function editors_activity()
    {
        $editors = get_users([
            'role' => 'editor',
            'orderby' => 'display_name',
            'order' => 'ASC',
        ]);

        $labels = [];
        $data = [];

        foreach ($editors as $editor) {
            $labels[] = $editor->display_name;
            $data[] = count_user_posts($editor->ID, 'post');
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
