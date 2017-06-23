<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\MapIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\MapIterator
 */
final class MapIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\MapIterator
     */
    public function test()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new MapIterator(new ArrayIterator($array), function (int $value) :string {
            return (string) (3 * $value);
        });
        $expected = ['9', '3', '12', '3', '15', '27'];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
