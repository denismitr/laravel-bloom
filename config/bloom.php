<?php

return [
    'default' => [
        'size' => 10000000,
        'num_hashes' => 5,
        'persistence' => [
            'driver' => 'redis',
            'connection' => 'default'
        ],
        'hashing_algorithm' => 'md5',
    ],

    'keys' => [
         // keys specific params
         // example
//        'user_recommendations' => [
//            'size' => 550000,
//            'num_hashes' => 10,
//            'persistence' => [
//                'driver' => 'redis',
//                'connection' => 'default'
//            ],
//            'hashing_algorithm' => 'md5',
//        ]
    ],
];