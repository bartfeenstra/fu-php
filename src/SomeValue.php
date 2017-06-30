<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Wraps some concrete value.
 */
final class SomeValue implements Some
{

    use ImmutableTrait;

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

    public function get()
    {
        return $this->value;
    }
}
