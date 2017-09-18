<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\MapIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\MapIterator
 */
final class MapIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\MapIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new MapIterator(new ArrayIterator($array), function (int $value, int $key): string {
            // Use both the key and the value.
            return (string)($key + $value);
        });
        $expected = ['3', '2', '6', '4', '9', '14'];
        $this->assertSame($expected, $iterator->toArray());
    }
}
