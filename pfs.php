<?php
/**
 * Plugin Name: PFS
 * Description: Simple wordpress post filter system
 * Version: 1.0.0
 * Author: Paulius Krutkis
 * Author URI: https://github.com/PauliusKrutkis
 * License: GPLv2
 */

foreach (glob(plugin_dir_path(__FILE__) . "src/*.php") as $file) {
    include_once $file;
}

new \Pfs\Setup(ABSPATH . 'wp-content/plugins/pfs');

function addPfsInstance($filters)
{
    $args = [
        'paged'   => true,
        'page'    => 119,
        'ajax'    => true,
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
                'meta'     => 'popularity',
                'dynamic'  => true,
            ],
            'status'     => [
                'title'    => 'Status',
                'template' => 'checkbox',
                'type'     => 'meta',
                'meta'     => 'status',
                'values'   => [
                    'Available' => 0,
                    'Sold'      => 1,
                ]
            ]
        ]
    ];

    $filters[] = $args;

    return $filters;
}

add_filter('pfs_navigation', 'addPfsInstance');