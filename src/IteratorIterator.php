<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Converts any \Traversable to a universal iterator.
 */
class IteratorIterator extends \IteratorIterator implements Iterator
{
    use IteratorTrait;
}
