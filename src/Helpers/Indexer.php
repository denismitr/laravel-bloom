<?php

declare(strict_types=1);

namespace Denismitr\Bloom\Helpers;


use Denismitr\Bloom\Contracts\Hasher;
use Illuminate\Support\Collection;

class Indexer
{
    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Indexer constructor.s
     * @param Hasher $hasher
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param int $numHashes
     * @param string $value
     * @param int $size
     * @return Collection
     */
    public function getIndexes(int $numHashes, string $value, int $size): Collection
    {
        $indexes = collect([]);

        for ($i = 1; $i <= $numHashes; $i++) {
            $indexes->push(
                $this->getIndex($i, $value, $size)
            );
        }

        return $indexes;
    }

    /**
     * @param int $seed
     * @param string $value
     * @param int $size
     * @return int
     */
    private function getIndex(int $seed, string $value, int $size): int
    {
        $hash = $this->hasher->hash($seed, $value);

        // Strip the two's complement negative bit
        $bitIndex = $hash & (-1 >> 1);

        // If the result has a 1 as its leading bit,
        // multiply our index by 2 to compensate.
        if (($hash >> 31) === 1) {
            $bitIndex *= 2;
        }

        return $bitIndex % $size;
    }
}