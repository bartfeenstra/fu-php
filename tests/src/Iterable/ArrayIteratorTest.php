<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\ArrayIterator
 */
final class ArrayIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\ArrayIterator
     */
    public function testIteration()
    {
        $array = [3, 1, 4];
        $this->assertSame($array, iterator_to_array(new ArrayIterator($array)));
    }

    /**
     * @covers ::reverse
     */
    public function testReverse()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->reverse();
        $this->assertSame([4, 1, 3], iterator_to_array($iterator));
    }
}
