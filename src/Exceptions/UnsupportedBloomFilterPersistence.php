<?php

declare(strict_types=1);


namespace Denismitr\Bloom\Exceptions;


class UnsupportedBloomFilterPersistenceDriver extends InvalidBloomFilterConfiguration
{
    public static function type(string $type): self
    {
        return new static("Bloom filter persistence driver must be a string, but [{$type}] was given.");
    }

    public static function driver($driver): self
    {
        $driver = is_string($driver) ? $driver : gettype($driver);

        return new static("Bloom filter persistence driver [{$driver}] is not supported.");
    } 
}