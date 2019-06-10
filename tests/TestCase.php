<?php

namespace Denismitr\Bloom\Tests;

use Denismitr\Bloom\BloomServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;


abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $config = require __DIR__ . './../config/bloom.php';

        config()->set('bloom', $config);
    }

    protected function getPackageProviders($app)
    {
        return [
            BloomServiceProvider::class
        ];
    }
}