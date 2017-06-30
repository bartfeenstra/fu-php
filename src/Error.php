<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines an error.
 */
interface Error extends Result
{

    /**
     * Gets a string representation of this error for debugging purposes.
     *
     * @return string
     */
    public function __toString();
}
