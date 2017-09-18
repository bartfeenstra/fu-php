<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use PHPUnit\Framework\TestCase;
use function BartFeenstra\Functional\Iterable\iter;

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
        iter($list)->map(function (int $i): string {
            return str_repeat('.', $i);
        })->filter(function (string $s): bool {
            return strlen($s) > 1;
        })->each(function (string $s) use (&$carrier): void {
            $carrier[] = $s;
        });
        $expected = ['...', '....', '.....', '.........'];
        $this->assertSame($expected, $carrier);
    }
}
