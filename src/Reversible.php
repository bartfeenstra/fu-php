<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines a reversible iterator.
 */
interface Reversible
{

    /**
     * Reverses the values.
     *
     * @return \BartFeenstra\Functional\Iterator
     */
    public function reverse(): Iterator;
}
