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
 * Tries code and converts exceptions to errors.
 *
 * @param callable $goal
 *   The code to try and execute. Signature: `function(): mixed`.
 * @param string ...$except
 *   The fully qualified names of \Throwable implementations to limit the catch to. Any throwable not listed, will not
 *   be caught. If not given, all throwables will be caught.
 *
 * @return \BartFeenstra\Functional\Result
 *
 * @throws \Throwable
 *   If $except is given, any throwable thrown by $goal that is not in $except.
 */
function try_except(callable $goal, string ...$except): Result
{
    try {
        return new OkValue($goal());
    } catch (\Throwable $t) {
        if (!$except or instance_of(...$except)($t)) {
            return new ThrowableError($t);
        }
        throw $t;
    }
}

/**
 * Tries code multiple times, and converts exceptions to errors.
 *
 * This is a combination of \BartFeenstra\Functional\try_except(), and the retry keyword proposed for PHP's standard
 * library (https://wiki.php.net/rfc/retry-keyword).
 *
 * @param callable $goal
 *   The code to try and execute. Signature: `function(): mixed`.
 * @param int $attempts
 *   The number of times to try to reach the goal. Defaults to 2 attempts / 1 retry.
 * @param string ...$except
 *   The fully qualified names of \Throwable implementations to limit the catch to. Any throwable not listed, will not
 *   be caught. If not given, all throwables will be caught.
 *
 * @return \BartFeenstra\Functional\Result
 *
 * @throws \Throwable
 *   If $except is given, any throwable thrown by $goal that is not in $except.
 */
function retry_except(callable $goal, int $attempts = 2, string ...$except): Result
{
    $remainingAttempts = $attempts;
    do {
        $remainingAttempts--;
        try {
            return new OkValue($goal());
        } catch (\Throwable $t) {
            if (!$except or instance_of(...$except)($t)) {
                if ($remainingAttempts) {
                    continue;
                }
                return new ThrowableError($t);
            }
            throw $t;
        }
    } while ($remainingAttempts);
}

/**
 * Gets a predicate that matches TRUE.
 *
 * @return callable
 *   A predicate.
 */
function true(): callable
{
    return id(true);
}

/**
 * Gets a predicate that matches FALSE.
 *
 * @return callable
 *   A predicate.
 */
function false(): callable
{
    return id(false);
}

/**
 * Gets a predicate that matches values that evaluate to TRUE.
 *
 * @return callable
 *   A predicate.
 */
function truthy(): callable
{
    return eq(true);
}

/**
 * Gets a predicate that matches values that evaluate to FALSE.
 *
 * @return callable
 *   A predicate.
 */
function falsy(): callable
{
    return eq(false);
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

/**
 * Gets a predicate to check if a value is an instance of one or more types.
 *
 * @param string $type
 * @param string ..$types
 *
 * @return callable
 *   A predicate.
 */
function instance_of(string $type, string ...$types): callable
{
    $types = func_get_args();
    return function ($value) use ($types) {
        foreach ($types as $type) {
            if ($value instanceof $type) {
                return true;
            }
        }
        return false;
    };
}
