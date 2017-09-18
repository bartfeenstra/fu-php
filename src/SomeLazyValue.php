<?php

declare(strict_types=1);

namespace BartFeenstra\Functional;

/**
 * Wraps some concrete value.
 */
final class SomeLazyValue implements Some
{

    use ImmutableTrait;
    use UnwrappableTrait;

    private $retriever;

    /**
     * Constructs a new instance.
     *
     * @param callable $retriever
     *   The value retriever. Signature: `function(): T`, where T is the type of the value.
     */
    public function __construct(callable $retriever)
    {
        $this->retriever = $retriever;
    }

    public function __invoke()
    {
        return call_user_func($this->retriever);
    }
}
