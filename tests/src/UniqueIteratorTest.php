<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\UniqueIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\UniqueIterator
 */
final class UniqueIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\UniqueIterator
     */
    public function test()
    {
        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();
        $array = [
            0 => 3,
            1 => 1,
            2 => 4,
            3 => 1,
            4 => 5,
            5 => 9,
            6 => 2,
            7 => 6,
            8 => 5,
            9 => '3',
            10 => true,
            11 => false,
            12 => null,
            13 => 0,
            14 => $objectOne,
            15 => $objectOne,
            16 => $objectTwo,
            17 => [],
            18 => [],
        ];
        $iterator = new UniqueIterator(new ArrayIterator($array));
        $expected = [
            0 => 3,
            1 => 1,
            2 => 4,
            4 => 5,
            5 => 9,
            6 => 2,
            7 => 6,
            9 => '3',
            10 => true,
            11 => false,
            12 => null,
            13 => 0,
            14 => $objectOne,
            16 => $objectTwo,
            17 => [],
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
