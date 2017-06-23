<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\FilterIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\FilterIterator
 */
final class FilterIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\FilterIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new FilterIterator(new ArrayIterator($array), function (int $value) :bool {
            return $value < 4;
        });
        $expected = [
            // Make sure the result remains associative.
            0 => 3,
            1 => 1,
            3 => 1,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
