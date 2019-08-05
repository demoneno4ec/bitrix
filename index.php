<?php
ini_set('display_errors', true);
error_reporting(-1);
require $_SERVER['DOCUMENT_ROOT'].'/classes/DevTools.php';
use Develop\DevTools;
$devTools = new DevTools();
//phpinfo();
$devTools->dump();
