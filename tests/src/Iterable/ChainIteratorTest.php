<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ChainIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\ChainIterator
 */
final class ChainIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\ChainIterator
     */
    public function test()
    {
        $arrayOne = [3, 1, 4];
        $arrayTwo = [1, 5, 9];
        $arrayThree = [2, 6, 5];
        $iterator = new ChainIterator($arrayOne, $arrayTwo, $arrayThree);
        $expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->assertSame($expected, $iterator->toArray());
        // Test again, to cover rewinding.
        $this->assertSame($expected, $iterator->toArray());
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\ChainIterator
     */
    public function testWithEmptyIterators()
    {
        $arrayOne = [3, 1, 4];
        $arrayTwo = [];
        $arrayThree = [1, 5, 9];
        $iterator = new ChainIterator($arrayOne, $arrayTwo, $arrayThree);
        $expected = [3, 1, 4, 1, 5, 9];
        $this->assertSame($expected, $iterator->toArray());
        // Test again, to cover rewinding.
        $this->assertSame($expected, $iterator->toArray());
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\ChainIterator
     */
    public function testWithoutIterators()
    {
        $iterator = new ChainIterator();
        $this->assertSame([], $iterator->toArray());
    }

    /**
     * @covers ::append
     */
    public function testAppend()
    {
        $arrayOne = [3, 1, 4];
        $arrayTwo = [1, 5, 9];
        $arrayThree = [2, 6, 5];
        $arrayFour = [3, 5, 8];
        $iterator = new ChainIterator($arrayOne, $arrayTwo);
        $iterator->append($arrayThree, $arrayFour);
        $expected = [3, 1, 4, 1, 5, 9, 2, 6, 5, 3, 5, 8];
        $this->assertSame($expected, $iterator->toArray());
    }
}
