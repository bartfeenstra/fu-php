<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\IndexedIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\IndexedIterator
 */
final class IndexedIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\IndexedIterator
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
