<?php

namespace Develop;

use RuntimeException;

class DevTools
{
    private static $oneLineDown = "\n";
    private $level = 1;
    private $tab = '    ';
    private $limitLenghtString = 40;
    private $openedTree;
    private $marginTree = 10;
    private $style = [
        'style' => 'font-size:10px;
            position: relative;
            top: 0px;
            text-align:left;
            background: rgba(0, 120, 201, 0.15);
            border: 1px solid #888;
        ',
    ];
    private $key;
    private $path;

    /**methods*/
    /**
     * Выводит массив в виде дерева
     *
     * @param  mixed - Массив или объект, который надо обойти
     * @param  boolean  $opened  - Раскрыть дерево элементов по-умолчанию или нет?
     *
     * @return void
     */
    public function tree($variable, $opened = true)
    {
        $this->setOpenedTree($opened);

        $this->resetLevel();
        $this->setKey(null);
        echo '<div style="'.$this->style['style'].'">';
        $this->viewTree($variable, $opened);
        echo '</div>';
    }

    /**
     * Выводит любую переменную, в удобочитаемом виде
     * @param  null  $variable
     */
    public function dump($variable = null)
    {
        $this->resetLevel();
        echo '<pre style="'.$this->style['style'].'">';
        $this->viewDump($variable);
        echo '</pre>';
    }

//dump in file
    public function dumpFile($variable, $fileName = 'dump.txt'){
        $path = $_SERVER['DOCUMENT_ROOT'].'/upload/dump/';
        if(!mkdir($path, 0775, true) && !is_dir($path)) {
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
    function clearPhone($string){
        $result = '';
        if(!is_array($string) and !is_object($string)){
            $result = preg_replace('/[^\+0-9+]/', '', $string);
        }
        return $result;
    }
    /**views*/
    /**
     * Рекурсивный метод для по уровневого вывода (отображение)
     * @param $variable
     */
    private function viewDump(&$variable)
    {
        $type = gettype($variable);
        $this->showWrap('magenta', $type);

        if (!$this->checkSimple($variable)) {
            $this->showSimple($variable);
            echo self::$oneLineDown;
        } else {
            echo 'count '.count($variable);
            echo self::$oneLineDown;
            foreach ($variable as $key => $value) {
                $this->showTab();
                $this->level++;
                $this->showKey($key);
                $this->viewDump($value);
            }
        }

        $this->level--;
    }

    /**
     * Рекурсивный метод для древовидного вывода (отображение)
     * @param $variable
     */
    private function viewTree($variable)
    {
        $type = gettype($variable);

        if (!$this->checkSimple($variable)) {

            echo '<div style="margin-left:'.$this->marginTree * $this->level.'px">';
            $this->showKey($this->key);
            $this->showWrap('magenta', $type);
            $this->showSimple($variable);
            echo '</div>';
            echo self::$oneLineDown;
        } else {
            echo '<details'.$this->getOpenedTree().' style="margin-left:'.$this->marginTree * $this->level.'px">';
            echo '<summary>';
            $opened = $this->getOpenedTree();
            $this->showKey($this->key);
            $this->showWrap('magenta', $type);
            echo 'count '.count($variable);

            echo '</summary>';
            foreach ($variable as $key => $value) {
                $this->level++;
                $this->setKey($key);
                $this->viewTree($value, $opened);
            }
            echo '</details>';
        }
        $this->level--;

    }

    /** show*/
    /**
     * Отображение табуляции, для уровней dump
     */
    private function showTab()
    {
        echo str_repeat($this->tab, $this->level);
    }

    /**
     * Отображает текст с нужным цветом
     * @param $color
     * @param $text
     */
    private function showWrap($color, $text)
    {
        echo ' (<span style="color:'.$color.';">'.$this->clearOuput($text).'</span>) ';
    }

    /**
     * Отображение примитива
     * @param $variable
     */
    private function showSimple($variable)
    {
        if ($variable === null) {
            echo 'null';
        } elseif (empty($variable)) {
            echo "''";
        } elseif (is_string($variable)) {
            $variable = $this->clearOuput($variable);
            echo $this->showString($variable);
        } else {
            echo $variable;
        }
    }

    /**
     * Отображает строку, обрезая по пределу
     * @param  string  $string
     * @return string|string
     */
    private function showString($string)
    {
        return strlen($string) >= $this->limitLenghtString ? substr($string, 0, $this->limitLenghtString).'…' : $string;
    }

    /**
     * Отобразить ключ, если он установлен
     * @param $key
     */
    private function showKey($key)
    {
        if (isset($key)) {
            $this->showWrap('red', $key);
            echo ' : ';
        }
    }

    /**
     * Проверяет является ли переменная массивом или объектом
     * @param $variable
     * @return bool
     */
    private function checkSimple($variable)
    {
        return is_array($variable) || is_object($variable);
    }

    /**
     * Очищает строку перед выводом, от html тегов
     * @param  string  $variable
     * @return string
     */
    private function clearOuput($variable)
    {
        return htmlspecialchars($variable);
    }


    /** increments*/
    /**
     * Сбрасывает уровень погружения для дампа
     */
    private function resetLevel()
    {
        $this->level = 1;
    }


    /** getters and setters*/
    /**
     * @return bool
     */
    public function getOpenedTree()
    {
        return $this->openedTree;
    }

    /**
     * @param  bool  $opened
     */
    public function setOpenedTree($opened)
    {
        $this->openedTree = $opened === true ? ' open' : false;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param  string  $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}