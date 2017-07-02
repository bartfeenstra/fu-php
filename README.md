# Functional PHP

[![Build Status](https://travis-ci.org/bartfeenstra/fu-php.svg?branch=master)](https://travis-ci.org/bartfeenstra/fu-php)

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
1. [Contributing](#contributing)
1. [Development](#development)

## [About](#about)
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

## [Installation](#installation)
Run `composer require bartfeenstra/fu` in your project's root directory.

## [Usage](#usage)
To use any of the code, you must first import the namespaces at the top of your files:
```php
<?php
use BartFeenstra\Functional as F;
use function BartFeenstra\Functional\iter;
?>
```


### [Iterators](#iterators)
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
$toIterator = new class() implements F\ToIterator {
  public function iter(): F\Iterator {
    return iter([]);
  }
};
$iterator = iter($toIterator);
?>
```

### [Operations](#operations)
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
$result = iter([3, 1, 4])->filter(F\gt(2));
assert([0 => 3, 2 => 4] === iterator_to_array($result));
?>
```

#### find
Tries to find a single matching value.
```php
<?php
$found = iter([3, 1, 4, 1, 5, 9])->find(F\gt(4));
assert(new F\SomeValue(5) == $found);
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
assert($expected === iterator_to_array($result));
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
assert(8 === $sum);
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
assert([3, 1, 4, 1] === iterator_to_array($result));
?>
```

#### takeWhile
Take as many consecutively matching values as possible from the beginning.
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = iter($list)->takeWhile(F\le(3));
assert([3, 1] === iterator_to_array($result));
?>
```

#### slice
Slices the values into a smaller collection.
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = iter($list)->slice(2, 3);
assert([2 => 4, 3 => 1, 4 => 5] === iterator_to_array($result));
?>
```

#### min
Gets the lowest value.
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$min = iter($list)->min();
assert(1 === $min);
?>
```

#### max
Gets the highest value.
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$max = iter($list)->max();
assert(9 === $max);
?>
```

#### sum
Sums all values.
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$sum = iter($list)->sum();
assert(23 === $sum);
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

#### keys
Uses keys as values, and the new keys are indexes.
```php
<?php
$array = [
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
];
$keys = iter($array)->keys();
$expected = ['a', 'b', 'c'];
assert($expected === iterator_to_array($keys));
?>
```

#### indexed
Converts all keys to integers, starting from 0.
```php
<?php
$array = [
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
];
$indexed = iter($array)->indexed();
$expected = ['A', 'B', 'C'];
assert($expected === iterator_to_array($indexed));
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
assert($expected === iterator_to_array($flipped));
?>
```

#### first
Gets the first value.
```php
<?php
$array = [3, 1, 4, 1, 5, 9];
assert(new F\SomeValue(3) == iter($array)->first());
?>
```

#### last
Gets the last value.
```php
<?php
$array = [3, 1, 4, 1, 5, 9];
assert(new F\SomeValue(9) == iter($array)->last());
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

### [Exception handling](#exceptions)
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


### [Predicates](#predicates)
Predicates can be used with [`filter()`](#filter) and [`find()`](#find). They can be any
[callable](http://php.net/manual/en/language.types.callable.php) that takes a
single parameter and returns a boolean, but we added some shortcuts for common
conditions. These functions take configuration parameters, and return
predicates.
```php
<?php
// All values strictly identical to TRUE.
$predicate = F\true();

// All values strictly identical to FALSE.
$predicate = F\false();

// All values that evaluate to TRUE.
$predicate = F\truthy();

// All values that evaluate to FALSE.
$predicate = F\falsy();

// All values strictly identical to 0.
$predicate = F\id(0);

// All values equal to "Apples and oranges".
$predicate = F\eq('Apples and oranges');

// All values greater than 9.
$predicate = F\gt(9);

// All values greater than or equal to 99.
$predicate = F\ge(99);

// All values lesser than 15.
$predicate = F\lt(15);

// All values lesser than or equal to 666.
$predicate = F\le(666);

// All values that are instances of Foo, Bar, Baz, or Qux.
$predicate = F\instance_of(Foo::class, Bar::class, Baz::class, Qux::class);

// One or more values are lesser than 0 OR greater than 9.
$predicate = F\any(F\lt(0), F\gt(9));
?>
```

### [The `Option` type](#the-option-type)
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

### [The `Result` type](#the-result-type)
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

### [Partial function application](#partial-function-application)
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


## [Contributing](#contributing)
Your involvement is more than welcome. Please
[leave feedback in an issue](https://github.com/bartfeenstra/fu-php/issues),
or [submit code improvements](https://github.com/bartfeenstra/fu-php/pulls)
through [pull requests](https://help.github.com/articles/about-pull-requests/).

The internet, and this project, is a place for all. We will keep it friendly
and productive, as documented in our [Code of Conduct](./CODE_OF_CONDUCT.md),
which also includes the project maintainers' contact details in case you want
to report a situation, on behalf of yourself or others.

## [Development](#development)

### Building the code
Run `./bin/build`.

### Testing the code
Run `./bin/test`.

### Fixing the code
Run `./bin/fix` to fix what can be fixed automatically.

### Code style
All PHP code follows [PSR-2](http://www.php-fig.org/psr/psr-2/).

