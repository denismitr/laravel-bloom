<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Exceptions;


class InvalidBloomFilterHashFunctionsNumber extends InvalidBloomFilterConfiguration
{
    public static function number($number): self
    {
        return new static("Number of hash functions must be a positive integer: value [{$number}] is invalid.");
    }
}