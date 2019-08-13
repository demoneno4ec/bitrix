<?php
ini_set('display_errors', true);
error_reporting(-1);

require __DIR__.'/classes/autoload.php';

use Develop\Dump\DumpDev\DumpDev;
use Develop\Dump\DumpTree\DumpTree;

$dumpTree = new DumpTree();
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

DumpDev::dump($testVar);
DumpDev::dump($testVar2);
DumpDev::dump($testVar3);
DumpDev::dump($testVar4);

$dumpTree->dump($testVar);
$dumpTree->dump($testVar2);
$dumpTree->dump($testVar3);
$dumpTree->dump($testVar4, false);
$dumpTree->dump($testVar4);
