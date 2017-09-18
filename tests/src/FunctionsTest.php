<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional as F;
use PHPUnit\Framework\TestCase;

/**
 * Covers functions.php.
 */
final class FunctionsTest extends TestCase
{

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
     * @covers       \BartFeenstra\Functional\type
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
    public function testRetryExceptOkAfterInfiniteRetry()
    {
        $value = new \stdClass();
        $goal = function () use ($value) {
            static $invocations = 0;
            $invocations++;
            if ($invocations > 999) {
                return $value;
            }
            throw new \Exception();
        };
        $expected = new F\OkValue($value);
        $this->assertEquals($expected, F\retry_except($goal, null));
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
    public function testCurryWithTooManyParameters()
    {
        $function = function (string $a, string $b, string $c, string $d = 'D'): string {
            return $a . $b . $c . $d;
        };
        $this->assertSame('ABCD', F\curry($function)('A', 'X')('B', 'Y')('C', 'Z'));
    }

    /**
     * @covers \BartFeenstra\Functional\curry
     */
    public function testCurryWithoutParameters()
    {
        $this->expectException(\TypeError::class);
        $function = function () {
        };
        F\curry($function);
    }

    /**
     * @covers \BartFeenstra\Functional\curry
     */
    public function testCurryWithoutRequiredParameters()
    {
        $this->expectException(\TypeError::class);
        $function = function (string $a = 'Z') {
        };
        F\curry($function);
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
