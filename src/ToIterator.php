<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines an iterable type.
 */
interface ToIterator
{

  /**
   * Gets an iterator over this data.
   *
   * @return \BartFeenstra\Functional\Iterator
   */
    public function iter(): Iterator;
}
