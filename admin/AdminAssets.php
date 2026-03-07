<?php

namespace WPAdminPostsExtended\Admin;

class AdminAssets
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        wp_enqueue_style(
            'select2',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css'
        );

        wp_enqueue_script(
            'select2',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js',
            ['jquery'],
            null,
            true
        );

        wp_add_inline_script(
            'select2',
            "jQuery(function($){
                $('#filter-by-tag').select2({
                    placeholder: 'Select tags',
                    allowClear: true,
                    width: '250px'
                });
            });"
        );
    }
}