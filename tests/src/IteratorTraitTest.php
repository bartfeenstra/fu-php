<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\IteratorIterator;
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
        $array = array_reverse([3, 1, 4, 1, 5, 9], true);
        $iterator = new ArrayIterator($array);
        $iterator->each(function (int $value, int $key) use (&$carrier): void {
            // Use both the key and the value.
            $carrier[$key] = $value;
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
        $iterator = $iterator->filter(function (int $value, int $key) :bool {
            // Use both the key and the value.
            return $key % 2 === 0 and $value >= 4;
        });
        $expected = [
            // Make sure the result remains associative.
            2 => 4,
            4 => 5,
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
        $found = $iterator->find(function (int $value, int $key) :bool {
            // Use both the key and the value.
            return $key % 2 === 0 and $value >= 4;
        });
        $this->assertEquals(new SomeValue(4), $found);
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
        $iterator = $iterator->map(function (int $value, int $key) :string {
            // Use both the key and the value.
            return (string) ($key + $value);
        });
        $expected = ['3', '2', '6', '4', '9', '14'];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::mapKeys
     */
    public function testMapKeys()
    {
        $array = [
            3 => 'c',
            1 => 'a',
            4 => 'd',
        ];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->mapKeys(function (string $value, int $key) :string {
            // Use both the key and the value.
            return (string) $key . $value;
        });
        $expected = [
            '3c' => 'c',
            '1a' => 'a',
            '4d' => 'd',
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::reduce
     */
    public function testReduce()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->reduce(function (int $carrier, int $value, int $key): int {
            // Use both the key and the value.
            return $carrier + $value + $key;
        });
        $this->assertEquals(new SomeValue(11), $actual);
    }

    /**
     * @covers ::reduce
     */
    public function testReduceWithEmptyIterator()
    {
        $iterator = new ArrayIterator([]);
        $actual = $iterator->reduce(function (int $carrier, int $value, int $key): int {
            // Use both the key and the value.
            return $carrier + $value + $key;
        });
        $this->assertEquals(new None(), $actual);
    }

    /**
     * @covers ::reduce
     * @covers \BartFeenstra\Functional\TerminateReduction
     */
    public function testReduceWithTermination()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->reduce(function (int $carrier, int $value): int {
            $carrier = $carrier + $value;
            if ($carrier > 9) {
                throw new TerminateReduction($carrier);
            }
            return $carrier;
        });
        $this->assertEquals(new SomeValue(14), $actual);
    }

    /**
     * @covers ::fold
     */
    public function testFold()
    {
        $array = [3, 1, 4];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->fold(function (int $carrier, int $value, int $key): int {
            // Use both the key and the value.
            return $carrier + $value + $key;
        }, 1);
        $this->assertSame(12, $actual);
    }

    /**
     * @covers ::fold
     * @covers \BartFeenstra\Functional\TerminateFold
     */
    public function testFoldWithTermination()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $actual = $iterator->fold(function (int $carrier, int $value): int {
            $carrier = $carrier + $value;
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
        $iterator = $iterator->takeWhile(function (int $value, int $key): bool {
            // Use both the key and the value.
            return $key + $value < 9;
        });
        $expected = [
            // Make sure the result remains associative.
            0 => 3,
            1 => 1,
            2 => 4,
            3 => 1,
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }

    /**
     * @covers ::slice
     */
    public function testSliceWithLength()
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
     * @covers ::slice
     */
    public function testSliceWithoutLength()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $iterator = $iterator->slice(2);
        $expected = [
            // Make sure the result remains associative.
            2 => 4,
            3 => 1,
            4 => 5,
            5 => 9,
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
        $this->assertEquals(new SomeValue(1), $iterator->min());
    }

    /**
     * @covers ::max
     */
    public function testMax()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertEquals(new SomeValue(9), $iterator->max());
    }

    /**
     * @covers ::sum
     */
    public function testSum()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertEquals(new SomeValue(23), $iterator->sum());
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

    /**
     * @covers ::keys
     */
    public function testKeys()
    {
        $array = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $iterator = new ArrayIterator($array);
        $expected = ['a', 'b', 'c'];
        $this->assertSame($expected, iterator_to_array($iterator->keys()));
    }

    /**
     * @covers ::indexed
     */
    public function testIndexed()
    {
        $array = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $iterator = new ArrayIterator($array);
        $expected = ['A', 'B', 'C'];
        $this->assertSame($expected, iterator_to_array($iterator->indexed()));
    }

    /**
     * @covers ::flip
     */
    public function testFlip()
    {
        $array = [
            'a' => 3,
            'b' => 1,
            'c' => 4,
        ];
        $iterator = new ArrayIterator($array);
        $expected = [
            3 => 'a',
            1 => 'b',
            4 => 'c',
        ];
        $this->assertSame($expected, iterator_to_array($iterator->flip()));
    }

    /**
     * @covers ::reverse
     */
    public function testReverse()
    {
        $array = [3, 1, 4];
        // This package's ArrayIterator overrides this method, so avoid it in this test.
        $iterator = new IteratorIterator(new \ArrayIterator($array));
        $iterator = $iterator->reverse();
        $this->assertSame([4, 1, 3], iterator_to_array($iterator));
    }

    /**
     * @covers ::first
     */
    public function testFirst()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertEquals(new SomeValue(3), $iterator->first());
    }

    /**
     * @covers ::first
     */
    public function testFirstWithEmptyIterator()
    {
        $iterator = new ArrayIterator([]);
        $this->assertEquals(new None(), $iterator->first());
    }

    /**
     * @covers ::last
     */
    public function testLast()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertEquals(new SomeValue(9), $iterator->last());
    }

    /**
     * @covers ::last
     */
    public function testLastWithEmptyIterator()
    {
        $iterator = new ArrayIterator([]);
        $this->assertEquals(new None(), $iterator->last());
    }

    /**
     * @covers ::empty
     */
    public function testEmptyWithEmptyIterator()
    {
        $iterator = new ArrayIterator([]);
        $this->assertTrue($iterator->empty());
    }

    /**
     * @covers ::empty
     */
    public function testEmptyWithNonEmptyIterator()
    {
        $iterator = new ArrayIterator([3, 1, 4]);
        $this->assertFalse($iterator->empty());
    }

    /**
     * @covers ::sort
     */
    public function testSort()
    {
        $array = [
            3 => 'c',
            1 => 'a',
            4 => 'd',
        ];
        $iterator = new ArrayIterator($array);
        $sort = $iterator->sort(function (string $a, string $b): int {
            // Reverse the order, so we are different from the default sort.
            return -1 * ($a <=> $b);
        });
        $expected = [
            4 => 'd',
            3 => 'c',
            1 => 'a',
        ];
        $this->assertSame($expected, iterator_to_array($sort));
    }

    /**
     * @covers ::sort
     */
    public function testSortWithoutSort()
    {
        $array = [
            3 => 'c',
            1 => 'a',
            4 => 'd',
        ];
        $iterator = new ArrayIterator($array);
        $sort = $iterator->sort();
        $expected = [
            1 => 'a',
            3 => 'c',
            4 => 'd',
        ];
        $this->assertSame($expected, iterator_to_array($sort));
    }

    /**
     * @covers ::sortKeys
     */
    public function testSortKeys()
    {
        $array = [
            3 => 'c',
            1 => 'a',
            4 => 'd',
        ];
        $iterator = new ArrayIterator($array);
        $sort = $iterator->sortKeys(function (string $a, string $b): int {
            // Reverse the order, so we are different from the default sort.
            return -1 * ($a <=> $b);
        });
        $expected = [
            4 => 'd',
            3 => 'c',
            1 => 'a',
        ];
        $this->assertSame($expected, iterator_to_array($sort));
    }

    /**
     * @covers ::sortKeys
     */
    public function testSortKeysWithoutSort()
    {
        $array = [
            'c' => 3,
            'a' => 1,
            'd' => 4,
        ];
        $iterator = new ArrayIterator($array);
        $sort = $iterator->sortKeys();
        $expected = [
            'a' => 1,
            'c' => 3,
            'd' => 4,
        ];
        $this->assertSame($expected, iterator_to_array($sort));
    }

    /**
     * @covers ::chain
     */
    public function testChain()
    {
        $arrayOne = [3, 1, 4];
        $arrayTwo = [1, 5, 9];
        $arrayThree = [2, 6, 5];
        $iterator = new ArrayIterator($arrayOne);
        $chain = $iterator->chain($arrayTwo, $arrayThree);
        $expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->assertSame($expected, iterator_to_array($chain));
    }

    /**
     * @covers ::flatten
     */
    public function testFlatten()
    {
        $array = [
            [3, 1, 4],
            [1, 5, 9],
            [2, 6, 5],
        ];
        $iterator = new ArrayIterator($array);
        $flattened = $iterator->flatten();
        $expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->assertSame($expected, iterator_to_array($flattened));
    }
}
