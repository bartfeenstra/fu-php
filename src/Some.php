<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Wraps some value.
 *
 * In PHP, NULL signifies the absence of a value, but it is also used as a value itself. In such cases, an option type
 * helps to distinguish between NULL as a value, and no value at all.
 */
interface Some extends Option, Unwrappable
{
}
