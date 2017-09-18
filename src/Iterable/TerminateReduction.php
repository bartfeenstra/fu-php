<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

use BartFeenstra\Functional\ImmutableTrait;

/**
 * Defines a throwable to terminate a reduction.
 */
final class TerminateReduction extends \RuntimeException
{
    use ImmutableTrait;

    private $carrier;

    public function __construct($carrier)
    {
        parent::__construct();
        $this->carrier = $carrier;
    }

    public function getCarrier()
    {
        return $this->carrier;
    }
}
