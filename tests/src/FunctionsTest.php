<?php

declare(strict_types = 1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional as F;
use BartFeenstra\Functional\Iterator;
use PHPUnit\Framework\TestCase;

/**
 * Covers functions.php.
 */
final class FunctionsTest extends TestCase
{

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithArray()
    {
        $array = [3, 1, 4];
        $iterator = F\iter($array);
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithGenerator()
    {
        $array = [3, 1, 4];
        $func = function () use ($array) {
            foreach ($array as $value) {
                yield $value;
            }
        };
        $iterator = F\iter($func());
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithClosure()
    {
        $array = [3, 1, 4];
        $func = function () use ($array) {
            foreach ($array as $value) {
                yield $value;
            }
        };
        $iterator = F\iter($func);
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithClosureWithRequiredParameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        $func = function ($required) {
        };
        F\iter($func);
    }

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithClosureWithInvocationError()
    {
        $this->expectException(\InvalidArgumentException::class);
        $func = function () {
            throw new \RuntimeException();
        };
        F\iter($func);
    }

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithClosureWithNonIteratorReturnValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $array = [3, 1, 4];
        $func = function () use ($array) {
            return 'fooz';
        };
        F\iter($func);
    }

    /**
     * @covers \BartFeenstra\Functional\iter
     */
    public function testIterWithIterator()
    {
        $array = [3, 1, 4];
        $iterator = F\iter(new F\ArrayIterator($array));
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\iter
     */
    public function testIterWithSplIterator()
    {
        $array = [3, 1, 4];
        $iterator = F\iter(new \ArrayIterator($array));
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\iter
     */
    public function testIterWithToIterator()
    {
        $iterator = F\iter(new class() implements F\ToIterator {
            public function iter(): Iterator
            {
                return new F\ArrayIterator([3, 1, 4]);
            }
        });
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame([3, 1, 4], iterator_to_array($iterator));
    }

    /**
     * Provides data to self::testType().
     */
    public function provideType()
    {
        $data = [];

        $data[] = ['0', 0];
        $data[] = ["'0'", '0'];
        $data[] = ["'Foo'", 'Foo'];
        $data[] = ['array', []];
        $data[] = ['array', [0]];
        $data[] = [get_class($this), $this];
        $data[] = ['resource', fopen(tempnam('/tmp', 'foo'), 'r')];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\type
     *
     * @dataProvider provideType
     */
    public function testType($expected, $value)
    {
        $this->assertSame($expected, F\type($value));
    }

    /**
     * Provides data to self::testEq().
     */
    public function provideEq()
    {
        $data = [];

        // Identical values.
        $data[] = [true, true, true];
        $data[] = [true, false, false];
        $data[] = [true, null, null];
        $data[] = [true, 7, 7];
        $object = new \stdClass();
        $data[] = [true, $object, $object];

        $data[] = [false, true, false];

        // Confirm strict comparison works.
        $data[] = [false, true, 1];
        $data[] = [false, null, false];
        $data[] = [false, null, ''];
        $data[] = [false, 7, 7.0];
        $data[] = [false, new \stdClass(), new \stdClass()];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\eq
     *
     * @dataProvider provideEq
     */
    public function testEq($expected, $value, $other)
    {
        $this->assertSame($expected, F\eq($other)($value));
    }

    /**
     * Provides data to self::testGt().
     */
    public function provideGt()
    {
        $data = [];

        $data[] = [true, 2, 1];
        $data[] = [true, 0, -1];
        $data[] = [false, 1, 2];
        $data[] = [false, -1, 0];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\gt
     *
     * @dataProvider provideGt
     */
    public function testGt($expected, $value, $other)
    {
        $this->assertSame($expected, F\gt($other)($value));
    }

    /**
     * Provides data to self::testGe().
     */
    public function provideGe()
    {
        $data = [];

        $data[] = [true, 2, 1];
        $data[] = [true, 2, 2];
        $data[] = [true, 0, -1];
        $data[] = [true, -1, -1];
        $data[] = [false, 1, 2];
        $data[] = [false, -1, 0];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\ge
     *
     * @dataProvider provideGe
     */
    public function testGe($expected, $value, $other)
    {
        $this->assertSame($expected, F\ge($other)($value));
    }

    /**
     * Provides data to self::testLt().
     */
    public function provideLt()
    {
        $data = [];

        $data[] = [true, 1, 2];
        $data[] = [true, -1, 0];
        $data[] = [false, 2, 1];
        $data[] = [false, 0, -1];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\lt
     *
     * @dataProvider provideLt
     */
    public function testLt($expected, $value, $other)
    {
        $this->assertSame($expected, F\lt($other)($value));
    }

    /**
     * Provides data to self::testLe().
     */
    public function provideLe()
    {
        $data = [];

        $data[] = [true, 1, 2];
        $data[] = [true, 2, 2];
        $data[] = [true, -1, 0];
        $data[] = [true, -1, -1];
        $data[] = [false, 2, 1];
        $data[] = [false, 0, -1];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\le
     *
     * @dataProvider provideLe
     */
    public function testLe($expected, $value, $other)
    {
        $this->assertSame($expected, F\le($other)($value));
    }
}
