<?php

namespace Denismitr\Bloom\Tests;

use Denismitr\Bloom\BloomServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;


abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            BloomServiceProvider::class
        ];
    }
}