<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Iterates over another iterator.
 */
final class IteratorIterator extends \IteratorIterator implements Iterator
{

    use IteratorTrait;
}
