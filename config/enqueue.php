<?php

return [
    'styles'   => [
        'jquery-ui-slider' => [
            'src' => '/assets/css/lib/jquery-ui.min.css',
        ],
    ],
    'scripts'  => [
        'pfs-scripts' => [
            'src'       => '/assets/js/scripts.js',
            'deps'      => ['jquery', 'jquery-ui-slider'],
            'ver'       => null,
            'in-footer' => true
        ]
    ],
    'localize' => [
        'pfs-scripts' => [
            'prefix'  => 'pfs',
            'strings' => [
                'ajaxUrl' => admin_url('admin-ajax.php'),
            ]
        ]
    ]
];
