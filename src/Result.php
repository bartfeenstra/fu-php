<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines a value that is the result of an action that may possible fail.
 *
 * This interface SHOULD not be implemented outside this package to guarantee only the Ok and Error implementations
 * exist. Dependent packages SHOULD only type hint on this interface.
 *
 * @see \BartFeenstra\Functional\Ok
 * @see \BartFeenstra\Functional\Error
 */
interface Result
{
}
