<?php

namespace Develop\Dump;

use Develop\DevTools;

abstract class Dump extends DevTools
{
    protected $oneLineDown = "\n";

    private $level = 1;
    private $limitLenghtString = 40;
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

    abstract protected function viewStart();
    abstract protected function view($variable);
    abstract protected function viewEnd();

    protected function dumpDefault($variable): void
    {
        $this->resetLevel();
        $this->setKey('');
        $this->viewStart();
        $this->view($variable);
        $this->viewEnd();
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
     * Проверяет является ли переменная массивом или объектом
     * @param $variable
     * @return bool
     */
    protected function checkSimple($variable): bool
    {
        return is_array($variable) || is_object($variable);
    }

    /**
     * Отображает строку, обрезая по пределу
     * @param  string  $string
     * @return string|string
     */
    private function getString($string): string
    {
        return strlen($string) >= $this->limitLenghtString ? substr($string, 0, $this->limitLenghtString).'…' : $string;
    }

    /** views */
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
     * Отображение перевода строки
     */
    protected function lineDown(): void
    {
        echo $this->getLineDown();
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
            echo $this->getString($variable);
        } else {
            echo $variable;
        }
    }

    /**
     * Отобразить ключ, если он установлен
     */
    protected function showKey(): void
    {
        $key = $this->getKey();
        if ($key !== '') {
            $this->showWrap('red', $key);
            echo ' : ';
        }
    }

    /** getters */
    /**
     * @return string
     */
    protected function getKey(): string
    {
        return $this->key;
    }

    /**
     * Возвращает стили, для отображения результата
     */
    protected function getStyle()
    {
        return $this->style['style'];
    }

    /**
     * @return string
     */
    protected function getLineDown(): string
    {
        return $this->oneLineDown;
    }

    /** setters */
    /**
     * @param  string  $key
     */
    protected function setKey($key): void
    {
        $this->key = strip_tags($key);
    }
}