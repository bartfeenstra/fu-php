<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional as F;
use PHPUnit\Framework\TestCase;

/**
 * Covers many things.
 */
final class IntegrationTest extends TestCase
{

    /**
     * Covers many things.
     */
    public function test()
    {
        $carrier = [];
        $list = [3, 1, 4, 1, 5, 9];
        F\iter($list)->map(function (int $i): string {
            return str_repeat('.', $i);
        })->filter(function (string $s): bool {
            return strlen($s) > 1;
        })->each(function (string $s) use (&$carrier) {
            $carrier[] = $s;
        });
        $expected = ['...', '....', '.....', '.........'];
        $this->assertSame($expected, $carrier);
    }
}
