<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\OkValue;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\OkValue
 */
final class OkValueTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        // Use an object to ensure value identity.
        $value = new \stdClass();
        $some = new OkValue($value);
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
        $some = new OkValue($value);
        $this->assertSame($value, $some->get());
    }
}
