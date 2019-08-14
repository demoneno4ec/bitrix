<?php
namespace Develop;

class DevLoader
{
    private static $files = array (
        __DIR__  . '/DevTools.php',
        __DIR__  . '/Dump/Dump.php',
        __DIR__  . '/Dump/DumpDev/DumpDev.php',
        __DIR__  . '/Dump/DumpTree/DumpTree.php',
        __DIR__  . '/Dump/DumpFile/DumpFile.php',
    );

    public static function getLoader(): void
    {
        foreach (self::$files as $file){
            include $file;
        }
    }
}