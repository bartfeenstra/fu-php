<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\KeyIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\KeyIterator
 */
final class KeyIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\KeyIterator
     */
    public function test()
    {
        $array = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $iterator = new KeyIterator(new ArrayIterator($array));
        $expected = ['a', 'b', 'c'];
        $this->assertSame($expected, $iterator->toArray());
    }
}
