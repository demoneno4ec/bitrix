<?php
ini_set('display_errors', true);
error_reporting(-1);
require $_SERVER['DOCUMENT_ROOT'].'/classes/DevTools.php';
use Develop\DevTools;
$devTools = new DevTools();
//phpinfo();

$testVar = 1;
$testVar2 = 'string';
$testVar3 = new DateTime();
$testVar4 = [
    [
        0 => [
            'test' => $testVar2,
        ],
        'test2' => $testVar,
    ],
    [
        'test' => $testVar2
    ],
    [
        'test' => new DateTime(),
    ],
    'yes' => false
];

$devTools->dump($testVar);
$devTools->dump($testVar2);
$devTools->dump($testVar3);
$devTools->dump($testVar4);

$devTools->tree($testVar);
$devTools->tree($testVar2);
$devTools->tree($testVar3);
$devTools->tree($testVar4, false);
$devTools->tree($testVar4);
