<?php

declare(strict_types = 1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional as F;
use BartFeenstra\Functional\Iterable\ArrayIterator;
use BartFeenstra\Functional\Iterable\InvalidIterable;
use BartFeenstra\Functional\Iterable\Iterator;
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
        $iterator = F\iter(new ArrayIterator($array));
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
        $iterator = F\iter(new class() implements F\Iterable\ToIterator {
            public function iter(): Iterator
            {
                return new ArrayIterator([3, 1, 4]);
            }
        });
        $this->assertInstanceOf(Iterator::class, $iterator);
        $this->assertSame([3, 1, 4], iterator_to_array($iterator));
    }
}
