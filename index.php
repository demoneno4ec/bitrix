<?php
ini_set('display_errors', true);
error_reporting(-1);

require __DIR__.'/classes/autoload.php';

use Develop\Dump\DumpDev\DumpDev;
use Develop\Dump\DumpFile\DumpFile;
use Develop\Dump\DumpTree\DumpTree;

$dumpTree = new DumpTree();
$dumpFile = new DumpFile();
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
$dumpFile->dump($testVar4);

DumpTree::dump($testVar);
DumpTree::dump($testVar2);
DumpTree::dump($testVar3);
DumpTree::dump($testVar4, false);
DumpTree::dump($testVar4);
