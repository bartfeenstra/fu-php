<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Callables;

/**
 * Defines the different callable types used by PHP core and this library.
 */
abstract class Definitions
{

    /**
     * Defines a predicate.
     *
     * @param mixed $value
     *   The value to check.
     * @param mixed $key
     *   The key to check.
     *
     * @return bool
     *   Whether or not the value meets the condition.
     */
    abstract public function predicate($value, $key): bool;
}
