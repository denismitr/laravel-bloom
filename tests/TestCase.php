<?php

namespace Denismitr\Bloom\Tests;

use Denismitr\Bloom\BloomServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;


abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('bloom.default', [
            'size' => 10000000,
            'num_hashes' => 5,
            'persistence' => 'redis',
            'hashing_algorithm' => 'md5',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            BloomServiceProvider::class
        ];
    }
}