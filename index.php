<?php

use Kirby\Patterns\Lab;

@include_once __DIR__ . '/vendor/autoload.php';
@require_once(__DIR__ . '/vendor/htmlawed/htmlawed.php');

Kirby::plugin('crealistiques/patterns', [
    'options' => [
        'enable' => true,
        'lock' => true,
        'error' => 'error',
        'path' => 'patterns',
        'title' => 'Kirby Pattern Lab',
        'directory' => kirby()->roots()->site() . '/patterns',
        'preview.mode' => 'preview',
        'preview.background' => '#21252b',
        'preview.css' => 'assets/css/index.css',
        'preview.js' => 'assets/js/index.js'
    ],
    'routes' => function() { return Lab::routes(); }
]);
