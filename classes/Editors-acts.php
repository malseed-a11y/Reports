<?php

class editors_acts
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
