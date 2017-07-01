<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines a mapping iterator.
 */
final class MapIterator extends IteratorIterator
{

    private $map;

  /**
   * Constructs a new instance.
   *
   * @param \Iterator $iterator
   *   The iterator to iterate over.
   * @param callable $map
   *   Signature: function(mixed $value, mixed $key): bool.
   */
    public function __construct(\Iterator $iterator, callable  $map)
    {
        parent::__construct($iterator);
        $this->map = $map;
    }

    public function current()
    {
        return call_user_func($this->map, parent::current(), $this->key());
    }
}
