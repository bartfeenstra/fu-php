<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Implements parts of \BartFeenstra\Functional\Unwrappable.
 */
trait UnwrappableTrait
{

    /**
     * Implements \BartFeenstra\Functional\Unwrappable::get().
     */
    public function get()
    {
        /** @var \BartFeenstra\Functional\Unwrappable $this */
        return $this();
    }
}
