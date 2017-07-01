<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\None;
use BartFeenstra\Functional\SomeValue;
use BartFeenstra\Functional\TerminateFold;
use BartFeenstra\Functional\TerminateReduction;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\IteratorTrait
 */
final class IteratorTraitTest extends TestCase
{

    /**
     * @covers ::each
     */
    public function testEach()
    {
        $carrier = [];
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator->each(function (int $i) use (&$carrier) {
            $carrier[] = $i;
        });
        $this->assertSame($array, $carrier);
    }

    /**
     * @covers ::filter
     */
    public function testFilter()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->filter(function (int $value) :bool {
            return $value < 4;
        });
        $expected = [
            // Make sure the result remains associative.
            0 => 3,
            1 => 1,
            3 => 1,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::find
     */
    public function testFindSome()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $found = $iterator->find(function (int $value) :bool {
            return $value > 4;
        });
        $this->assertEquals(new SomeValue(5), $found);
    }

    /**
     * @covers ::find
     */
    public function testFindNone()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $found = $iterator->find(function (int $value) :bool {
            return $value > 9;
        });
        $this->assertEquals(new None(), $found);
    }

    /**
     * @covers ::find
     */
    public function testFindWithoutPredicate()
    {
        $array = [0, null, false, '', [], 666, 777];
        $iterator = new ArrayIterator($array);
        $found = $iterator->find();
        $this->assertEquals(new SomeValue(666), $found);
    }

    /**
     * @covers ::map
     */
    public function testMap()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->map(function (int $value) :string {
            return (string) (3 * $value);
        });
        $expected = ['9', '3', '12', '3', '15', '27'];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::reduce
     */
    public function testReduce()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->reduce(function (int $carrier, int $item): int {
            return $carrier + $item;
        });
        $this->assertSame(8, $actual);
    }

    /**
     * @covers ::reduce
     * @covers \BartFeenstra\Functional\TerminateReduction
     */
    public function testReduceWithTermination()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->reduce(function (int $carrier, int $item): int {
            $carrier = $carrier + $item;
            if ($carrier > 9) {
                throw new TerminateReduction($carrier);
            }
            return $carrier;
        });
        $this->assertSame(14, $actual);
    }

    /**
     * @covers ::fold
     */
    public function testFold()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->fold(function (int $carrier, int $item): int {
            return $carrier + $item;
        }, 1);
        $this->assertSame(9, $actual);
    }

    /**
     * @covers ::fold
     * @covers \BartFeenstra\Functional\TerminateFold
     */
    public function testFoldWithTermination()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->fold(function (int $carrier, int $item): int {
            $carrier = $carrier + $item;
            if ($carrier > 9) {
                throw new TerminateFold($carrier);
            }
            return $carrier;
        }, 1);
        $this->assertSame(10, $actual);
    }

    /**
     * @covers ::count
     */
    public function testCount()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $this->assertSame(3, $iterator->count());
        $this->assertSame(3, count($iterator));
    }

    /**
     * @covers ::take
     */
    public function testTake()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->take(3);
        $expected = [3, 1, 4];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::takeWhile
     */
    public function testTakeWhile()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->takeWhile(function (int $i): bool {
            return $i < 4;
        });
        $expected = [
            // Make sure the result remains associative.
            0 => 3,
            1 => 1,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::slice
     */
    public function testSlice()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->slice(2, 3);
        $expected = [
            // Make sure the result remains associative.
            2 => 4,
            3 => 1,
            4 => 5,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::min
     */
    public function testMin()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertSame(1, $iterator->min());
    }

    /**
     * @covers ::max
     */
    public function testMax()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertSame(9, $iterator->max());
    }

    /**
     * @covers ::sum
     */
    public function testSum()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertSame(23, $iterator->sum());
    }

    /**
     * @covers ::forever
     *
     * @depends testTake
     */
    public function testForever()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->forever();
        $expected = [3, 1, 4, 3, 1, 4, 3];
        $this->assertSame($expected, iterator_to_array($iterator->take(7), false));
    }

    /**
     * @covers ::zip
     */
    public function testZip()
    {
        $one = new ArrayIterator([3, 1, 4]);
        $two = [1, 5, 9];
        $three = [2, 9, 2];
        $iterator = $one->zip($two, $three);
        $expected = [[3, 1, 2], [1, 5, 9], [4, 9, 2]];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
