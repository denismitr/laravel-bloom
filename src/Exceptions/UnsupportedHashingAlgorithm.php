<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Exceptions;


class UnsupportedHashingAlgorithm extends InvalidBloomFilterConfiguration
{
    /**
     * @param string $algorithm
     * @return UnsupportedHashingAlgorithm
     */
    public static function algorithm(string $algorithm): self
    {
        return new static("Unsupported hashing algorithm: {$algorithm}.");
    }

    /**
     * @param string $type
     * @return UnsupportedHashingAlgorithm
     */
    public static function type(string $type): self
    {
        return new static("Hashing algorithm must be specified as string, but [$type] was given");
    }
}