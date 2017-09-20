<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

use BartFeenstra\Functional\ImmutableTrait;
use BartFeenstra\Functional\Some;
use BartFeenstra\Functional\UnwrappableTrait;

/**
 * Wraps some iterable item.
 */
final class SomeItem implements Some
{

    use ImmutableTrait;
    use UnwrappableTrait;

    private $key;
    private $value;

    /**
     * Constructs a new instance.
     *
     * @param mixed $value
     *   The item's value.
     * @param mixed $key
     *   The item's key.
     */
    public function __construct($value, $key)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function __invoke()
    {
        return $this->value;
    }

    public function getKey()
    {
        return $this->key;
    }
}
