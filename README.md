# Simple wordpress post filter system

### Usage:
1. Clone / download the plugin;
1. Activate it;
1. Hook in to the `pfs_navigation` filter and pass in the arguments. Check the example on how arguments are formated;
1. Refresh permalinks.

### Example:
```php
<?php

function addPfsInstance($filters)
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
                'meta'     => 'popularity',
                'values'   => [0, 100]
            ]
        ]
    ];

    $filters[] = $args;

    return $filters;
}

add_filter('pfs_navigation', 'addPfsInstance');
```
