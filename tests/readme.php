<?php

if ('1' !== ini_get('zend.assertions')) {
    throw new \RuntimeException('zend.assertions MUST be enabled for this test to run.');
}

$root = __DIR__ . '/..';

require_once $root . '/vendor/autoload.php';

require_once $root . '/README.md';
