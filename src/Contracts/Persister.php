<?php


namespace Denismitr\Bloom\Contracts;


interface Persister
{
    public function setBit(string $key, int $index, bool $value): void;

    public function getBit(string $key, int $index): bool;
}