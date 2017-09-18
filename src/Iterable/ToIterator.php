<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Defines an iterable type.
 */
interface ToIterator
{

    /**
     * Gets an iterator over this data.
     *
     * @return \BartFeenstra\Functional\Iterable\Iterator
     */
    public function iter(): Iterator;
}
