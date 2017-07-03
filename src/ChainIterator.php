<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Chains multiple iterables together, and re-indexes the values.
 */
class ChainIterator implements Iterator
{
    use IteratorTrait;

    /**
     * @var \BartFeenstra\Functional\Iterator
     */
    private $currentIterator;
    private $index = 0;
    private $iterators = [];

    /**
     * Constructs a new instance.
     *
     * @param mixed[] ...$iterables
     *
     * @throws \BartFeenstra\Functional\InvalidIterable
     */
    public function __construct(...$iterables)
    {
        // Use an empty array as default iterable, to simplify the code in this class.
        $this->append(...$iterables ?: [[]]);
    }

    /**
     * Appends an iterable to the chain.
     *
     * @param mixed[] ...$iterables
     *
     * @return $this
     */
    public function append(...$iterables): self
    {
        foreach ($iterables as $iterable) {
            $this->iterators[] = iter($iterable);
        }
        return $this;
    }

    public function current()
    {
        return $this->getCurrentIterator()->current();
    }

    public function key()
    {
        return $this->valid() ? $this->index : null;
    }

    public function next()
    {
        $this->getCurrentIterator()->next();
        $this->index++;
    }

    public function rewind()
    {
        $this->currentIterator = reset($this->iterators);
        $this->getCurrentIterator()->rewind();
        $this->index = 0;
    }

    public function valid()
    {
        return $this->getCurrentIterator()->valid();
    }

    private function getCurrentIterator(): Iterator
    {
        // If the current iterator can still be used, use it.
        if ($this->currentIterator->valid()) {
            return $this->currentIterator;
        }

        // The current iterator is exhausted. Advance to the next one if it is available.
        $nextIterator = next($this->iterators);
        if ($nextIterator) {
            $this->currentIterator = $nextIterator;
        }

        // Return the last available iterator.
        return $this->currentIterator;
    }
}
