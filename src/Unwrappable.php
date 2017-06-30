<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines a type that wraps another value.
 */
interface Unwrappable
{

    /**
     * Returns the wrapped value.
     *
     * This allows for short syntax: `$value = $unwrappable();`
     *
     * @return mixed
     *   The wrapped value.
     */
    public function __invoke();

    /**
     * Returns the wrapped value.
     *
     * @return mixed
     *   The wrapped value.
     */
    public function get();
}
