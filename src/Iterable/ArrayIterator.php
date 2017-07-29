<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Iterates over an array.
 */
final class ArrayIterator extends \ArrayIterator implements Iterator
{

    use IteratorTrait;

    public function reverse(): Iterator
    {
        return new static(array_reverse($this->getArrayCopy()));
    }
}
