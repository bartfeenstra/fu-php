<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ImmutableTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\ImmutableTrait
 */
final class ImmutableTraitTest extends TestCase
{

    /**
     * @covers ::__set
     */
    public function testSet()
    {
        $immutable = $this->getMockForTrait(ImmutableTrait::class);
        $this->expectException(\BadMethodCallException::class);
        $immutable->thisWillFail = 'VeryBadly';
    }
}
