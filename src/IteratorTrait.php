<?php

declare(strict_types=1);

namespace BartFeenstra\Functional;

/**
 * Implements \BartFeenstra\Functional\Iterator.
 */
trait IteratorTrait
{

    public function each(callable $operation): void
    {
        foreach ($this as $key => $value) {
            $operation($value, $key);
        }
    }

    public function filter(callable $predicate = null): Iterator
    {
        return new FilterIterator($this, $predicate);
    }

    public function find(callable $predicate = null): Option
    {
        $predicate = $predicate ?: truthy();
        return $this->fold(function ($none, $value, $key) use ($predicate): Option {
            if ($predicate($value, $key)) {
                throw new TerminateFold(new SomeValue($value));
            }
            return $none;
        }, new None());
    }

    public function map(callable $conversion): Iterator
    {
        return new MapIterator($this, $conversion);
    }

    public function reduce(callable $reduction)
    {
        try {
            $this->rewind();
            $carrier = $this->current();
            $this->next();
            while ($this->valid()) {
                $carrier = $reduction($carrier, $this->current(), $this->key());
                $this->next();
            }
            return $carrier;
        } catch (TerminateReduction $t) {
            return $t->getCarrier();
        }
    }

    public function fold(callable $fold, $initial_carrier)
    {
        $carrier = $initial_carrier;
        try {
            foreach ($this as $key => $value) {
                $carrier = $fold($carrier, $value, $key);
            }
            return $carrier;
        } catch (TerminateFold $t) {
            return $t->getCarrier();
        }
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

    public function slice(int $start, int $length = null): Iterator
    {
        if (is_null($length)) {
            $length = -1;
        }
        return new LimitIterator($this, $start, $length);
    }

    public function min()
    {
        return $this->reduce(function ($carrier, $value) {
            return $value < $carrier ? $value : $carrier;
        });
    }

    public function max()
    {
        return $this->reduce(function ($carrier, $value) {
            return $value > $carrier ? $value : $carrier;
        });
    }

    public function sum()
    {
        return $this->reduce(function ($carrier, $value) {
            return $value + $carrier;
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

    public function keys(): Iterator
    {
        return new KeyIterator($this);
    }

    public function indexed(): Iterator
    {
        return new IndexedIterator($this);
    }

    public function flip(): Iterator
    {
        return new FlipIterator($this);
    }

    public function first(): Option
    {
        $this->rewind();
        if ($this->valid()) {
            return new SomeValue($this->current());
        }
        return new None;
    }

    public function last(): Option
    {
        $this->rewind();
        if (!$this->valid()) {
            return new None();
        }
        $last;
        foreach ($this as $value) {
            $last = $value;
        }
        return new SomeValue($last);
    }

    public function empty(): bool
    {
        $this->rewind();
        return !$this->valid();
    }
    public function sort(callable $sort = null): Iterator
    {
        $array = iterator_to_array($this);
        if ($sort) {
            uasort($array, $sort);
        } else {
            asort($array);
        }
        return new ArrayIterator($array);
    }

    public function sortKeys(callable $sort = null): Iterator
    {
        $array = iterator_to_array($this);
        if ($sort) {
            uksort($array, $sort);
        } else {
            ksort($array);
        }
        return new ArrayIterator($array);
    }
}
