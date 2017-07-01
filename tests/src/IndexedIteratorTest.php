<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\IndexedIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\IndexedIterator
 */
final class IndexedIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\IndexedIterator
     */
    public function test()
    {
        $array = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $iterator = new IndexedIterator(new ArrayIterator($array));
        $expected = ['A', 'B', 'C'];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
