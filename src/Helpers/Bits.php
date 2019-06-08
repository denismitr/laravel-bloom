<?php


namespace Denismitr\Bloom\Helpers;


class Bits
{
    /**
     * @var array
     */
    private $values;

    /**
     * Values constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function test(): bool
    {
        if ( empty($this->values) ) {
            return false;
        }

        foreach ($this->values as $value) {
            if ( ! $value) {
                return false;
            }
        }

        return true;
    }
}