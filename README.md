## Laravel Bloom filter implementation
[![Build Status](https://travis-ci.org/denismitr/laravel-bloom.svg?branch=master)](https://travis-ci.org/denismitr/laravel-bloom.svg?branch=master)

A Bloom filter is a space-efficient probabilistic data structure, 
conceived by Burton Howard Bloom in 1970, 
that is used to test whether an element is a member of a set. 
False positive matches are possible, but false negatives are not – in other words, 
a query returns either "possibly in set" or "definitely not in set". 
Elements can be added to the set, 
but not removed (though this can be addressed with a "counting" filter); 
the more elements that are added to the set, the larger the probability of false positives. 

Read the [Full article on Wikipedia](https://en.wikipedia.org/wiki/Bloom_filter).

#### Under Construction

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


### Usage

##### Default configuration

```php
Bloom::key("shown-banners")->add($banner->id);
...
Bloom::key("shown-banners")->test($banner->id);
// true
Bloom::key("shown-banners")->test($unseenBanner->id);
// false, but can be true sometimes (a false positive)

// reset bloom filter for given key
Bloom::key('shown-banners')->reset();
```

#### Key specific configuration

in `bloom.php` find `keys` section and add a configuration for a **key** that you want
to use with parameters others than `default`.

```php
'keys' => [
    'seen-banners' => [
        'size' => 5550000,
        'num_hashes' => 3,
        'persistence' => [
            'driver' => 'redis',
            'connection' => 'default'
        ],
        'hashing_algorithm' => 'md5',
    ]    
]
```

now when you use Bloom filter with that key it will use that configuration. 
`Bloom::key('seen-banners')->add($bammer->id)`

#### Key suffix
For your convenience there is an easy way to generate user specific keys, just pass 
user ID as a second argument when calling the `key` method like so:
```php
Bloom::key('user-recommendation', $user->id)->add($recommendation->id);
```

or

```php
$bloomFilter = Bloom::key('user-recommendation', $user->id);

$bloomFilter->add($recommendation->id);

$bloomFilter->test($recommendation->id); // true

// to reset that user specific key do
$bloomFilter->reset();
```

### Testing

For now you need an actual Redis set up on your machine.