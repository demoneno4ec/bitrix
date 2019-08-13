<?php

namespace Develop\Dump;

use Develop\DevTools;

class Dump extends DevTools
{
    private $level = 1;
    private $limitLenghtString = 40;
    private $oneLineDown = "\n";
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

    public function __construct()
    {
        parent::__construct();
    }

    /** Уровень погружения*/
    /**
     * Сбрасывает уровень погружения для дампа
     */
    protected function resetLevel(): void
    {
        $this->level = 1;
    }

    /**
     * Увеличивает уровень погружения
     */
    protected function incrementLevel(): void
    {
        $this->level++;
    }

    /**
     * Уменьшает уровень погружения
     */
    protected function decrementLevel(): void
    {
        $this->level--;
    }

    /**
     * Возвращает текущий уровень погружения
     */
    protected function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Возвращает стили, для отображения результата
     */
    protected function getStyle()
    {
        return $this->style['style'];
    }

    /**
     * Отображение перевода строки
     */
    protected function lineDown(): void
    {
        echo $this->oneLineDown;
    }


    /**
     * Проверяет является ли переменная массивом или объектом
     * @param $variable
     * @return bool
     */
    protected function checkSimple($variable): bool
    {
        return is_array($variable) || is_object($variable);
    }

    /**
     * Отображает текст с нужным цветом
     * @param $color
     * @param $text
     */
    protected function showWrap($color, $text): void
    {
        echo ' (<span style="color:'.$color.';">'.$this->clearOutput($text).'</span>) ';
    }

    /**
     * Отображение примитива
     * @param $variable
     */
    protected function showSimple($variable): void
    {
        if ($variable === null) {
            echo 'null';
        } elseif (empty($variable)) {
            echo "''";
        } elseif (is_string($variable)) {
            $variable = $this->clearOutput($variable);
            echo $this->showString($variable);
        } else {
            echo $variable;
        }
    }

    /**
     * Отобразить ключ, если он установлен
     * @param $key
     */
    protected function showKey(): void
    {
        $key = $this->getKey();
        if ($key !== '') {
            $this->showWrap('red', $key);
            echo ' : ';
        }
    }

    /**
     * @return string
     */
    protected function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param  string  $key
     */
    protected function setKey($key): void
    {
        $this->key = strip_tags($key);
    }

    /**
     * Отображает строку, обрезая по пределу
     * @param  string  $string
     * @return string|string
     */
    private function showString($string): string
    {
        return strlen($string) >= $this->limitLenghtString ? substr($string, 0, $this->limitLenghtString).'…' : $string;
    }


}