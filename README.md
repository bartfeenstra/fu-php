# Functional PHP

[![Build Status](https://travis-ci.org/bartfeenstra/fu-php.svg?branch=master)](https://travis-ci.org/bartfeenstra/fu-php)

This library provides several tools to write more functional PHP. Its main goal
is to make you more productive by providing universal iterators, and to make
your code shorter and more self-documenting at the same time.

Many of the features exist in PHP in one way or another, such as mapping,
filtering, and reductions. They are, however, inconsistent, sometimes verbose,
and different depending on the type of traversable data you are working with.
This library uses native PHP features where possible, wrapping them in a
unified API. Additionally, many of the operations are lazy, so they are only
applied to the iterator items you actually use.

## Table of contents
1. [Installation](#installation)
1. [Usage](#usage)
    1. [Iterators](#iterators)
    1. [Operations](#operations)
    1. [Predicates](#predicates)
1. [Contributing](#contributing)
1. [Development](#development)

## [Installation](#installation)
Run `composer require bartfeenstra/fu` in your project's root directory.

## [Usage](#usage)

### [Iterators](#iterators)
Traversable/iterable data structures can be converted to a universal iterator:
```php
<?php
use BartFeenstra\Functional as F;

// Arrays.
$iterator = F\iter([3, 1, 4]);

// \Traversable (includes native/Spl iterators).
$iterator = F\iter(new \ArrayIterator([3, 1, 4]));

// Callables that (return callables that...) return iterators.
$callable = function (){
  return function () {
    return F\iter([]);
  };
};
$iterator = F\iter($callable);

// Existing universal iterators are passed through.
$iterator = F\iter([]);
assert($iterator === F\iter($iterator));

// Objects can expose universal iterators as well.
$toIterator = new class() implements F\ToIterator {
  public function iter(): F\Iterator {
    return F\iter([]);
  }
};
$iterator = F\iter($toIterator);
?>
```

### [Operations](#operations)

#### each
```php
<?php
$carrier = [];
$list = [3, 1, 4];
F\iter($list)->each(function (int $i) use (&$carrier) {
  $carrier[] = $i;
});
assert($list === $carrier);
?>
```

#### filter
```php
<?php
$result = F\iter([3, 1, 4])->filter(F\gt(2));
assert([0 => 3, 2 => 4] === iterator_to_array($result));
?>
```

#### map
```php
<?php
$original = [3, 1, 4];
$expected = [9, 3, 12];
$result = F\iter($original)->map(function (int $i): int {
  return 3 * $i;
});
assert($expected === iterator_to_array($result));
?>
```

#### reduce
```php
<?php
$list = [3, 1, 4];
$sum = F\iter($list)->reduce(function (int $sum, int $item): int {
  return $sum + $item;
});
assert(8 === $sum);
?>
```

#### fold
```php
<?php
$start = 2;
$list = [3, 1, 4];
$total = F\iter($list)->fold(function (int $total, int $item): int {
  return $total + $item;
}, $start);
assert(10 === $total);
?>
```

#### take
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = F\iter($list)->take(4);
assert([3, 1, 4, 1] === iterator_to_array($result));
?>
```

#### takeWhile
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = F\iter($list)->takeWhile(F\le(3));
assert([3, 1] === iterator_to_array($result));
?>
```

#### slice
```php
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = F\iter($list)->slice(2, 3);
assert([2 => 4, 3 => 1, 4 => 5] === iterator_to_array($result));
?>
```

#### min
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$min = F\iter($list)->min();
assert(1 === $min);
?>
```

#### max
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$max = F\iter($list)->max();
assert(9 === $max);
?>
```

#### sum
```php
<?php
$list = [3, 1, 4, 1, 5, 9];
$sum = F\iter($list)->sum();
assert(23 === $sum);
?>
```

### [Predicates](#predicates)
Predicates can be used with `filter()`. They can be any
[callable](http://php.net/manual/en/language.types.callable.php) without
parameters and a boolean return value, but we added some shortcuts for common
conditions. These functions take configuration parameters, and return
predicates.
```php
<?php
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

