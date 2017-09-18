<?php

declare(strict_types=1);

namespace BartFeenstra\Functional;

/**
 * Wraps a successful result value.
 */
interface Ok extends Result, Unwrappable
{
}
