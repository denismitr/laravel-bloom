<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Contracts;


interface Hasher
{
    /**
     * @param int $seed
     * @param string $value
     * @return int
     */
    public function hash(int $seed, string $value): int;
}