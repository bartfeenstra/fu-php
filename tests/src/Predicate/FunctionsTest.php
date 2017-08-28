<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Predicate;

use PHPUnit\Framework\TestCase;
use function BartFeenstra\Functional\Predicate\all;
use function BartFeenstra\Functional\Predicate\any;
use function BartFeenstra\Functional\Predicate\eq;
use function BartFeenstra\Functional\Predicate\false;
use function BartFeenstra\Functional\Predicate\falsy;
use function BartFeenstra\Functional\Predicate\ge;
use function BartFeenstra\Functional\Predicate\gt;
use function BartFeenstra\Functional\Predicate\id;
use function BartFeenstra\Functional\Predicate\instance_of;
use function BartFeenstra\Functional\Predicate\le;
use function BartFeenstra\Functional\Predicate\lt;
use function BartFeenstra\Functional\Predicate\not;
use function BartFeenstra\Functional\Predicate\true;
use function BartFeenstra\Functional\Predicate\truthy;

/**
 * Covers functions.php.
 */
final class FunctionsTest extends TestCase
{

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
     * @covers \BartFeenstra\Functional\Predicate\true
     *
     * @dataProvider provideTrue
     */
    public function testTrue($expected, $value)
    {
        $this->assertSame($expected, true()($value));
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
     * @covers \BartFeenstra\Functional\Predicate\false
     *
     * @dataProvider provideFalse
     */
    public function testFalse($expected, $value)
    {
        $this->assertSame($expected, false()($value));
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
     * @covers \BartFeenstra\Functional\Predicate\truthy
     *
     * @dataProvider provideTruthy
     */
    public function testTruthy($expected, $value)
    {
        $this->assertSame($expected, truthy()($value));
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
     * @covers \BartFeenstra\Functional\Predicate\falsy
     *
     * @dataProvider provideFalsy
     */
    public function testFalsy($expected, $value)
    {
        $this->assertSame($expected, falsy()($value));
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
     * @covers \BartFeenstra\Functional\Predicate\id
     *
     * @dataProvider provideId
     */
    public function testId($expected, $value, $other)
    {
        $this->assertSame($expected, id($other)($value));
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
     * @covers \BartFeenstra\Functional\Predicate\eq
     *
     * @dataProvider provideEq
     */
    public function testEq($expected, $value, $other)
    {
        $this->assertSame($expected, eq($other)($value));
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
     * @covers \BartFeenstra\Functional\Predicate\gt
     *
     * @dataProvider provideGt
     */
    public function testGt($expected, $value, $other)
    {
        $this->assertSame($expected, gt($other)($value));
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
     * @covers \BartFeenstra\Functional\Predicate\ge
     *
     * @dataProvider provideGe
     */
    public function testGe($expected, $value, $other)
    {
        $this->assertSame($expected, ge($other)($value));
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
     * @covers \BartFeenstra\Functional\Predicate\lt
     *
     * @dataProvider provideLt
     */
    public function testLt($expected, $value, $other)
    {
        $this->assertSame($expected, lt($other)($value));
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
     * @covers \BartFeenstra\Functional\Predicate\le
     *
     * @dataProvider provideLe
     */
    public function testLe($expected, $value, $other)
    {
        $this->assertSame($expected, le($other)($value));
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
     * @covers \BartFeenstra\Functional\Predicate\instance_of
     *
     * @dataProvider provideInstanceOf
     */
    public function testInstanceOf(bool $expected, $value, array $types)
    {
        $this->assertSame($expected, instance_of(...$types)($value));
    }

    /**
     * @covers  \BartFeenstra\Functional\Predicate\any
     *
     * @depends testGt
     * @depends testLt
     */
    public function testAny()
    {
        $any = any(lt(0), gt(9));
        $this->assertTrue($any(-111));
        $this->assertTrue($any(-1));
        $this->assertTrue($any(10));
        $this->assertTrue($any(1000));
        $this->assertFalse($any(0));
        $this->assertFalse($any(5));
        $this->assertFalse($any(9));
    }

    /**
     * @covers  \BartFeenstra\Functional\Predicate\all
     *
     * @depends testGt
     * @depends testLt
     */
    public function testAll()
    {
        $all = all(gt(0), lt(9));
        $this->assertTrue($all(1));
        $this->assertTrue($all(8));
        $this->assertFalse($all(-111));
        $this->assertFalse($all(0));
        $this->assertFalse($all(9));
        $this->assertFalse($all(999));
    }

    /**
     * @covers  \BartFeenstra\Functional\Predicate\not
     *
     * @depends testEq
     */
    public function testNot()
    {
        $not = not(eq('Apples and oranges'));
        $this->assertTrue($not('apples and Oranges'));
        $this->assertTrue($not('Pineapples and orange juice'));
        $this->assertFalse($not('Apples and oranges'));
    }
}
