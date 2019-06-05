<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Contracts;

interface Bloom
{
    public function add($item): void;

    public function test($item): bool;
}