<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional;

/**
 * Defines an error caused by an exception or PHP error.
 */
final class ThrowableError implements Error, Unwrappable
{

    use UnwrappableTrait;

    private $throwable;

    /**
     * Constructs a new instance.
     *
     * @param \Throwable $throwable
     *   The exception or PHP error.
     */
    public function __construct(\Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /**
     * Gets a string representation of this error for debugging purposes.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->throwable;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Throwable
     */
    public function __invoke()
    {
        return $this->throwable;
    }
}
