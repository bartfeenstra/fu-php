<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Creates an iterator for iterable data.
 *
 * @param \BartFeenstra\Functional\Iterable\Iterator|\BartFeenstra\Functional\Iterable\ToIterator|iterable|callable $iterable
 *   Callables must take no arguments, and return any value that is valid for this parameter.
 *
 * @return \BartFeenstra\Functional\Iterable\Iterator
 *
 * @throws \BartFeenstra\Functional\Iterable\InvalidIterable
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
            throw new InvalidIterable(sprintf('Callables must take no arguments, '), $iterable, $e);
        } catch (\Throwable $e) {
            throw new InvalidIterable(sprintf('Error when invoking callable.'), $iterable, $e);
        }

        // Catch any problems with the return value.
        try {
            return iter($iterable);
        } catch (\Throwable $e) {
            throw new InvalidIterable(sprintf('Callable does not return anything that can be resolved to an iterator.'), $iterable, $e);
        }
    }

    throw new InvalidIterable(sprintf('%s is not a valid iterable.', type($iterable)), $iterable);
}
