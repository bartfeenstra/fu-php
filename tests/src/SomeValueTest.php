<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\SomeValue;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\SomeValue
 */
final class SomeValueTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        // Use an object to ensure value identity.
        $value = new \stdClass();
        $some = new SomeValue($value);
        $this->assertSame($value, $some());
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGet()
    {
        // Use an object to ensure value identity.
        $value = new \stdClass();
        $some = new SomeValue($value);
        $this->assertSame($value, $some->get());
    }
}
