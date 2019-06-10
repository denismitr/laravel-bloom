<?php

return [
    'default' => [
        'size' => 10000000,
        'num_hashes' => 5,
        'persistence' => 'redis',
        'hashing_algorithm' => 'md5',
    ],

    'keys' => [
        // keys specific params
        'user_recommendations' => [
            'size' => 550000,
            'num_hashes' => 10,
            'persistence' => 'redis',
            'hashing_algorithm' => 'md5',
        ]
    ],
];