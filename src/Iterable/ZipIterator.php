<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Zips iterators into tuples.
 */
final class ZipIterator implements Iterator
{

    use IteratorTrait;

    private $key = 0;
    /** @var \BartFeenstra\Functional\Iterable\Iterator[] */
    private $iterators;

    /**
     * Constructs a new instance.
     *
     * @param mixed $one
     *   Any value taken by \BartFeenstra\Functional\iter().
     * @param mixed $two
     *   Any value taken by \BartFeenstra\Functional\iter().
     * @param mixed ...$more
     *   Any values taken by \BartFeenstra\Functional\iter().
     */
    public function __construct($one, $two, ...$more)
    {
        $this->iterators = array_map('\BartFeenstra\Functional\Iterable\iter', func_get_args());
    }

    public function current()
    {
        $zip = [];
        foreach ($this->iterators as $iterator) {
            $zip[] = $iterator->current();
        }
        return $zip;
    }

    public function key()
    {
        return $this->key;
    }

    public function rewind()
    {
        $this->key = 0;
        foreach ($this->iterators as $iterator) {
            $iterator->rewind();
        }
    }

    public function next()
    {
        $this->key++;
        foreach ($this->iterators as $iterator) {
            $iterator->next();
        }
    }

    public function valid()
    {
        foreach ($this->iterators as $iterator) {
            if (!$iterator->valid()) {
                return false;
            }
        }
        return true;
    }
}
