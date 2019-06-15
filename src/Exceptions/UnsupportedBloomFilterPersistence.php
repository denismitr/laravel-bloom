<?php

declare(strict_types=1);


namespace Denismitr\Bloom\Exceptions;


class UnsupportedBloomFilterPersistence extends InvalidBloomFilterConfiguration
{
    public static function driver($driver): self
    {
        if ( ! is_string($driver) ) {
            $type = gettype($driver);

            return new static("Bloom filter persistence driver must be a string, but [{$type}] was given.");
        }

        return new static("Bloom filter persistence driver [{$driver}] is not supported.");
    }

    public static function connection($connection): self
    {
        if ( ! is_string($connection) ) {
            $type = gettype($connection);

            return new static(
                "Bloom filter persistence connection must be a string, but [{$type}] was given."
            );
        }

        return new static("Bloom filter persistence connection [{$connection}] is not supported.");
    }
}