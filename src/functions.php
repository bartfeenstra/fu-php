<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Creates an iterator for iterable data.
 *
 * @param \BartFeenstra\Functional\Iterator|\BartFeenstra\Functional\ToIterator|iterable|callable $iterable
 *   Callables must take no arguments, and return any value that is valid for this parameter.
 *
 * @return \BartFeenstra\Functional\Iterator
 *
 * @throws \InvalidArgumentException
 */
function iter($iterable) :Iterator
{
    if ($iterable instanceof Iterator) {
        return $iterable;
    } elseif ($iterable instanceof ToIterator) {
        return $iterable->iter();
    } elseif (is_array($iterable)) {
        return new ArrayIterator($iterable);
    } elseif ($iterable instanceof \Traversable) {
        return new IteratorIterator($iterable);
    } elseif (is_callable($iterable)) {
        // Catch any problems with the invocation.
        try {
            $iterable = $iterable();
        } catch (\ArgumentCountError $e) {
            throw new \InvalidArgumentException(sprintf('Callables must take no arguments, '), 0, $e);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException(sprintf('Error when invoking callable.'), 0, $e);
        }

        // Catch any problems with the return value.
        try {
            return iter($iterable);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException(sprintf('Callable does not return anything that can be resolved to an iterator.'), 0, $e);
        }
    }

    throw new \InvalidArgumentException(sprintf('%s is not a valid iterable.', type($iterable)));
}

/**
 * Get's a value's human-readable (type) representation.
 *
 * @param mixed $value
 *
 * @return string
 */
function type($value): string
{
    if (is_object($value)) {
        return get_class($value);
    } elseif (is_scalar($value)) {
        return var_export($value, true);
    } else {
        return gettype($value);
    }
}

/**
 * Gets an identity predicate.
 *
 * @param $other
 *   The other value to compare against.
 *
 * @return callable
 *   A predicate.
 */
function id($other): callable
{
    return function ($value) use ($other) {
        return $value === $other;
    };
}

/**
 * Gets an equality predicate.
 *
 * @param $other
 *   The other value to compare against.
 *
 * @return callable
 *   A predicate.
 */
function eq($other): callable
{
    return function ($value) use ($other) {
        return $value == $other;
    };
}

/**
 * Gets a predicate to check values are greater than another.
 *
 * @param $other
 *   The other value to compare against.
 *
 * @return callable
 *   A predicate.
 */
function gt($other): callable
{
    return function ($value) use ($other) {
        return $value > $other;
    };
}

/**
 * Gets a predicate to check values are greater than or equal to another.
 *
 * @param $other
 *   The other value to compare against.
 *
 * @return callable
 *   A predicate.
 */
function ge($other): callable
{
    return function ($value) use ($other) {
        return $value >= $other;
    };
}

/**
 * Gets a predicate to check values are lesser than another.
 *
 * @param $other
 *   The other value to compare against.
 *
 * @return callable
 *   A predicate.
 */
function lt($other): callable
{
    return function ($value) use ($other) {
        return $value < $other;
    };
}

/**
 * Gets a predicate to check values are lesser than or equal to another.
 *
 * @param $other
 *   The other value to compare against.
 *
 * @return callable
 *   A predicate.
 */
function le($other): callable
{
    return function ($value) use ($other) {
        return $value <= $other;
    };
}
