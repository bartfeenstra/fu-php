<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

use BartFeenstra\Functional\None;
use BartFeenstra\Functional\Option;
use BartFeenstra\Functional\SomeValue;
use function BartFeenstra\Functional\Predicate\truthy;

/**
 * Implements \BartFeenstra\Functional\Iterable\Iterator.
 */
trait IteratorTrait
{

    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function each(callable $operation): Iterator
    {
        foreach ($this as $key => $value) {
            $operation($value, $key);
        }
        return $this;
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
                throw new TerminateFold(new SomeItem($value, $key));
            }
            return $none;
        }, new None());
    }

    public function map(callable $conversion): Iterator
    {
        return new MapIterator($this, $conversion);
    }

    public function mapKeys(callable $conversion): Iterator
    {
        return new KeyMapIterator($this, $conversion);
    }

    public function reduce(callable $reduction): Option
    {
        try {
            $this->rewind();
            if (!$this->valid()) {
                return new None();
            }
            $carrier = $this->current();
            $this->next();
            while ($this->valid()) {
                $carrier = $reduction($carrier, $this->current(), $this->key());
                $this->next();
            }
            return new SomeValue($carrier);
        } catch (TerminateReduction $t) {
            return new SomeValue($t->getCarrier());
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

    public function min(): Option
    {
        return $this->reduce(function ($carrier, $value) {
            return $value < $carrier ? $value : $carrier;
        });
    }

    public function max(): Option
    {
        return $this->reduce(function ($carrier, $value) {
            return $value > $carrier ? $value : $carrier;
        });
    }

    public function sum(): Option
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

    public function list(): Iterator
    {
        return new IndexedIterator($this);
    }

    public function listKeys(): Iterator
    {
        return new KeyIterator($this);
    }

    public function flip(): Iterator
    {
        return new FlipIterator($this);
    }

    public function reverse(): Iterator
    {
        return new ArrayIterator(array_reverse($this->toArray()));
    }

    public function first(): Option
    {
        $this->rewind();
        if ($this->valid()) {
            return new SomeItem($this->current(), $this->key());
        }
        return new None;
    }

    public function last(): Option
    {
        $this->rewind();
        if (!$this->valid()) {
            return new None();
        }
        foreach ($this as $key => $value) {
            $lastValue = $value;
            $lastKey = $key;
        }
        return new SomeItem($lastValue, $lastKey);
    }

    public function empty(): bool
    {
        $this->rewind();
        return !$this->valid();
    }

    public function sort(callable $sort = null): Iterator
    {
        $array = $this->toArray();
        if ($sort) {
            uasort($array, $sort);
        } else {
            asort($array);
        }
        return new ArrayIterator($array);
    }

    public function sortKeys(callable $sort = null): Iterator
    {
        $array = $this->toArray();
        if ($sort) {
            uksort($array, $sort);
        } else {
            ksort($array);
        }
        return new ArrayIterator($array);
    }

    public function chain(...$iterables): Iterator
    {
        return new ChainIterator($this, ...$iterables);
    }

    public function merge(...$iterables): Iterator
    {
        return new ArrayIterator(array_merge(...
            array_map('\BartFeenstra\Functional\Iterable\ensure_array', array_merge([$this], $iterables))));
    }

    public function flatten(int $levels = 1): Iterator
    {
        $iterator = $this;
        do {
            $iterator = new ChainIterator(...$iterator->list());
            $levels--;
        } while ($levels);
        return $iterator;
    }

    public function unique(): Iterator
    {
        return new UniqueIterator($this);
    }
}
