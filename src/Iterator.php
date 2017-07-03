<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines an iterator.
 */
interface Iterator extends \Iterator, \Countable
{

  /**
   * Performs an operation for each value.
   *
   * @param callable $operation
   *   Signature: function(mixed $value, mixed $key): void.
   */
    public function each(callable $operation): void;

  /**
   * Filters values using a predicate.
   *
   * @param callable|null $predicate
   *   Signature: function(mixed $value, mixed $key): bool. Defaults to NULL for \BartFeenstra\Functional\truthy().
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function filter(callable $predicate = null): self;

  /**
   * Tries to find a single value.
   *
   * @param callable|null $predicate
   *   Signature: function(mixed $value, mixed $key): bool. Defaults to NULL for \BartFeenstra\Functional\truthy().
   *
   * @return Option
   *   Returns an Ok with the value, if found.
   */
    public function find(callable $predicate = null): Option;

  /**
   * Maps/converts values.
   *
   * @param callable $conversion
   *   Signature: function(mixed $value, mixed $key): mixed.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function map(callable $conversion): self;

    /**
     * Maps/converts keys.
     *
     * @param callable $conversion
     *   Signature: function(mixed $value, mixed $key): mixed.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function mapKeys(callable $conversion): self;

  /**
   * Reduces values to a single value.
   *
   * @param callable $reduction
   *   Signature: function(mixed $carrier, mixed $value, mixed $key): mixed. The parameter and return types are
   *   identical.
   *
   * @return \BartFeenstra\Functional\Option
   *   A Some with a value of the same type as that of the values, or None.
   */
    public function reduce(callable $reduction): Option;

  /**
   * Folds values to a single value.
   *
   * @param callable $fold
   *   Signature: function(mixed $carrier, mixed $value, mixed $key): mixed. The return type is the same as that of the
   *   carrier.
   * @param mixed $initial_carrier
   *
   * @return mixed
   *   The type is the same as that of the initial carrier.
   */
    public function fold(callable $fold, $initial_carrier);

  /**
   * Takes only the first n values.
   *
   * @param int $length
   *   The number of values to take.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function take(int $length): self;

  /**
   * Takes all values up to but not including the first one for which the predicate evaluates to FALSE.
   *
   * @param callable $predicate
   *   Signature: function(mixed $value, mixed $key): bool.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function takeWhile(callable $predicate): self;

  /**
   * Takes a slice out of the values.
   *
   * @param int $start
   *   The index of the first value to take. Indexes start at 0.
   * @param int|null $length
   *   The number of values to take, or NULL to create an infinite slice.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function slice(int $start, int $length = null): self;

  /**
   * Gets the value with the lowest value.
   *
   * @return \BartFeenstra\Functional\Option
   *   A Some with a value of the same type as that of the values, or None.
   */
    public function min();

  /**
   * Gets the value with the highest value.
   *
   * @return \BartFeenstra\Functional\Option
   *   A Some with a value of the same type as that of the values, or None.
   */
    public function max();

  /**
   * Gets the sum of all the values.
   *
   * @return \BartFeenstra\Functional\Option
   *   A Some with a value of the same type as that of the values, or None.
   */
    public function sum();

  /**
   * Repeats all values forever.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function forever(): self;

    /**
     * Zips each value into a tuple with corresponding values from each of the other traversables.
     *
     * @param mixed $other
     *   Any value taken by \BartFeenstra\Functional\iter().
     * @param mixed ...$others
     *   Any values taken by \BartFeenstra\Functional\iter().
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function zip($other, ...$others): self;

    /**
     * Returns the keys as indexed values.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function keys(): self;

    /**
     * Converts all keys to integers, starting from 0.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function indexed(): self;

    /**
     * Swaps keys and values.
     *
     * Not all types are valid keys. Refer to the \Iterator documentation on php.net for more information.
     * @see http://php.net/manual/en/class.iterator.php
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function flip(): self;

    /**
     * Reverses the values.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function reverse(): Iterator;

    /**
     * Gets the first value.
     *
     * @return \BartFeenstra\Functional\Option
     *   An Ok with the value, or None.
     */
    public function first(): Option;

    /**
     * Gets the last value.
     *
     * @return \BartFeenstra\Functional\Option
     *   An Ok with the value, or None.
     */
    public function last(): Option;

    /**
     * Checks if the iterator is empty.
     *
     * @return bool
     *   TRUE if there are no items. FALSE if there is at least one.
     */
    public function empty(): bool;

    /**
     * Sorts items by their values.
     *
     * @param  callable $sort
     *   Signature: function(mixed $value1, mixed $value2): bool. Defaults to NULL for a regular sort.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function sort(callable $sort = null): self;

    /**
     * Sorts items by their keys.
     *
     * @param  callable $sort
     *   Signature: function(mixed $key1, mixed $key2): bool. Defaults to NULL for a regular sort.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function sortKeys(callable $sort = null): self;

    /**
     * Chains other iterables to this iterator, and re-indexes the values.
     *
     * @return \BartFeenstra\Functional\Iterator
     *
     * @throws \BartFeenstra\Functional\InvalidIterable
     *   Thrown if one of the items is not an iterable.
     */
    public function chain(...$iterables): self;

    /**
     * Flattens the iterables contained by this iterator into a single new iterator.
     *
     * @return \BartFeenstra\Functional\Iterator
     *
     * @throws \BartFeenstra\Functional\InvalidIterable
     *   Thrown if one of the items is not an iterable.
     */
    public function flatten(): self;
}
