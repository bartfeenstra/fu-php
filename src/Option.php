<?php

declare(strict_types=1);

namespace BartFeenstra\Functional;

/**
 * Defines an optional value.
 *
 * In PHP, NULL signifies the absence of a value, but it is also used as a value itself. In such cases, an option type
 * helps to distinguish between NULL as a value, and no value at all.
 *
 * This interface SHOULD not be implemented outside this package to guarantee only the Some and None implementations
 * exist. Dependent packages SHOULD only type hint on this interface, and SHOULD perform instanceof on Some and None.
 *
 * @see \BartFeenstra\Functional\Some
 * @see \BartFeenstra\Functional\None
 */
interface Option
{
}
