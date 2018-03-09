<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Predicate;

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
 * Gets a predicate to check if an object or class name is an instance of one or more types.
 *
 * @param string[] $types
 *
 * @return callable
 *   A predicate.
 */
function instance_of(array $types): callable
{
    return function ($value) use ($types) {
        foreach ($types as $type) {
            if ($value instanceof $type or is_subclass_of($value, $type) or $value === $type) {
                return true;
            }
        }
        return false;
    };
}

/**
 * Gets a predicate that wraps other predicates and checks at least one of them matches.
 *
 * @param callable[] $predicates
 *
 * @return callable
 *   A predicate.
 */
function any(array $predicates): callable
{
    return function ($value) use ($predicates) {
        foreach ($predicates as $predicate) {
            if ($predicate($value)) {
                return true;
            }
        }
        return false;
    };
}

/**
 * Gets a predicate that wraps other predicates and checks all of them match.
 *
 * @param callable[] $predicates
 *
 * @return callable
 *   A predicate.
 */
function all(array $predicates): callable
{
    return function ($value) use ($predicates) {
        foreach ($predicates as $predicate) {
            if (!$predicate($value)) {
                return false;
            }
        }
        return true;
    };
}

/**
 * Gets a predicate that negates another predicate's result.
 *
 * @param callable $predicate
 *
 * @return callable
 *   A predicate.
 */
function not(callable $predicate): callable
{
    return function ($value) use ($predicate) {
        return !$predicate($value);
    };
}
