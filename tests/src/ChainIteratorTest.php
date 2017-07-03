<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ChainIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\ChainIterator
 */
final class ChainIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\ChainIterator
     */
    public function test()
    {
        $arrayOne = [3, 1, 4];
        $arrayTwo = [1, 5, 9];
        $arrayThree = [2, 6, 5];
        $iterator = new ChainIterator($arrayOne, $arrayTwo, $arrayThree);
        $expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\ChainIterator
     */
    public function testWithoutITerators()
    {
        $iterator = new ChainIterator();
        $this->assertSame([], iterator_to_array($iterator));
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
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
