<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ZipIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\ZipIterator
 */
final class ZipIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\ZipIterator
     */
    public function testIteration()
    {
        $one = [3, 1, 4];
        $two = [1, 5, 9];
        $three = [2, 9, 2];
        $iterator = new ZipIterator($one, $two, $three);
        $expected = [[3, 1, 2], [1, 5, 9], [4, 9, 2]];
        $this->assertSame($expected, $iterator->toArray());
    }
}
