<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Takes all values up to but not including the first one for which the predicate evaluates to FALSE.
 */
final class TakeWhileIterator extends \FilterIterator implements Iterator
{

    use IteratorTrait;

    private $predicate;
    private $while = true;

  /**
   * Constructs a new instance.
   *
   * @param \Iterator $iterator
   *   The iterator to iterate over.
   * @param callable $predicate
   *   Signature: function(mixed $value, mixed $key): bool.
   */
    public function __construct(\Iterator $iterator, callable $predicate)
    {
        parent::__construct($iterator);
        $this->predicate = $predicate;
    }

    public function accept()
    {
        $accept = call_user_func($this->predicate, $this->current(), $this->key());
        if (!$accept) {
            $this->while = false;
        }
        return $accept;
    }

    public function valid()
    {
        return $this->while && parent::valid();
    }

    public function rewind()
    {
        $this->while = true;
        parent::rewind();
    }
}
