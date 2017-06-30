<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ThrowableError;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\ThrowableError
 */
final class ThrowableErrorTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $throwable = new \Exception();
        $error = new ThrowableError($throwable);
        $this->assertSame($throwable, $error());
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGet()
    {
        $throwable = new \Exception();
        $error = new ThrowableError($throwable);
        $this->assertSame($throwable, $error->get());
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToStringWithPrintableThrowable()
    {
        $throwable = new \Exception();
        $error = new ThrowableError($throwable);
        $this->assertContains(__FILE__, (string) $error);
    }
}
