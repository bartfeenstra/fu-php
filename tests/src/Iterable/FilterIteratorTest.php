<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\FilterIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\FilterIterator
 */
final class FilterIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\FilterIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new FilterIterator(new ArrayIterator($array), function (int $value, int $key): bool {
            // Use both the key and the value.
            return $key % 2 === 0 and $value >= 4;
        });
        $expected = [
            // Make sure the result remains associative.
            2 => 4,
            4 => 5,
        ];
        $this->assertSame($expected, $iterator->toArray());
    }
}
