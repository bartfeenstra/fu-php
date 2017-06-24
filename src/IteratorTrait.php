<?php

declare(strict_types=1);

namespace BartFeenstra\Functional;

/**
 * Implements \BartFeenstra\Functional\Iterator.
 */
trait IteratorTrait
{

    public function each(callable $operation)
    {
        foreach ($this as $item) {
            $operation($item);
        }
    }

    public function filter(callable $predicate = null): Iterator
    {
        return new FilterIterator($this, $predicate);
    }

    public function map(callable $conversion): Iterator
    {
        return new MapIterator($this, $conversion);
    }

    public function reduce(callable $reduction)
    {
        $this->rewind();
        $carrier = $this->current();
        $this->next();
        while ($this->valid()) {
            $carrier = $reduction($carrier, $this->current());
            $this->next();
        }
        return $carrier;
    }

    public function fold(callable $fold, $initial_carrier)
    {
        $carrier = $initial_carrier;
        foreach ($this as $item) {
            $carrier = $fold($carrier, $item);
        }
        return $carrier;
    }

    public function count(): int
    {
        return $this->fold(function (int $count): int {
            return $count + 1;
        }, 0);
    }

    public function take(int $length): Iterator
    {
        return $this->slice(0, $length);
    }

    public function takeWhile(callable $predicate): Iterator
    {
        return new TakeWhileIterator($this, $predicate);
    }

    public function slice(int $start, int $length): Iterator
    {
        return new LimitIterator($this, $start, $length);
    }

    public function min()
    {
        return $this->reduce(function ($carrier, $item) {
            return $item < $carrier ? $item : $carrier;
        });
    }

    public function max()
    {
        return $this->reduce(function ($carrier, $item) {
            return $item > $carrier ? $item : $carrier;
        });
    }

    public function sum()
    {
        return $this->reduce(function ($carrier, $item) {
            return $item + $carrier;
        });
    }

    public function forever(): Iterator
    {
        return new InfiniteIterator($this);
    }

    public function zip($other, ...$others): Iterator
    {
        return new ZipIterator($this, ...func_get_args());
    }
}
