<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Wraps some concrete value.
 */
final class SomeValue implements Some
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
