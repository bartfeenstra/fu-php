<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Converts any \Traversable to a universal iterator.
 */
class IteratorIterator extends \IteratorIterator implements Iterator
{
    use IteratorTrait;
}
