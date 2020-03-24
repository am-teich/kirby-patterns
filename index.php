<?php

use Kirby\Patterns\Lab;

@include_once __DIR__ . '/vendor/autoload.php';
@require_once(__DIR__ . '/src/helpers.php');


Kirby::plugin('amteich/patterns', [
  'options' => [
    'enable' => true,
    'lock' => true,
    'error' => 'error',
    'path' => 'patterns',
    'title' => 'Pattern Lab',
    'includepaths' => [],
    'directory' => kirby()->roots()->site() . '/patterns',
    'preview.mode' => 'preview',
    'preview.background' => '#fff',
    'preview.css' => 'assets/index.css',
    'preview.js' => 'assets/index.js'
  ],
  'routes' => function() { return Lab::routes(); },
  'twigcomponents' => function () {
    return option('amteich.patterns.directory');
  },
]);
