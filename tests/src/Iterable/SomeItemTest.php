<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\Iterable\SomeItem;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\Iterable\SomeItem
 */
final class SomeItemTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        // Use an object to ensure value identity.
        $value = new \stdClass();
        $key = 666;
        $some = new SomeItem($value, $key);
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
        $key = 666;
        $some = new SomeItem($value, $key);
        $this->assertSame($value, $some->get());
    }

    /**
     * @covers ::__construct
     * @covers ::getKey
     */
    public function testGetKey()
    {
        // Use an object to ensure value identity.
        $value = new \stdClass();
        $key = 666;
        $some = new SomeItem($value, $key);
        $this->assertSame($key, $some->getKey());
    }
}
