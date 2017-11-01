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
                'values'   => [0, 100]
            ]
        ]
    ];

    $filters[] = $args;

    return $filters;
}

add_filter('pfs_navigation', 'addPfsInstance');
```

#### Example on how to add event listeners when filters are updating

```js
$('[data-pfs-navigation]').on('update_start', function () {
    console.log('start');
});

$('[data-pfs-navigation]').on('update_done', function () {
    console.log('done');
});
```

If you need to edit any of the template files you can copy them into a directory within your theme named /pfs, keeping the same file structure but removing the /views/ subdirectory.

**Example:** To override the checkbox template, copy: **wp-content/plugins/pfs/views/types/checkbox.php** to **wp-ontent/themes/yourtheme/pfs/types/checkbox.php**

