<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\InvalidIterable;
use BartFeenstra\Functional\Iterable\Iterator;
use BartFeenstra\Functional\Iterable\IteratorIterator;
use BartFeenstra\Functional\Iterable\ToIterator;
use function BartFeenstra\Functional\Predicate\instance_of;

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
 * @param int|null $attempts
 *   The number of times to try to reach the goal. Use NULL to retry infinitely. Defaults to 2 attempts / 1 retry.
 * @param string ...$except
 *   The fully qualified names of \Throwable implementations to limit the catch to. Any throwable not listed, will not
 *   be caught. If not given, all throwables will be caught.
 *
 * @return \BartFeenstra\Functional\Result
 *
 * @throws \Throwable
 *   If $except is given, any throwable thrown by $goal that is not in $except.
 */
function retry_except(callable $goal, ?int $attempts = 2, string ...$except): Result
{
    $retry = true;
    $remainingAttempts = $attempts;
    do {
        if (is_int($attempts)) {
            if ($remainingAttempts > 2) {
                $remainingAttempts--;
            } else {
                $retry = false;
            }
        }
        try {
            return new OkValue($goal());
        } catch (\Throwable $t) {
            if (!$except or instance_of(...$except)($t)) {
                if ($retry) {
                    continue;
                }
                return new ThrowableError($t);
            }
            throw $t;
        }
    } while ($retry);
}

/**
 * Curries a callable.
 *
 * @see https://en.wikipedia.org/wiki/Currying
 *
 * @param callable $callable
 *   The callable to curry.
 *
 * @return callable
 *   The curried callable.
 *
 * @throws \TypeError
 *   Thrown if the callable cannot be curried.
 */
function curry(callable $callable): callable
{
    $r = new \ReflectionFunction(\Closure::fromCallable($callable));
    $requiredParameters = $r->getNumberOfRequiredParameters();

    if ($requiredParameters < 1) {
        throw new \TypeError(sprintf('Callables must have at least one required parameter in order to be curried, but a callable with %d required parameters was given.', $requiredParameters));
    } elseif ($requiredParameters === 1) {
        return $callable;
    }

    // Curries the callable for a single parameter only, and keeps track of arguments that have been passed on already,
    // and when it must stop the curry and invoke the callable.
    $curry = function (callable $curry, array $arguments, callable $callable, int $remainingRequiredParameters) {
        $remainingRequiredParameters--;

        // If any other parameter need to be curried after this one, do so.
        if ($remainingRequiredParameters > 0) {
            return function ($argument) use ($curry, $arguments, $callable, $remainingRequiredParameters) {
                $arguments[] = $argument;
                return $curry($curry, $arguments, $callable, $remainingRequiredParameters);
            };
        }

        // This was the last parameter to be curried, so invoke the original callable.
        return function ($argument) use ($arguments, $callable) {
            $arguments[] = $argument;
            return $callable(...$arguments);
        };
    };

    // Initialize the curry, with an empty argument list, as the callable is not invoked yet.
    return $curry($curry, [], $callable, $requiredParameters);
}

/**
 * Partially applies a callable, left-sided.
 *
 * @see https://en.wikipedia.org/wiki/Partial_application
 *
 * @param callable $callable
 *   The callable to apply.
 * @param mixed ...$fixedArguments
 *   The arguments to fix.
 *
 * @return callable
 */
function apply_l(callable $callable, ...$fixedArguments): callable
{
    return function () use ($callable, $fixedArguments) {
        return call_user_func($callable, ...$fixedArguments, ...func_get_args());
    };
}

/**
 * Partially applies a callable, right-sided.
 *
 * @see https://en.wikipedia.org/wiki/Partial_application
 *
 * @param callable $callable
 *   The callable to apply.
 * @param mixed ...$fixedArguments
 *   The arguments to fix.
 *
 * @return callable
 */
function apply_r(callable $callable, ...$fixedArguments): callable
{
    $r = new \ReflectionFunction(\Closure::fromCallable($callable));
    $index = $r->getNumberOfParameters() - count($fixedArguments);
    return apply_i($callable, $index, ...$fixedArguments);
}

/**
 * Partially applies a callable, fixing positioned arguments.
 *
 * @see https://en.wikipedia.org/wiki/Partial_application
 *
 * @param callable $callable
 *   The callable to apply.
 * @param int $index
 *   The index of the argument to fix.
 * @param mixed ...$fixedArguments
 *   The arguments to fix.
 *
 * @return callable
 */
function apply_i(callable $callable, int $index, ...$fixedArguments): callable
{
    return function () use ($callable, $index, $fixedArguments) {
        $arguments = func_get_args();
        array_splice($arguments, $index, 0, $fixedArguments);
        return call_user_func($callable, ...$arguments);
    };
}
