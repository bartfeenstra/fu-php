<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\TakeWhileIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\TakeWhileIterator
 */
final class TakeWhileIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\TakeWhileIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new TakeWhileIterator(new ArrayIterator($array), function (int $value, int $key): bool {
            // Use both the key and the value.
            return $key + $value < 9;
        });
        $expected = [
            // Make sure the result remains associative.
            0 => 3,
            1 => 1,
            2 => 4,
            3 => 1,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
