<?php

return [
    'default' => [
        'size' => 100000,
        'num_hashes' => 5,

        'implementation' => \Denismitr\Bloom\BloomRedisImpl::class,

        'hasher' => \Denismitr\Bloom\Helpers\HasherMD5Impl::class,
    ],

    'keys' => [],
];