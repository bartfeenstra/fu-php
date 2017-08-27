<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional\Iterable;

use function BartFeenstra\Functional\Predicate\truthy;

/**
 * Removes all duplicate items.
 */
final class UniqueIterator extends \FilterIterator implements Iterator
{

    use IteratorTrait;

    private $seen = [];

  /**
   * Constructs a new instance.
   *
   * @param \Iterator $iterator
   *   The iterator to iterate over.
   * @param callable $predicate
   *   Signature: function(mixed $value, mixed $key): bool. Defaults to NULL for \BartFeenstra\Functional\truthy().
   */
    public function __construct(\Iterator $iterator, callable $predicate = null)
    {
        parent::__construct($iterator);
        $this->predicate = $predicate ?: truthy();
    }

    public function accept()
    {
        return !in_array($this->current(), $this->seen, true);
    }

    public function next()
    {
        $this->seen[] = $this->current();
        parent::next();
    }
}
