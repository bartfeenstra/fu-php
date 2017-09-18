<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Iterable;

/**
 * Is thrown when an invalid iterator item is encountered.
 *
 * An iterable is any value that is accepted by \BartFeenstra\Functional\iter().
 *
 * @see \BartFeenstra\Functional\iter()
 */
final class InvalidItem extends \InvalidArgumentException
{

    private $invalidItem;

    /**
     * Constructs a new instance.
     *
     * @param string $message
     * @param mixed $invalidItem
     * @param \Throwable|null $previous
     */
    public function __construct($message, $invalidItem, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->invalidItem = $invalidItem;
    }

    /**
     * Gets the value that isn't valid.
     *
     * @return mixed
     */
    public function getInvaliditem()
    {
        return $this->invalidItem;
    }
}
