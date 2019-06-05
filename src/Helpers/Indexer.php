<?php


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
        $index = $this->hasher->hash($seed, $value);

        return $index % $size;
    }
}