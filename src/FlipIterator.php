<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Swaps keys and values.
 */
final class FlipIterator extends \IteratorIterator implements Iterator
{

    use IteratorTrait;

    public function current()
    {
        return parent::key();
    }

    public function key()
    {
        return parent::current();
    }
}
