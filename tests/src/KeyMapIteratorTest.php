<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional;

use BartFeenstra\Functional\ArrayIterator;
use BartFeenstra\Functional\KeyMapIterator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BartFeenstra\Functional\KeyMapIterator
 */
final class KeyMapIteratorTest extends TestCase
{

    /**
     * @covers \BartFeenstra\Functional\KeyMapIterator
     */
    public function test()
    {
        $array = [
            3 => 'c',
            1 => 'a',
            4 => 'd',
        ];
        $iterator = new KeyMapIterator(new ArrayIterator($array), function (string $value, int $key): string {
            // Use both the key and the value.
            return (string) $key . $value;
        });
        $expected = [
            '3c' => 'c',
            '1a' => 'a',
            '4d' => 'd',
        ];
        $this->assertSame($expected, iterator_to_array($iterator));
    }
}
