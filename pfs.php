<?php
/*
Plugin Name: PFS
*/

foreach (glob(plugin_dir_path(__FILE__) . "src/*.php") as $file) {
    include_once $file;
}

new \Pfs\Setup(ABSPATH . 'wp-content/plugins/pfs');

function addPfsFilters($filters)
{
    $args = [
        'paged'      => true,
        'page'       => 119,
        'base_query' => [
            'post_type'      => 'post',
            'posts_per_page' => 10
        ],
        'groups'     => [
            'category'   => [
                'type'       => 'taxonomy',
                'hide_empty' => true,
            ],
            'post_tag'   => [
                'type' => 'taxonomy'
            ],
            'popularity' => [
                'type'   => 'meta',
                'values' => [0, 100]
            ]
        ]
    ];

    $filters[] = $args;

    return $filters;
}

add_filter('pfs_filters', 'addPfsFilters');
