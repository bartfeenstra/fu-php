<?php

declare(strict_types=1);

namespace BartFeenstra\Functional;

/**
 * Defines the absence of a value.
 */
final class None implements Option
{
    use ImmutableTrait;
}
