<?php

namespace Develop;


abstract class DevTools
{
    // Путь, для каждой CMS устанавливается свой.
    protected $path = __DIR__.'/';


    public function __construct()
    {
        /*
         * Определяем уровни доступа, для разной cms свои,
         * Для вывода, только для авторизованного пользователя и администратора
         */
    }

    /**methods*/
    /**
     * Очищает строку от всех символов кроме ведущего плюса и цифр.
     * @param $string
     * @return string
     */
    protected function clearPhone($string): string
    {
        $result = '';

        if (!is_array($string) && !is_object($string)) {
            $string = strip_tags($string);
            $result = preg_replace_callback(
                '/[^\+0-9+]/',
                static function (){return '';},
                $string
            );
        }

        return $result ?? '';
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