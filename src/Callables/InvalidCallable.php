<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Callables;

/**
 * Is thrown when an invalid callable is encountered.
 *
 * @see \BartFeenstra\Functional\assert_callable()
 */
final class InvalidCallable extends \InvalidArgumentException
{

    private $invalidCallable;

    /**
     * Constructs a new instance.
     *
     * @param string $message
     * @param mixed $invalidCallable
     * @param \Throwable|null $previous
     */
    public function __construct($message, $invalidCallable, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->invalidCallable = $invalidCallable;
    }

    /**
     * Gets the value that isn't a valid callable.
     *
     * @return mixed
     */
    public function getInvalidCallable()
    {
        return $this->invalidCallable;
    }
}
