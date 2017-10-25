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
        'paged'   => true,
        'page'    => 119,
        'query'   => [
            'post_type'      => 'post',
            'posts_per_page' => 10
        ],
        'filters' => [
            'category'   => [
                'title'    => 'Category',
                'type'     => 'taxonomy',
                'template' => 'checkbox',
                'taxonomy' => 'category'
            ],
            'post_tag'   => [
                'title'    => 'Tags',
                'type'     => 'taxonomy',
                'template' => 'checkbox',
                'taxonomy' => 'post_tag'
            ],
            'popularity' => [
                'title'    => 'Popularity',
                'template' => 'range',
                'type'     => 'meta',
                'values'   => [0, 100]
            ]
        ]
    ];

    $filters[] = $args;

    return $filters;
}

add_filter('pfs_navigation', 'addPfsFilters');
