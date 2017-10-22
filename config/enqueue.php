<?php

return [
    'styles'   => [
        'nouislider' => [
            'src' => '/assets/css/lib/nouislider.min.css',
        ],
    ],
    'scripts'  => [
        'nouislider' => [
            'src'       => '/assets/js/lib/nouislider.min.js',
            'deps'      => null,
            'ver'       => null,
            'in-footer' => true
        ],
        'scripts'    => [
            'src'       => '/assets/js/scripts.js',
            'deps'      => ['jquery'],
            'ver'       => null,
            'in-footer' => true
        ]
    ],
    'localize' => [
        'scripts' => [
            'prefix'  => 'pfs',
            'strings' => [
                'ajaxUrl' => admin_url('admin-ajax.php'),
            ]
        ]
    ]
];
