<?php

declare(strict_types=1);


namespace Denismitr\Bloom\Exceptions;


class InvalidBloomFilterSize extends InvalidBloomFilterConfiguration
{
    public static function size($size): self
    {
        return new static("Size must be a positive integer: value [{$size}] is invalid.");
    }

    public static function max(int $size, int $maxCapacity): self
    {
        return new static(
    "Size must not be greater than [{$maxCapacity}] for the given perister driver: value [{$size}] is too large."
        );
    }
}