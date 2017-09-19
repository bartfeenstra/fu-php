<?php

declare(strict_types=1);

namespace BartFeenstra\Tests\Functional\Callables;

use BartFeenstra\Functional\Callables\InvalidCallable;
use PHPUnit\Framework\TestCase;
use function BartFeenstra\Functional\Callables\assert_predicate;
use function BartFeenstra\Functional\Predicate\true;

/**
 * Covers functions.php.
 */
final class FunctionsTest extends TestCase
{

    /**
     * Provides data to self::testAssertPredicateWithInvalidPredicate().
     */
    public function provideAssertPredicateWithInvalidPredicate()
    {
        $data = [];

        $data['non_function_string'] = ['kjhlkdhoduyiuwmmsmbdscqplpkkdjdkjh'];
        $data['non_predicate_callable'] = [
            function () {
            }
        ];
        $data['too_many_parameters'] = [
            function ($value, $foo): bool {
            }
        ];
        $data['missing_return_type'] = [
            function ($value) {
            }
        ];
        $data['nullable_return_type'] = [
            function ($value): ?bool {
            }
        ];
        $data['invalid_return_type'] = [
            function ($value): int {
            }
        ];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\Callables\assert_predicate
     *
     * @dataProvider provideAssertPredicateWithInvalidPredicate
     */
    public function testAssertPredicatewWithInvalidPredicate($predicate)
    {
        $this->expectException(InvalidCallable::class);
        assert_predicate($predicate);
    }

    /**
     * Provides data to self::testAssertPredicateWithValidPredicate().
     */
    public function provideAssertPredicateWithValidPredicate()
    {
        $data = [];

        $data['built_in'] = ['is_string'];
        $data['non_predicate_callable'] = [
            function () {
            }
        ];
        $data['too_many_parameters'] = [
            function ($value, $foo): bool {
            }
        ];
        $data['missing_return_type'] = [
            function ($value) {
            }
        ];
        $data['nullable_return_type'] = [
            function ($value): ?bool {
            }
        ];
        $data['invalid_return_type'] = [
            function ($value): int {
            }
        ];

        return $data;
    }

    /**
     * @covers \BartFeenstra\Functional\Callables\assert_predicate
     *
     * @dataProvider provideAssertPredicateWithValidPredicate
     */
    public function testAssertPredicatewWithValidPredicate()
    {
        $this->assertNull(assert_predicate(true()));
    }
}
