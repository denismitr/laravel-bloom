<?php

// Bloom filter configuration file

return [
    'default' => [
        'size' => 16777216,
        'num_hashes' => 4,
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
//            'size' => 5500000,
//            'num_hashes' => 10,
//            'persistence' => [
//                'driver' => 'redis',
//                'connection' => 'default'
//            ],
//            'hashing_algorithm' => 'md5',
//        ]
    ],
];