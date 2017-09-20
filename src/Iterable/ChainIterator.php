<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Chains multiple iterables together, and re-indexes the values.
 */
class ChainIterator implements Iterator
{
    use IteratorTrait;

    /**
     * @var \BartFeenstra\Functional\Iterable\Iterator
     */
    private $currentIterator;
    private $index = 0;
    private $iterators = [];

    /**
     * Constructs a new instance.
     *
     * @param mixed[] $iterables
     *   An array of any values taken by \BartFeenstra\Functional\iter().
     *
     * @throws \BartFeenstra\Functional\Iterable\InvalidIterable
     */
    // @todo What if we want to pass on an iterable of iterables?
    public function __construct(array $iterables)
    {
        // Use an empty array as default iterable, to simplify the code in this class.
        $this->append($iterables ?: [[]]);
    }

    /**
     * Appends an iterable to the chain.
     *
     * @param mixed[] $iterables
     *   An array of any values taken by \BartFeenstra\Functional\iter().
     *
     * @return $this
     */
    public function append(array $iterables): self
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
        $this->currentIterator->rewind();
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
        while (!$this->currentIterator->valid() and $nextIterator = next($this->iterators)) {
            $this->currentIterator = $nextIterator;
            $this->currentIterator->rewind();
        }

        // Return the last available iterator.
        return $this->currentIterator;
    }
}
