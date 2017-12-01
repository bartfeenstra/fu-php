# Functional PHP

[![Build Status](https://travis-ci.org/bartfeenstra/fu-php.svg?branch=master)](https://travis-ci.org/bartfeenstra/fu-php) [![Coverage Status](https://coveralls.io/repos/github/bartfeenstra/fu-php/badge.svg?branch=master)](https://coveralls.io/github/bartfeenstra/fu-php?branch=master) [![License](https://poser.pugx.org/bartfeenstra/fu/license)](https://packagist.org/packages/bartfeenstra/fu) [![Latest Stable Version](https://poser.pugx.org/bartfeenstra/fu/v/stable)](https://packagist.org/packages/bartfeenstra/fu) [![Latest Unstable Version](https://poser.pugx.org/bartfeenstra/fu/v/unstable)](https://packagist.org/packages/bartfeenstra/fu) [![Total Downloads](https://poser.pugx.org/bartfeenstra/fu/downloads)](https://packagist.org/packages/bartfeenstra/fu)

This library provides tools to write more functional PHP code. Its concise and consistent API
makes you more productive in different ways:
 - Universal tools for [processing iterables](#iterators) like arrays.
 - Callback [generation](#predicates) and [modification](#partial-function-application) functions.
 - [Optional value types](#the-option-type) to aid with stronger typing and erorr handling.
 - Shorthand [exception handling](#exception-handling).

## Table of contents
1. [Installation](#installation)
1. [About](#about)
1. [Usage](#usage)
    1. [Iterators](#iterators)
    1. [Operations](#operations)
    1. [Exception handling](#exception-handling)
    1. [Predicates](#predicates)
    1. [The `Option` type](#the-option-type)
    1. [The `Result` type](#the-result-type)
    1. [Partial function application](#partial-function-application)
    1. [Currying](#currying)
1. [Contributing](#contributing)
1. [Development](#development)

## About
This library was written to address several concerns:
- Provide a single, consistent API to the different [iterable](http://php.net/manual/en/language.types.iterable.php)
  types in PHP, and the different [operations](#operations) available to the individual types: one API, any iterable,
  always associative, access to keys.
- Provide iterable processing operations that do not yet exist in PHP.
- Make writing closures quick and easy. [Predicate](#predicates) factories can be used to generate common (filter)
  conditions.
- Allow developers to create functions that easily distinguish between different function outputs using
  [optional value types](#the-option-type). These can be used to solve problems like with `json_decode()`, which returns
  `NULL` in case of an error, or when it successfully decodes the JSON string `null`. It is impossible to distinguish
  between the different outcomes without additional code, such as option types.
- Use native PHP features where possible for improved interoperability and performance. Naming and parameter order
  follow the predominant conventions in PHP. This means all iterators implement `\Iterator`, and many PHP core functions
  are used internally.
- Add laziness where possible, so many [operations](#operations) are only applied to the iterator items you actually
  use.

## Installation
Run `composer require bartfeenstra/fu` in your project's root directory.

## Usage
To use any of the code, you must first import the namespaces at the top of your files:
```php
<?php
use BartFeenstra\Functional as F;
use BartFeenstra\Functional\Iterable as I;
use BartFeenstra\Functional\Predicate as P;
use function BartFeenstra\Functional\Iterable\iter;
?>
```


### Iterators
Traversable/iterable data structures can be converted to universal iterators:
```php
<?php
// Arrays.
$iterator = iter([3, 1, 4]);

// \Traversable (includes native/Spl iterators).
$iterator = iter(new \ArrayIterator([3, 1, 4]));

// Callables that (return callables that...) return iterators.
$callable = function (){
  return function () {
    return iter([]);
  };
};
$iterator = iter($callable);

// Existing universal iterators are passed through.
$iterator = iter([]);
assert($iterator === iter($iterator));

// Objects can expose universal iterators as well.
$toIterator = new class() implements I\ToIterator {
  public function iter(): I\Iterator {
    return iter([]);
  }
};
$iterator = iter($toIterator);
?>
```

### Operations
The following operations work with iterator values, and even keys in the case of user-supplied callbacks:

#### each
Executes code for every value.
```php
<?php
$carrier = [];
$list = [3, 1, 4];
iter($list)->each(function (int $i) use (&$carrier) {
  $carrier[] = $i;
});
assert($list === $carrier);
?>
```

#### filter
Filters out values that do not match.
```php
<?php
$result = iter([3, 1, 4])->filter(P\gt(2));
assert([0 => 3, 2 => 4] === $result->toArray());
?>
```

#### find
Tries to find a single matching value.
```php
<?php
$found = iter([3, 1, 4, 1, 5, 9])->find(P\gt(4));
assert(new I\SomeItem(5, 4) == $found);
?>
```

#### map
Converts all values individually.
```php
<?php
$original = [3, 1, 4];
$expected = [9, 3, 12];
$result = iter($original)->map(function (int $i): int {
  return 3 * $i;
});
assert($expected === $result->toArray());
?>
```

#### mapKeys
Converts all keys individually.
```php
<?php
$original = [
    3 => 'c',
    1 => 'a',
    4 => 'd',
];
$expected = [
    9 => 'c',
    3 => 'a',
    12 => 'd',
];
$result = iter($original)->mapKeys(function (string $value, int $key): int {
  return 3 * $key;
});
assert($expected === $result->toArray());
?>
```

#### reduce
Combines all values into a single one.
```php
<?php
$list = [3, 1, 4];
$sum = iter($list)->reduce(function (int $sum, int $item): int {
  return $sum + $item;
});
assert(new F\SomeValue(8) == $sum);
?>
```
To terminate the reduction before all items have been processed, throw a `TerminateReduction` with the final carrier
value.

#### fold
Combines all values into a single one, with a default start value.
```php
<?php
$start = 2;
$list = [3, 1, 4];
$total = iter($list)->fold(function (int $total, int $item): int {
  return $total + $item;
}, $start);
assert(10 === $total);
?>
```
To terminate the fold before all items have been processed, throw a `TerminateFold` with the final carrier value.

#### take
Takes *n* values.
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = iter($list)->take(4);
assert([3, 1, 4, 1] === $result->toArray());
?>
```

#### takeWhile
Take as many consecutively matching values as possible from the beginning.
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = iter($list)->takeWhile(P\le(3));
assert([3, 1] === $result->toArray());
?>
```

#### slice
Slices the values into a smaller collection.
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = iter($list)->slice(2, 3);
assert([2 => 4, 3 => 1, 4 => 5] === $result->toArray());
?>
```

#### min
Gets the lowest value.
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$min = iter($list)->min();
assert(new F\SomeValue(1) == $min);
?>
```

#### max
Gets the highest value.
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$max = iter($list)->max();
assert(new F\SomeValue(9) == $max);
?>
```

#### sum
Sums all values.
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$sum = iter($list)->sum();
assert(new F\SomeValue(23) == $sum);
?>
```

#### forever
Infinitely repeats the set of values.
```php
<?php
$list = [3, 1, 4];
$iterator = iter($list)->forever();
$expected = [3, 1, 4, 3, 1, 4, 3];
assert($expected === iterator_to_array($iterator->take(7), false));
?>
```

#### zip
Combines the values of two or more iterables into [tuples](https://en.wikipedia.org/wiki/Tuple).
```php
<?php
$one = [3, 1, 4];
$two = [1, 5, 9];
$three = [2, 9, 2];
$zip = iter($one)->zip($two, $three);
$expected = [[3, 1, 2], [1, 5, 9], [4, 9, 2]];
assert($expected === iterator_to_array($zip));
?>
```

#### list
Converts all keys to integers, starting from 0.
```php
<?php
$array = [
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
];
$indexed = iter($array)->list();
$expected = ['A', 'B', 'C'];
assert($expected === iterator_to_array($indexed));
?>
```

#### listKeys
Uses keys as values, and indexes them from 0.
```php
<?php
$array = [
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
];
$keys = iter($array)->listKeys();
$expected = ['a', 'b', 'c'];
assert($expected === iterator_to_array($keys));
?>
```

#### flip
Swaps keys and values, similarly to [`array_flip()`](http://php.net/manual/en/function.array-flip.php).
```php
<?php
$array = [
    'a' => 3,
    'b' => 1,
    'c' => 4,
];
$flipped = iter($array)->flip();
$expected = [
    3 => 'a',
    1 => 'b',
    4 => 'c',
];
assert($expected === $flipped->toArray());
?>
```

#### reverse
Reverses the order of the values.
```php
<?php
$array = [3, 1, 4];
$reverse = iter($array)->reverse();
assert([4, 1, 3] === $reverse->toArray());
?>
```

#### first
Gets the first value.
```php
<?php
$array = [3, 1, 4, 1, 5, 9];
assert(new I\SomeItem(3, 0) == iter($array)->first());
?>
```

#### last
Gets the last value.
```php
<?php
$array = [3, 1, 4, 1, 5, 9];
assert(new I\SomeItem(9, 5) == iter($array)->last());
?>
```

#### empty
Checks if there are no values.
```php
<?php
assert(TRUE === iter([])->empty());
assert(FALSE === iter([3, 1, 4])->empty());

?>
```

#### sort
Sorts items by their values.
```php
<?php
$array = [
    3 => 'c',
    1 => 'a',
    4 => 'd',
];
// ::sort() also takes an optional custom comparison callable.
$sort = iter($array)->sort();
$expected = [
    1 => 'a',
    3 => 'c',
    4 => 'd',
];
assert($expected === iterator_to_array($sort));
?>
```

#### sortKeys
Sorts items by their keys.
```php
<?php
$array = [
    'c' => 3,
    'a' => 1,
    'd' => 4,
];
// ::sortKeys() also takes an optional custom comparison callable.
$sort = iter($array)->sortKeys();
$expected = [
    'a' => 1,
    'c' => 3,
    'd' => 4,
];
assert($expected === iterator_to_array($sort));
?>
```

#### chain
Chains other iterables to an existing iterator, and re-indexes the values.
```php
<?php
$arrayOne = [3, 1, 4];
$arrayTwo = [1, 5, 9];
$arrayThree = [2, 6, 5];
$iterator = iter($arrayOne)->chain($arrayTwo, $arrayThree);
$expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
assert($expected === $iterator->toArray());
?>
```

#### flatten
Flattens the iterables contained by an iterator into a single new iterator.
```php
<?php
$array = [
    [3, 1, 4],
    [1, 5, 9],
    [2, 6, 5],
];
$iterator = iter($array)->flatten();
$expected = [3, 1, 4, 1, 5, 9, 2, 6, 5];
assert($expected === $iterator->toArray());
?>
```

#### unique
Removes all duplicate values.
```php
<?php
$objectOne = new \stdClass();
$objectTwo = new \stdClass();
$array = [0, false, false, null, [], [], '0', $objectOne, $objectOne, $objectTwo];
$iterator = iter($array)->unique();
$expected = [
    0 => 0,
    1 => false,
    3 => null,
    4 => [],
    6 => '0',
    7 => $objectOne,
    9 => $objectTwo,
];
assert($expected === $iterator->toArray());
?>
```


### Exception handling
Complex `try`/`catch` blocks can be replaced and converted to [`Result`](#the-result-type) easily:
```php
<?php
// Try executing a callable, catch all exceptions, and output a Result.
$result = F\try_except(function () {/** ... */});

// Try executing a callable, catch all Foo, Bar, Baz, and Qux exceptions, and output a Result.
$result = F\try_except(function () {/** ... */}, Foo::class, Bar::class, Baz::class, Qux::class);

// Try executing a callable at most twice, catch all exceptions, and output a Result.
$result = F\retry_except(function () {/** ... */});

// Try executing a callable at most 5 times, catch all Foo, Bar, Baz, and Qux exceptions, and output a Result.
$result = F\retry_except(function () {/** ... */}, 5, Foo::class, Bar::class, Baz::class, Qux::class);
?>
```


### Predicates
Predicates can be used with [`filter()`](#filter) and [`find()`](#find). They can be any
[callable](http://php.net/manual/en/language.types.callable.php) that takes a
single parameter and returns a boolean, but we added some shortcuts for common
conditions. These functions take configuration parameters, and return
predicates.
```php
<?php
// All values strictly identical to TRUE.
$predicate = P\true();

// All values strictly identical to FALSE.
$predicate = P\false();

// All values that evaluate to TRUE.
$predicate = P\truthy();

// All values that evaluate to FALSE.
$predicate = P\falsy();

// All values strictly identical to 0.
$predicate = P\id(0);

// All values equal to "Apples and oranges".
$predicate = P\eq('Apples and oranges');

// All values greater than 9.
$predicate = P\gt(9);

// All values greater than or equal to 99.
$predicate = P\ge(99);

// All values lesser than 15.
$predicate = P\lt(15);

// All values lesser than or equal to 666.
$predicate = P\le(666);

// All values that are instances of Foo, Bar, Baz, or Qux.
$predicate = P\instance_of(Foo::class, Bar::class, Baz::class, Qux::class);

// One or more values are lesser than 0 OR greater than 9.
$predicate = P\any(P\lt(0), P\gt(9));

// All values are greater than 0 AND lesser than 9.
$predicate = P\all(P\gt(0), P\lt(9));

// All values different from "Apples and oranges".
$predicate = P\not(P\eq('Apples and oranges'));
?>
```

### The `Option` type
In PHP, `NULL` signifies the absence of a value, but it is also used as a value itself. In such cases, an `Option` type
helps to distinguish between `NULL` as a value, and no value at all.
```php
<?php
use BartFeenstra\Functional\Option;
use BartFeenstra\Functional\Some;
use BartFeenstra\Functional\SomeValue;
use BartFeenstra\Functional\None;
function get_option(): Option {
    if (true) {
        return new SomeValue(666);
    }
    return new None();
}
function handle_option(Option $value) {
    if ($value instanceof Some) {
        print sprintf('The value is %s.', $value());
    }
    // $value is an instance of None.
    else {
        print 'No value could be retrieved.';
    }
}
handle_option(get_option());
?>
```

### The `Result` type
The `Result` type can be used to [complement](#exception-handling) or replace exceptions. As such, it is returned by
functions like [`try_except()`](#exception-handling). It represents success and a value, or an error.
```php
<?php
use BartFeenstra\Functional\Ok;
use BartFeenstra\Functional\OkValue;
use BartFeenstra\Functional\Result;
use BartFeenstra\Functional\ThrowableError;
function get_result(): Result {
    try {
        // Do some things that may throw a ResultComputationException.
        return new OkValue(666);
    }
    catch (\ResultComputationException $e) {
        return new ThrowableError($e);
    }
}
function handle_result(Result $result) {
    if ($result instanceof Ok) {
        print sprintf('The value is %s.', $result());
    }
    // $value is an instance of Error.
    else {
        print sprintf('An error occurred: %s.', $result);
    }
}
handle_result(get_result());
?>
```

### Partial function application
[Partial function application](https://en.wikipedia.org/wiki/Partial_application) is the creation of a new function with
zero or more parameters, based on an existing function, by fixing one or more of the arguments of the original function,
before calling it. Practically speaking, it allows you to copy a function, and fill out some of the arguments before
calling it. You can use this to quickly transform existing functions into anonymous functions that can be used as
callbacks. In PHP, this is possible with any kind of [callable](http://php.net/manual/en/language.types.callable.php)
(functions, methods, closures, ...).

```php
<?php
$originalFunction = function (string $a, string $b, string $c, string $d): string {
    return $a . $b . $c . $d;
};

// Fix the two first/left-handed arguments.
$newFunction = F\apply_l($originalFunction, 'A', 'B');
assert('ABCD' === $newFunction('C', 'D'));

// Fix the two last/right-handed arguments.
$newFunction = F\apply_r($originalFunction, 'C', 'D');
assert('ABCD' === $newFunction('A', 'B'));

// Fix two arguments by index/in the middle.
$newFunction = F\apply_i($originalFunction, 1, 'B', 'C');
assert('ABCD' === $newFunction('A', 'D'));
?>
```

### Currying
[Currying](https://en.wikipedia.org/wiki/Currying) converts a single function with *n* parameters to *n* functions with
one parameter each. Practically speaking, it allows you to copy a function, and fill out some of the arguments one at a
time before calling it. You can use this to quickly transform existing functions into anonymous functions that can be
used as callbacks. In PHP, this is possible with any kind of
[callable](http://php.net/manual/en/language.types.callable.php) (functions, methods, closures, ...).

```php
<?php
$originalFunction = function (string $a, string $b, string $c, string $d = 'D'): string {
    return $a . $b . $c . $d;
};

assert('ABCD' === F\curry($originalFunction)('A')('B')('C'));
?>
```


## Contributing
Your involvement is more than welcome. Please
[leave feedback in an issue](https://github.com/bartfeenstra/fu-php/issues),
or [submit code improvements](https://github.com/bartfeenstra/fu-php/pulls)
through [pull requests](https://help.github.com/articles/about-pull-requests/).

The internet, and this project, is a place for all. We will keep it friendly
and productive, as documented in our [Code of Conduct](./CODE_OF_CONDUCT.md),
which also includes the project maintainers' contact details in case you want
to report a situation, on behalf of yourself or others.

## Development

### Building the code
Run `./bin/build`.

### Testing the code
Run `./bin/test`.

### Fixing the code
Run `./bin/fix` to fix what can be fixed automatically.

### Code style
All PHP code follows [PSR-2](http://www.php-fig.org/psr/psr-2/).

