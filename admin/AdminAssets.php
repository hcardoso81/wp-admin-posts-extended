<?php

namespace WPAdminPostsExtended\Admin;

class AdminAssets
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue($hook): void
    {
        if ($hook !== 'edit.php') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Styles
        |--------------------------------------------------------------------------
        */

        // Select2 CSS
        wp_register_style(
            'wpape-select2',
            'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css',
            [],
            '4.0.13'
        );

        // Admin custom CSS
        wp_register_style(
            'wpape-admin',
            plugin_dir_url(__FILE__) . 'assets/css/admin.css',
            ['wpape-select2'],
            '1.0'
        );

        wp_enqueue_style('wpape-select2');
        wp_enqueue_style('wpape-admin');


        /*
        |--------------------------------------------------------------------------
        | Scripts
        |--------------------------------------------------------------------------
        */

        wp_register_script(
            'wpape-select2',
            'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js',
            ['jquery'],
            '4.0.13',
            true
        );

        wp_enqueue_script('wpape-select2');


        /*
        |--------------------------------------------------------------------------
        | Select2 Init
        |--------------------------------------------------------------------------
        */

        wp_add_inline_script(
            'wpape-select2',
            "
            jQuery(document).ready(function($){

                if (typeof $.fn.select2 === 'undefined') {
                    console.error('Select2 not loaded');
                    return;
                }

                const exportButton = $('#wpape-export-posts');
                const filterButton = $('#post-query-submit');

                if (exportButton.length && filterButton.length) {
                    exportButton.insertAfter(filterButton);
                }

                const el = $('#filter-by-tag');

                function formatTag(option){

                    if (!option.id){
                        return option.text;
                    }

                    const state = $(
                        '<span style=\"display:flex;align-items:center;gap:6px;\">' +
                        '<input type=\"checkbox\" ' +
                        (option.selected ? 'checked' : '') +
                        ' /> ' +
                        option.text +
                        '</span>'
                    );

                    return state;
                }

                if (el.length) {
                    el.select2({
                        placeholder: 'Filtrar por etiquetas',
                        allowClear: true,
                        closeOnSelect: false,
                        width: '260px',
                        templateResult: formatTag,
                        templateSelection: function(option){
                            return option.text;
                        }
                    });
                }

            });
            ",
            'after'
        );
    }
}
