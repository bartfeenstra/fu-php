<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\TakeWhileIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\TakeWhileIterator
 */
final class TakeWhileIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\TakeWhileIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new TakeWhileIterator(new ArrayIterator($array), function (int $i): bool {
            return $i < 4;
        });
        $expected = [
            // Make sure the result remains associative.
            0 => 3,
            1 => 1,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
