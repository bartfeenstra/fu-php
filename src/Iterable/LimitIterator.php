<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Iterates over a subset of another iterator.
 */
final class LimitIterator extends \LimitIterator implements Iterator
{

    use IteratorTrait;
}
