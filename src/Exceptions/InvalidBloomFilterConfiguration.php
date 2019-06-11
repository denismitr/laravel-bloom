<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Exceptions;


class InvalidBloomFilterConfiguration extends BloomServiceException
{
    public static function because(string $message): self
    {
        return new static($message);
    }
}