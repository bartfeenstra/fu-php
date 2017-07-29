<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Iterates over keys.
 */
final class KeyIterator extends IteratorIterator
{

    private $index = 0;

    public function current()
    {
        return parent::key();
    }

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
