<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\ArrayIterator;
use function BartFeenstra\Functional\Iterable\ensure_array;
use BartFeenstra\Functional\Iterable\InvalidIterable;
use BartFeenstra\Functional\Iterable\Iterator;
use BartFeenstra\Functional\Iterable\ToIterator;
use PHPUnit\Framework\TestCase;
use function BartFeenstra\Functional\Iterable\iter;

/**
 * Covers functions.php.
 */
final class FunctionsTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithArray()
    {
        $array = [3, 1, 4];
        $iterator = iter($array);
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithGenerator()
    {
        $array = [3, 1, 4];
        $func = function () use ($array) {
            foreach ($array as $value) {
                yield $value;
            }
        };
        $iterator = iter($func());
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithClosure()
    {
        $array = [3, 1, 4];
        $func = function () use ($array) {
            foreach ($array as $value) {
                yield $value;
            }
        };
        $iterator = iter($func);
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithClosureWithRequiredParameters()
    {
        $this->expectException(InvalidIterable::class);
        $func = function ($required) {
        };
        iter($func);
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithClosureWithInvocationError()
    {
        $this->expectException(InvalidIterable::class);
        $func = function () {
            throw new \RuntimeException();
        };
        iter($func);
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithClosureWithNonIteratorReturnValue()
    {
        $this->expectException(InvalidIterable::class);
        $array = [3, 1, 4];
        $func = function () use ($array) {
            return 'fooz';
        };
        iter($func);
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithIterator()
    {
        $array = [3, 1, 4];
        $iterator = iter(new ArrayIterator($array));
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithSplIterator()
    {
        $array = [3, 1, 4];
        $iterator = iter(new \ArrayIterator($array));
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame($array, iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\iter
     */
    public function testIterWithToIterator()
    {
        $iterator = iter(new class() implements ToIterator
        {
            public function iter(): Iterator
            {
                return new ArrayIterator([3, 1, 4]);
            }
        });
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame([3, 1, 4], iterator_to_array($iterator));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\ensure_array()
     */
    public function testEnsureArrayWithArray()
    {
        $array = [new \stdClass(), new \stdClass(), new \stdClass()];
        $this->assertSame($array, ensure_array($array));
    }

    /**
     * @covers \BartFeenstra\Functional\Iterable\ensure_array()
     */
    public function testEnsureArrayWithIterator()
    {
        $array = [new \stdClass(), new \stdClass(), new \stdClass()];
        $iterator = new ArrayIterator($array);
        $this->assertSame($array, ensure_array($iterator));
    }
}
