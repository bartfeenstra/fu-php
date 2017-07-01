<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Iterates over an array.
 */
final class ArrayIterator implements Iterator
{

    use IteratorTrait;

    private $array;

  /**
   * Constructs a new instance.
   *
   * @param array $array
   *   The array to iterate over.
   */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function current()
    {
        return current($this->array);
    }

    public function key()
    {
        return key($this->array);
    }

    public function next()
    {
        next($this->array);
    }

    public function rewind()
    {
        reset($this->array);
    }

    public function valid()
    {
        return array_key_exists($this->key(), $this->array);
    }

    public function reverse(): Iterator
    {
        return new static(array_reverse($this->array));
    }
}
