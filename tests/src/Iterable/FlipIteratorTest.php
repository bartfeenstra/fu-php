<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\FlipIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\FlipIterator
 */
final class FlipIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\FlipIterator
     */
    public function test()
    {
        $array = [
            'a' => 3,
            'b' => 1,
            'c' => 4,
        ];
        $iterator = new FlipIterator(new ArrayIterator($array));
        $expected = [
            3 => 'a',
            1 => 'b',
            4 => 'c',
        ];
        $this->assertSame($expected, $iterator->toArray());
    }
}
