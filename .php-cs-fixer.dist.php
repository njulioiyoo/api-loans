<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(
        [
            __DIR__ . '/config',
            __DIR__ . '/public',
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ],
    );

return (new PhpCsFixer\Config('CI'))
    ->setRules(['@PER-CS2.0' => true, '@PHP82Migration' => true])
    ->setCacheFile(__DIR__ . '/.php-cs-fixer/.php-cs-fixer.cache')
    ->setFinder($finder);
