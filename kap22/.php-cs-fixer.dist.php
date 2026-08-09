<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

// Welche Dateien geprüft werden: alle *.php unter src/.
$finder = Finder::create()
    ->in(__DIR__ . '/src')
    ->name('*.php');

// Der PER Coding Style als Basis, dazu ein paar sinnvolle Ergänzungen.
return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PER-CS' => true,
        'array_indentation' => true,
        'whitespace_after_comma_in_array' => true,
        'class_attributes_separation' => [
            'elements' => ['method' => 'one', 'property' => 'one'],
        ],
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'single_quote' => true,
    ])
    ->setFinder($finder);
