<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Aides in making objects immutable.
 */
trait ImmutableTrait
{

    public function __set($name, $value)
    {
        throw new \BadMethodCallException('This object is immutable.');
    }
}
