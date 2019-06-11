## Laravel Bloom

#### Under Construction

### Usage
```php
Bloom::key("shown-banners")->add($banner->id);
...
Bloom::key("shown-banners")->test($banner->id);
// true
Bloom::key("shown-banners")->test($unseenBanner->id);
// false (but can be a false positive)
```

### Configuration

Bloom filter configuration file: `bloom.php`

```php
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
```

`default` section applies to all keys, unless they have a dedicated configuration,
specified inside the `keys` section.

- `size` - is the size of the Bloom filter storage 10M-100M bit is recommended size
- `num_hashes` - is a number of hash functions applied to each item put into Bloom filter
- `persistance` - is an array containing persistence configuration - `driver` and `connection`
    - `driver` - at the moment only **redis** is supported
    - `connection` - for redis connection is specified in `redis` section of the `database.php` configuration file in standard Laravel setup.
- `hashing_algorithm` - self explanatory, at the moment only `md5` is supported. 