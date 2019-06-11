<?php

namespace Denismitr\Bloom\Facades;

use Denismitr\Bloom\BloomFilter;
use Illuminate\Support\Facades\Facade;

/**
 * Class Bloom
 * @package Denismitr\Bloom\Facades
 *
 * @method static BloomFilter key(string $key, ?string $keySuffix = null)
 */
class Bloom extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bloom.manager';
    }
}