<?php

namespace Develop;

use RuntimeException;

class DevTools
{

    private $path;


    public function __construct()
    {
        /*
         * Определяем уровни доступа, для разной cms свои,
         * Для вывода, только для авторизованного пользователя и администратора
         */
    }

    /**methods*/




    //DumpDev in file
    public function dumpFile($variable, $fileName = 'DumpDev.txt')
    {
        $path = $_SERVER['DOCUMENT_ROOT'].'/upload/DumpDev/';
        if (!mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
        $text = '=======================\r\n';
        $text .= print_r($variable, 1);
        $text .= '\r\n';
        file_put_contents($path.$fileName, $text, FILE_APPEND);
    }

    /**
     * Очищает строку от всех символов кроме ведущего плюса и цифр.
     *
     *
     */
    protected function clearPhone($string)
    {
        $result = '';
        if (!is_array($string) and !is_object($string)) {
            $result = preg_replace('/[^\+0-9+]/', '', $string);
        }
        return $result;
    }

    /**views*/

    /**
     * Очищает строку перед выводом, от html тегов
     * @param  string  $variable
     * @return string
     */
    protected function clearOutput($variable): string
    {
        return htmlspecialchars($variable);
    }
}