<?php

declare(strict_types = 1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Is thrown when an invalid iterator argument is encountered.
 *
 * An iterable is any value that is accepted by \BartFeenstra\Functional\iter().
 *
 * @see \BartFeenstra\Functional\iter()
 */
final class InvalidIterable extends \InvalidArgumentException
{

    private $invalidIterable;

    /**
     * Constructs a new instance.
     *
     * @param string $message
     * @param mixed $invalidIterable
     * @param \Throwable|null $previous
     */
    public function __construct($message, $invalidIterable, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->invalidIterable = $invalidIterable;
    }

    /**
     * Gets the value that isn't an iterable.
     *
     * @return mixed
     */
    public function getInvalidIterable()
    {
        return $this->invalidIterable;
    }
}
