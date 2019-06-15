<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Exceptions;


class UnsupportedHashingAlgorithm extends InvalidBloomFilterConfiguration
{
    /**
     * @param string $algorithm
     * @return UnsupportedHashingAlgorithm
     */
    public static function algorithm($algorithm): self
    {
        if ( ! is_string($algorithm) ) {
            $type = gettype($algorithm);

            return new static("Hashing algorithm must be specified as string, but [$type] was given");
        }

        return new static("Unsupported hashing algorithm: {$algorithm}.");
    }
}