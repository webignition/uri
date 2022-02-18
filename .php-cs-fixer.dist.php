<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
//    ->in(__DIR__ . '/bin')/
//    ->in(__DIR__ . '/public')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'types_spaces' => false,
        'trailing_comma_in_multiline' => false,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'declare_strict_types' => true,
//        'blank_line_before_statement' => [
//            'statements' => [
//                'break',
////                'case',
//                'continue',
//                'declare',
//                'default',
//                'phpdoc',
//                'do',
//                'exit',
//                'for',
////                'foreach',
//                'goto',
////                'if',
//                'include',
//                'include_once',
//                'require',
//                'require_once',
//                'return',
//                'switch',
//                'throw',
//                'try',
//                'while',
//                'yield',
//                'yield_from',
//            ],
//        ],
    ])
    ->setFinder($finder)
    ;
