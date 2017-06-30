<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Wraps a concrete successful result value.
 */
final class OkValue implements Ok
{

    use ImmutableTrait;
    use UnwrappableTrait;

    private $value;

    /**
     * Constructs a new instance.
     *
     * @param mixed $value
     *   The value to wrap.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke()
    {
        return $this->value;
    }
}
