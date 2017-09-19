<?php

declare(strict_types=1);

namespace BartFeenstra\Functional\Callables;

use function BartFeenstra\Functional\type;

/**
 * Renders a callable in a human-readable way.
 *
 * @param \Reflector $reflector
 *
 * @return string
 */
function signature(\Reflector $reflector): string
{
    if ($reflector instanceof \ReflectionFunctionAbstract) {
        $parameters = implode(', ', array_map('\BartFeenstra\Functional\Callables\signature', $reflector->getParameters()));
        $return = $reflector->hasReturnType() ? sprintf(': %s', (string) $reflector->getReturnType()) : '';
        return sprintf('%s(%s)%s {}', $reflector->getName(), $parameters, $return);
    } elseif ($reflector instanceof \ReflectionParameter) {
        $type = $reflector->hasType() ? (string) $reflector->getType() . ' ' : '';
        $name = sprintf('$%s', $reflector->getName());
        $defaultValue = '';
        if ($reflector->isDefaultValueAvailable()) {
            if ($reflector->isDefaultValueConstant()) {
                $defaultValue = sprintf(' = %s' . $reflector->getDefaultValueConstantName());
            } else {
                $defaultValue = sprintf(' = %s', $reflector->getDefaultValue());
            }
        }
        return sprintf('%s%s%s', $type, $name, $defaultValue);
    }
    return (string) $reflector;
}

/**
 * Asserts a value is a valid predicate.
 *
 * @param mixed $predicate
 *
 * @throws \BartFeenstra\Functional\Callables\InvalidCallable
 */
function assert_predicate($predicate)
{
    assert_callable([Definitions::class, 'predicate'], $predicate);
}

/**
 * Asserts a given value is a valid callable matching the expected callable.
 *
 * @param callable|array $expected
 *   A callable, or an array with the following two items:
 *   - A string with the fully qualified name of an interface or abstract class.
 *   - A string with the name of the method on this interface or class.
 *   To indicate a parameter or return value can have any value, using `@param mixed $NAME` and `@return mixed` in the
 *   callable's docblock.
 * @param mixed $actual
 *   The value to assert as a valid callable.
 *
 * @throws \BartFeenstra\Functional\Callables\InvalidCallable
 *   Thrown if the actual value is not a valid callable.
 * @throws \TypeError
 *   Thrown if the expected value is not valid.
 */
function assert_callable($expected, $actual)
{
    if (!is_callable($actual)) {
        throw new InvalidCallable(sprintf('Invalid callable: %s', type($actual)), $actual);
    }

    try {
        $expectedReflection = new \ReflectionFunction($expected);
    } catch (\TypeError $e) {
        // Abstract methods are not callable, but we can reflect on them anyway via this detour.
        if (is_array($expected) and 2 === count($expected)) {
            $expectedReflection = (new \ReflectionClass($expected[0]))->getMethod($expected[1]);
        } else {
            throw $e;
        }
    }
    $actualReflection = new \ReflectionFunction($actual);
    try {
        _assert_callable($expectedReflection, $actualReflection);
    } catch (InvalidCallable $e) {
        throw new InvalidCallable(sprintf('Failed asserting that callable %s is compatible with expected %s.', signature($actualReflection), signature($expectedReflection)), $e->getInvalidCallable(), $e);
    }
}

function _assert_callable(\ReflectionFunctionAbstract $expectedCallable, \ReflectionFunctionAbstract $actualCallable)
{
    $expectedParameters = $expectedCallable->getParameters();
    $actualParameters = $actualCallable->getParameters();

    if (count($expectedParameters) < count($actualParameters)) {
        throw new InvalidCallable(sprintf(
            'The callable %s must take %d argument(s) or less.',
            signature($expectedCallable),
            count($expectedParameters)
        ), $actualCallable);
    }

    $expectedParameters = array_slice($expectedParameters, 0, count($actualParameters));
    for ($i = 0; $i < count($expectedParameters); $i++) {
        $expectedParameter = $expectedParameters[$i];
        $actualParameter = $actualParameters[$i];
        if (!$expectedParameter->hasType() and $actualParameter->hasType()) {
            throw new InvalidCallable(sprintf(
                'The callable %s must not specify a type for parameter %d.',
                signature($actualCallable),
                $i
            ), $actualCallable);
        }

        if ($expectedParameter->hasType()) {
            if (!$actualParameter->hasType()) {
                throw new InvalidCallable(sprintf(
                    'The callable %s must specify a type for parameter %d.',
                    signature($actualCallable),
                    $i
                ), $actualCallable);
            }

            // @todo If interface or class, check covariance and contravariance.
            if ($expectedParameter->getType()->getName() !== $actualParameter->getType()->getName()) {
                throw new InvalidCallable(
                    sprintf('The callable %s must take %s for parameter %d', type($actualCallable), $i),
                    $expectedCallable
                );
            }

            if ($expectedParameter->getType()->allowsNull() !== $actualParameter->getType()->allowsNull()) {
                $nullable = $expectedParameter->getType()->allowsNull() ? 'nullable' : 'non-nullable';
                throw new InvalidCallable(
                    sprintf('The callable %s must return a %s type.', type($actualCallable), $nullable),
                    $expectedCallable
                );
            }
        }


        // @todo Check type, including variance.
        // @todo Check default values.
        // @todo Check pass by references.
        // @todo Check nullability. Do we need to treat `?` and default values similarly? What matters for validyty
        // @todo
    }

    if (!$expectedCallable->hasReturnType() and $actualCallable->hasReturnType()) {
        throw new InvalidCallable(
            sprintf('The callable %s must not specify a return type.', signature($actualCallable)),
            $actualCallable
        );
    }

    if ($expectedCallable->hasReturnType()) {
        if (!$actualCallable->hasReturnType() or $expectedCallable->getReturnType()->getName() !== $actualCallable->getReturnType()->getName()) {
            throw new InvalidCallable(
                sprintf(
                    'The callable %s must specify return type %s.',
                    signature($actualCallable),
                    $expectedCallable->getReturnType()->getName()
                ),
                $expectedCallable
            );
        }

        if ($expectedCallable->getReturnType()->allowsNull() !== $actualCallable->getReturnType()->allowsNull()) {
            $nullable = $expectedCallable->getReturnType()->allowsNull() ? 'nullable' : 'non-nullable';
            throw new InvalidCallable(
                sprintf('The callable %s must return a %s type.', type($actualCallable), $nullable),
                $expectedCallable
            );
        }
    }
}
