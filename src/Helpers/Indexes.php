<?php


namespace Denismitr\Bloom\Helpers;

use Countable;

class Indexes implements Countable
{
    /**
     * @var array
     */
    private $indexes;

    /**
     * Multi constructor.
     * @param array $indexes
     */
    public function __construct(array $indexes = [])
    {
        $this->indexes = $indexes;
    }

    /**
     * @param int $index
     */
    public function push(int $index): void
    {
        $this->indexes[] = $index;
    }

    public function get(): iterable
    {
        foreach ($this->indexes as $index) {
            yield $index;
        }
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->indexes);
    }
}