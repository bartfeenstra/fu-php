<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\SomeLazyValue;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\SomeLazyValue
 */
final class SomeLazyValueTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        // Use an object to ensure value identity.
        $value = new \stdClass();
        $retriever = function () use ($value) {
            return $value;
        };
        $some = new SomeLazyValue($retriever);
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
        $retriever = function () use ($value) {
            return $value;
        };
        $some = new SomeLazyValue($retriever);
        $this->assertSame($value, $some->get());
    }
}
