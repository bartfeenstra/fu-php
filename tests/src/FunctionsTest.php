<?php

declare(strict_types = 1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional as F;
use BartFeenstra\Functional\InvalidIterable;
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
        $this->expectException(InvalidIterable::class);
        $func = function ($required) {
        };
        F\iter($func);
    }

  /**
   * @covers \BartFeenstra\Functional\iter
   */
    public function testIterWithClosureWithInvocationError()
    {
        $this->expectException(InvalidIterable::class);
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
        $this->expectException(InvalidIterable::class);
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
     * @covers \BartFeenstra\Functional\try_except
     */
    public function testTryExceptOk()
    {
        $value = new \stdClass();
        $goal = function () use ($value) {
            return $value;
        };
        $expected = new F\OkValue($value);
        $this->assertEquals($expected, F\try_except($goal));
    }

    /**
     * @covers \BartFeenstra\Functional\try_except
     */
    public function testTryExceptCaughtThrowable()
    {
        $throwable = new \BadMethodCallException();
        $goal = function () use ($throwable) {
            throw $throwable;
        };
        $expected = new F\ThrowableError($throwable);
        // Except a parent class, so we make sure the code under test can handle inheritance.
        $this->assertEquals($expected, F\try_except($goal, \BadFunctionCallException::class));
    }

    /**
     * @covers \BartFeenstra\Functional\try_except
     */
    public function testTryExceptUncaughtThrowable()
    {
        $this->expectException(\RuntimeException::class);
        $goal = function () {
            throw new \RuntimeException();
        };
        F\try_except($goal, \InvalidArgumentException::class);
    }

    /**
     * @covers \BartFeenstra\Functional\retry_except
     */
    public function testRetryExceptOk()
    {
        $value = new \stdClass();
        $goal = function () use ($value) {
            return $value;
        };
        $expected = new F\OkValue($value);
        $this->assertEquals($expected, F\retry_except($goal));
    }

    /**
     * @covers \BartFeenstra\Functional\retry_except
     */
    public function testRetryExceptOkAfterRetry()
    {
        $value = new \stdClass();
        $goal = function () use ($value) {
            static $invocations = 0;
            $invocations++;
            if ($invocations > 5) {
                return $value;
            }
            throw new \Exception();
        };
        $expected = new F\OkValue($value);
        $this->assertEquals($expected, F\retry_except($goal, 9));
    }

    /**
     * @covers \BartFeenstra\Functional\retry_except
     */
    public function testRetryExceptCaughtThrowable()
    {
        $throwable = new \BadMethodCallException();
        $goal = function () use ($throwable) {
            throw $throwable;
        };
        $expected = new F\ThrowableError($throwable);
        // Except a parent class, so we make sure the code under test can handle inheritance.
        $this->assertEquals($expected, F\retry_except($goal, 2, \BadFunctionCallException::class));
    }

    /**
     * @covers \BartFeenstra\Functional\retry_except
     */
    public function testRetryExceptUncaughtThrowable()
    {
        $this->expectException(\RuntimeException::class);
        $goal = function () {
            throw new \RuntimeException();
        };
        F\retry_except($goal, 2, \InvalidArgumentException::class);
    }

    /**
     * Provides data to self::testTrue().
     */
    public function provideTrue()
    {
        $data = [];

        $data[] = [true, true];
        $data[] = [false, 7];
        $data[] = [false, 'foo'];
        $data[] = [false, new \stdClass()];
        $data[] = [false, ['foo']];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\true
     *
     * @dataProvider provideTrue
     */
    public function testTrue($expected, $value)
    {
        $this->assertSame($expected, F\true()($value));
    }

    /**
     * Provides data to self::testFalse().
     */
    public function provideFalse()
    {
        $data = [];

        $data[] = [true, false];
        $data[] = [false, 0];
        $data[] = [false, ''];
        $data[] = [false, []];
        $data[] = [false, null];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\false
     *
     * @dataProvider provideFalse
     */
    public function testFalse($expected, $value)
    {
        $this->assertSame($expected, F\false()($value));
    }

    /**
     * Provides data to self::testTruthy().
     */
    public function provideTruthy()
    {
        $data = [];

        $data[] = [true, true];
        $data[] = [true, 7];
        $data[] = [true, 'foo'];
        $data[] = [true, new \stdClass()];
        $data[] = [true, ['foo']];

        $data[] = [false, []];
        $data[] = [false, ''];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\truthy
     *
     * @dataProvider provideTruthy
     */
    public function testTruthy($expected, $value)
    {
        $this->assertSame($expected, F\truthy()($value));
    }

    /**
     * Provides data to self::testFalsy().
     */
    public function provideFalsy()
    {
        $data = [];

        $data[] = [true, false];
        $data[] = [true, 0];
        $data[] = [true, ''];
        $data[] = [true, []];
        $data[] = [true, null];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\falsy
     *
     * @dataProvider provideFalsy
     */
    public function testFalsy($expected, $value)
    {
        $this->assertSame($expected, F\falsy()($value));
    }

    /**
     * Provides data to self::testId().
     */
    public function provideId()
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
        $data[] = [false, 'foo', 'bar'];

        // Confirm strict comparison works.
        $data[] = [false, true, 1];
        $data[] = [false, null, false];
        $data[] = [false, null, ''];
        $data[] = [false, 7, 7.0];
        $data[] = [false, '0', 0];
        $data[] = [false, new \stdClass(), new \stdClass()];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\id
     *
     * @dataProvider provideId
     */
    public function testId($expected, $value, $other)
    {
        $this->assertSame($expected, F\id($other)($value));
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
        $data[] = [false, 'foo', 'bar'];

        // Confirm loose comparison works.
        $data[] = [true, true, 1];
        $data[] = [true, null, false];
        $data[] = [true, null, ''];
        $data[] = [true, 7, 7.0];
        $data[] = [true, '0', 0];
        $data[] = [true, new \stdClass(), new \stdClass()];

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

    /**
     * Provides data to self::testInstanceOf().
     */
    public function provideInstanceOf()
    {
        $data = [];

        // The exact same type.
        $data[] = [true, new \BadFunctionCallException(), [\BadFunctionCallException::class]];
        $data[] = [true, \BadFunctionCallException::class, [\BadFunctionCallException::class]];
        // A super type.
        $data[] = [true, new \BadMethodCallException(), [\BadFunctionCallException::class]];
        $data[] = [true, \BadMethodCallException::class, [\BadFunctionCallException::class]];
        // An interface.
        $data[] = [true, new \BadMethodCallException(), [\Throwable::class]];
        $data[] = [true, \BadMethodCallException::class, [\Throwable::class]];
        // A different type.
        $data[] = [false, new \BadMethodCallException(), [\InvalidArgumentException::class]];
        $data[] = [false, \BadMethodCallException::class, [\InvalidArgumentException::class]];
        // Non-objects.
        $data[] = [false, 666, [\Throwable::class]];
        $data[] = [false, 'foo', [\Throwable::class]];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\instance_of
     *
     * @dataProvider provideInstanceOf
     */
    public function testInstanceOf(bool $expected, $value, array $types)
    {
        $this->assertSame($expected, F\instance_of(...$types)($value));
    }

    /**
     * @covers \BartFeenstra\Functional\any
     *
     * @depends testGt
     * @depends testLt
     */
    public function testAny()
    {
        $any = F\any(F\lt(0), F\gt(9));
        $this->assertTrue($any(-111));
        $this->assertTrue($any(-1));
        $this->assertTrue($any(10));
        $this->assertTrue($any(1000));
        $this->assertFalse($any(0));
        $this->assertFalse($any(5));
        $this->assertFalse($any(9));
    }

    /**
     * @covers \BartFeenstra\Functional\all
     *
     * @depends testGt
     * @depends testLt
     */
    public function testAll()
    {
        $all = F\all(F\gt(0), F\lt(9));
        $this->assertTrue($all(1));
        $this->assertTrue($all(8));
        $this->assertFalse($all(-111));
        $this->assertFalse($all(0));
        $this->assertFalse($all(9));
        $this->assertFalse($all(999));
    }

    /**
     * @covers \BartFeenstra\Functional\not
     *
     * @depends testEq
     */
    public function testNot()
    {
        $not = F\not(F\eq('Apples and oranges'));
        $this->assertTrue($not('apples and Oranges'));
        $this->assertTrue($not('Pineapples and orange juice'));
        $this->assertFalse($not('Apples and oranges'));
    }

    /**
     * @covers \BartFeenstra\Functional\curry
     */
    public function testCurry()
    {
        $function = function (string $a, string $b, string $c, string $d = 'D'): string {
            return $a . $b . $c . $d;
        };
        $this->assertSame('ABCD', F\curry($function)('A')('B')('C'));
    }

    /**
     * @covers \BartFeenstra\Functional\curry
     */
    public function testCurryWithoutRequiredParameters()
    {
        $function = function (string $a = 'Z'): string {
            return $a;
        };
        $this->assertSame('A', F\curry($function)('A'));
    }

    /**
     * @covers \BartFeenstra\Functional\apply_l
     */
    public function testApplyL()
    {
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_l($function, 'A', 'B');
        $expected = 'ABCD';
        $this->assertSame($expected, $function('C', 'D'));
    }

    /**
     * @covers \BartFeenstra\Functional\apply_l
     */
    public function testApplyLWithoutEnoughArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_l($function, 'A', 'B');
        $function('C');
    }

    /**
     * @covers \BartFeenstra\Functional\apply_l
     */
    public function testApplyLWithTooManyArguments()
    {
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_l($function, 'A', 'B');
        $expected = 'ABCD';
        $this->assertSame($expected, $function('C', 'D', 'E"'));
    }

    /**
     * @covers \BartFeenstra\Functional\apply_r
     */
    public function testApplyR()
    {
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_r($function, 'C', 'D');
        $expected = 'ABCD';
        $this->assertSame($expected, $function('A', 'B'));
    }

    /**
     * @covers \BartFeenstra\Functional\apply_l
     */
    public function testApplyRWithoutEnoughArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_r($function, 'C', 'D');
        $function('A');
    }

    /**
     * @covers \BartFeenstra\Functional\apply_R
     */
    public function testApplRLWithTooManyArguments()
    {
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_r($function, 'C', 'D');
        $expected = 'ABCD';
        $this->assertSame($expected, $function('A', 'B', 'E'));
    }

    /**
     * @covers \BartFeenstra\Functional\apply_i
     */
    public function testApplyI()
    {
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_i($function, 1, 'B', 'C');
        $expected = 'ABCD';
        $this->assertSame($expected, $function('A', 'D'));
    }

    /**
     * @covers \BartFeenstra\Functional\apply_i
     */
    public function testApplyIWithoutEnoughArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_i($function, 1, 'B', 'C');
        $function('A');
    }

    /**
     * @covers \BartFeenstra\Functional\apply_i
     */
    public function testApplyIWithTooManyArguments()
    {
        $function = function (string $a, string $b, string $c, string $d): string {
            return $a . $b . $c . $d;
        };
        $function = F\apply_i($function, 1, 'B', 'C');
        $expected = 'ABCD';
        $this->assertSame($expected, $function('A', 'D', 'E'));
    }
}
