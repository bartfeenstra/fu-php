<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines a filterable iterator.
 *
 * This does not extend \CallbackFilterIterator, because that passes the predicate too many arguments.
 */
final class FilterIterator extends \FilterIterator implements Iterator
{

    use IteratorTrait;

    private $predicate;

  /**
   * Constructs a new instance.
   *
   * @param \Iterator $iterator
   *   The iterator to iterate over.
   * @param callable $predicate
   *   Signature: function($item): bool.
   */
    public function __construct(\Iterator $iterator, callable $predicate)
    {
        parent::__construct($iterator);
        $this->predicate = $predicate;
    }

    public function accept()
    {
        return call_user_func($this->predicate, parent::current());
    }
}
