<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines an iterator.
 */
interface Iterator extends \Iterator, \Countable
{

  /**
   * Performs an operation for each item.
   *
   * @param callable $operation
   *   Signature: function(mixed $item): void.
   */
    public function each(callable $operation): void;

  /**
   * Filters items using a predicate.
   *
   * @param callable $predicate
   *   Signature: function($item): bool.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function filter(callable $predicate): self;

  /**
   * Maps/converts items.
   *
   * @param callable $conversion
   *   Signature: function(mixed $item): mixed.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function map(callable $conversion): self;

  /**
   * Reduces items to a single value.
   *
   * @param callable $reduction
   *   Signature: function(mixed $carrier, mixed $item): mixed. The parameter and return types are identical.
   *
   * @return mixed
   *   The type is the same as that of the items.
   */
    public function reduce(callable $reduction);

  /**
   * Folds items to a single value.
   *
   * @param callable $fold
   *   Signature: function(mixed $carrier, mixed $item): mixed. The return type is the same as that of the carrier.
   * @param mixed $initial_carrier
   *
   * @return mixed
   *   The type is the same as that of the initial carrier.
   */
    public function fold(callable $fold, $initial_carrier);

  /**
   * Takes only the first n items.
   *
   * @param int $length
   *   The number of items to take.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function take(int $length): self;

  /**
   * Takes all items up to but not including the first one for which the predicate evaluates to FALSE.
   *
   * @param callable $predicate
   *   Signature: function($item): bool.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function takeWhile(callable $predicate): self;

  /**
   * Takes a slice out of the items.
   *
   * @param int $start
   *   The index of the first item to take. Indexes start at 0.
   * @param int $length
   *   The number of items to take.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function slice(int $start, int $length): self;

  /**
   * Gets the item with the lowest value.
   *
   * @return mixed
   *   The type is the same as that of the items.
   */
    public function min();

  /**
   * Gets the item with the highest value.
   *
   * @return mixed
   *   The type is the same as that of the items.
   */
    public function max();

  /**
   * Gets the sum of all the items.
   *
   * @return mixed
   *   The type is the same as that of the items.
   */
    public function sum();

  /**
   * Zips each item into a tuple with corresponding items from each of the other traversables.
   *
   * @param mixed $other
   *   Any value taken by \BartFeenstra\Functional\iter().
   * @param mixed ...$others
   *   Any values taken by \BartFeenstra\Functional\iter().
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function zip($other, ...$others): self;
}
