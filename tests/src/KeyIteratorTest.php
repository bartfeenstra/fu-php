<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\KeyIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\KeyIterator
 */
final class KeyIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\KeyIterator
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
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
