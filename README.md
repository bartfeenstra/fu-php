# Functional PHP

This library provides several tools to write more functional PHP.

## Installation
Run `composer require bartfeenstra/fu` in your project's root directory.

## Usage

### Iterators
Traversable/iterable data structures can be converted to a universal iterator:
```
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
}
?>
```

## [Operations](#operations).

### each
```
<?php
$carrier = [];
$list = [3, 1, 4];
F\iter($list)->each(function (int $i) use (&$carrier) {
  $carrier[] = $i;
});
assert($list === $carrier);
?>
```

### filter
```
<?php
$result = F\iter([3, 1, 4])->filter(F\gt(2));
assert([0 => 3, 2 => 4] === iterator_to_array($result));
?>
```

### map
```
<?php
$original = [3, 1, 4];
$expected = [9, 3, 12];
$result = F\iter($original)->map(function (int $i): int {
  return 3 * $i;
});
assert($expected === iterator_to_array($result));
?>
```

### reduce
```
<?php
$list = [3, 1, 4];
$sum = F\iter($list)->reduce(function (int $sum, int $item): int {
  return $sum + $item;
});
assert(8 === $sum);
?>
```

### fold
```
<?php
$start = 2;
$list = [3, 1, 4];
$total = F\iter($list)->fold(function (int $total, int $item): int {
  return $total + $item;
}, $start);
assert(10 === $total);
?>
```

### take
```
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = F\iter($list)->take(4);
assert([3, 1, 4, 1] === iterator_to_array($result));
?>
```

### takeWhile
```
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = F\iter($list)->takeWhile(F\le(3));
assert([3, 1] === iterator_to_array($result));
?>
```

### slice
```
<?php
$start = 2;
$list = [3, 1, 4, 1, 5, 9];
$result = F\iter($list)->slice(2, 3);
assert([2 => 4, 3 => 1, 4 => 5] === iterator_to_array($result));
?>
```

### min
```
<?php
$list = [3, 1, 4, 1, 5, 9];
$min = F\iter($list)->min();
assert(1 === $min);
?>
```

### max
```
<?php
$list = [3, 1, 4, 1, 5, 9];
$min = F\iter($list)->max();
assert(9 === $min);
?>
```

### sum
```
<?php
$list = [3, 1, 4, 1, 5, 9];
$sum = F\iter($list)->sum();
assert(23 === $sum);
?>
```

## [Predicates](#predicates).
Predicates can be used with `filter()`. These functions provide shortcuts for common conditions.
```
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
