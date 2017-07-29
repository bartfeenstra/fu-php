<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Converts all keys to integers, starting from 0.
 */
class IndexedIterator extends \IteratorIterator implements Iterator
{

    use IteratorTrait;

    private $index = 0;

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        parent::next();
        $this->index++;
    }

    public function rewind()
    {
        parent::rewind();
        $this->index = 0;
    }
}
