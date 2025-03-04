<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'dist',
        'resources',
        'vendor',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => true,
        "array_indentation" => true,
        "explicit_indirect_variable" => true,
        "explicit_string_variable" => true,
        "method_chaining_indentation" => true,
        "no_unused_imports" => true,
        "single_quote" => true,
        "trailing_comma_in_multiline" => true,
        "phpdoc_line_span" => true,
    ])
    ->setFinder($finder)
;
