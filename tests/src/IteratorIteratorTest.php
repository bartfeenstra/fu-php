<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\IteratorIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\IteratorIterator
 */
final class IteratorIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\IteratorIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new IteratorIterator(new \ArrayIterator($array));
        $this->assertSame($array, iterator_to_array($iterator));
    }
}
