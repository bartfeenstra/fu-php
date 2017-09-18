<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\InvalidItem;
use BartFeenstra\Functional\Iterable\IteratorIterator;
use BartFeenstra\Functional\Iterable\TerminateFold;
use BartFeenstra\Functional\Iterable\TerminateReduction;
use BartFeenstra\Functional\None;
use BartFeenstra\Functional\SomeValue;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\IteratorTrait
 */
final class IteratorTraitTest extends TestCase
{

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this->assertSame($array, $iterator->toArray());
    }

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
        $this->assertSame($expected, $iterator->toArray());
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
     * @covers ::assert
     * @covers \BartFeenstra\Functional\Iterable\InvalidItem
     */
    public function testAssertSome()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        try {
            $iterator->assert(function (int $value, int $key): bool {
                // Use both the key and the value.
                return $key % 2 === 0 or $value < 5;
            });
            $this->fail('The expected exception was not thrown.');
        } catch (InvalidItem $e) {
            $this->assertEquals(9, $e->getInvalidItem());
        }
    }

    /**
     * @covers ::assert
     */
    public function testAssertNone()
    {
        $array = [3, 1, 4, 1, 5, 9];
        $iterator = new ArrayIterator($array);
        $this_iterator = $iterator->assert(function (int $value) :bool {
            return $value < 999;
        });
        $this->assertSame($iterator, $this_iterator);
    }

    /**
     * @covers ::assert
     * @covers \BartFeenstra\Functional\Iterable\InvalidItem
     */
    public function testAssertWithoutPredicate()
    {
        $array = [3, 1, 4, 1, 5, 9, 0];
        $iterator = new ArrayIterator($array);
        try {
            $iterator->assert();
            $this->fail('The expected exception was not thrown.');
        } catch (InvalidItem $e) {
            $this->assertEquals(0, $e->getInvalidItem());
        }
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
        $this->assertSame($expected, $iterator->toArray());
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
        $this->assertSame($expected, $iterator->toArray());
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
     * @covers \BartFeenstra\Functional\Iterable\TerminateReduction
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
     * @covers \BartFeenstra\Functional\Iterable\TerminateFold
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
        $this->assertSame($expected, $iterator->toArray());
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
        $this->assertSame($expected, $iterator->toArray());
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
        $this->assertSame($expected, $iterator->toArray());
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
        $this->assertSame($expected, $iterator->toArray());
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
        $this->assertSame($expected, $iterator->take(7)->list()->toArray());
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
        $this->assertSame($expected, $iterator->toArray());
    }

    /**
     * @covers ::list
     */
    public function testList()
    {
        $array = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $iterator = new ArrayIterator($array);
        $expected = ['A', 'B', 'C'];
        $this->assertSame($expected, $iterator->list()->toArray());
    }

    /**
     * @covers ::listKeys
     */
    public function testListKeys()
    {
        $array = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $iterator = new ArrayIterator($array);
        $expected = ['a', 'b', 'c'];
        $this->assertSame($expected, $iterator->listKeys()->toArray());
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
        $this->assertSame($expected, $iterator->flip()->toArray());
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
        $this->assertSame([4, 1, 3], $iterator->toArray());
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
        $this->assertSame($expected, $sort->toArray());
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
        $this->assertSame($expected, $sort->toArray());
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
        $this->assertSame($expected, $sort->toArray());
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
        $this->assertSame($expected, $sort->toArray());
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
     * @covers ::merge
     */
    public function testMerge()
    {
        $arrayOne = [
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
        ];
        $arrayTwo = [
            'zero' => 'Nul',
            'three' => 'Drie',
        ];
        $arrayThree = [
            'two' => 'Dva',
            'four' => 'Chotyry',
        ];
        $iterator = new ArrayIterator($arrayOne);
        $merge = $iterator->merge($arrayTwo, $arrayThree);
        $expected = [
            'one' => 'One',
            'two' => 'Dva',
            'three' => 'Drie',
            'zero' => 'Nul',
            'four' => 'Chotyry',
        ];
        $this->assertSame($expected, $merge->toArray());
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

    /**
     * @covers ::flatten
     */
    public function testFlattenWithMultipleLevels()
    {
        $array = [
            [[3, 1], [4]],
            [[1], [[5, 9]]],
            [[2], [6, 5]],
        ];
        $iterator = new ArrayIterator($array);
        $flattened = $iterator->flatten(2);
        $expected = [3, 1, 4, 1, [5, 9], 2, 6, 5];
        $this->assertSame($expected, iterator_to_array($flattened));
    }

    /**
     * @covers ::flatten
     */
    public function testFlattenWithNonIntegerKeys()
    {
        $array = [
            'a' => [3, 1, 4],
            'b' => [1, 5, 9],
            'c' => [2, 6, 5],
        ];
        $iterator = new ArrayIterator($array);
        $flattened = $iterator->flatten();
        $expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
        $this->assertSame($expected, iterator_to_array($flattened));
    }

    /**
     * @covers ::unique
     */
    public function testUnique()
    {
        $objectOne = new \stdClass();
        $objectTwo = new \stdClass();
        $array = [
            0 => 3,
            1 => 1,
            2 => 4,
            3 => 1,
            4 => 5,
            5 => 9,
            6 => 2,
            7 => 6,
            8 => 5,
            9 => '3',
            10 => true,
            11 => false,
            12 => null,
            13 => 0,
            14 => $objectOne,
            15 => $objectOne,
            16 => $objectTwo,
            17 => [],
            18 => [],
        ];
        $iterator = new ArrayIterator($array);
        $unique = $iterator->unique();
        $expected = [
            0 => 3,
            1 => 1,
            2 => 4,
            4 => 5,
            5 => 9,
            6 => 2,
            7 => 6,
            9 => '3',
            10 => true,
            11 => false,
            12 => null,
            13 => 0,
            14 => $objectOne,
            16 => $objectTwo,
            17 => [],
        ];
        $this->assertSame($expected, iterator_to_array($unique));
    }
}
