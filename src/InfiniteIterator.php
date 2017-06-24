<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Iterates over an array until infinity and beyond.
 */
final class InfiniteIterator extends \InfiniteIterator implements Iterator
{
    use IteratorTrait;
}
