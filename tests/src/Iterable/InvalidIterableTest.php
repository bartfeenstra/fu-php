<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Iterable;

use BartFeenstra\Functional\Iterable\InvalidIterable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\InvalidIterable
 */
final class InvalidIterableTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getInvalidIterable
     */
    public function testGetInvalidIterable()
    {
        $invalidIterable = new \stdClass();
        $sut = new InvalidIterable('Something is wrong.', $invalidIterable);
        $this->assertSame($invalidIterable, $sut->getInvalidIterable());
    }

    /**
     * @covers ::__construct
     * @covers ::getMessage
     */
    public function testGetMessage()
    {
        $message = 'Something is wrong.';
        $sut = new InvalidIterable($message, null);
        $this->assertSame($message, $sut->getMessage());
    }

    /**
     * @covers ::__construct
     * @covers ::getPrevious
     */
    public function testGetPrevious()
    {
        $previousException = new \Exception();
        $sut = new InvalidIterable('Something is wrong.', null, $previousException);
        $this->assertSame($previousException, $sut->getPrevious());
    }
}
