<?php

namespace Develop\Dump\DumpTree;

use Develop\Dump\Dump;

class DumpTree extends Dump
{
    private $margin = 10;
    private $openedTree;
    private static $_instance;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(): DumpTree
    {
        if (self::$_instance !== null){
            return self::$_instance;
        }

        return new self;
    }

    /**
     * Выводит массив в виде дерева
     *
     * @param  mixed - Массив или объект, который надо обойти
     * @param  boolean  $opened  - Раскрыть дерево элементов по-умолчанию или нет?
     *
     * @return void
     */
    public static function dump($variable = null, $opened = true): void
    {
        $dump = self::getInstance();
        $dump->setOpenedTree($opened);

        $dump->dumpDefault($variable);
    }

    protected function viewStart(): void
    {
        echo '<div style="'.$this->getStyle().'">';
    }
    protected function viewEnd(): void
    {
        echo '</div>';
    }
    /**
     * Рекурсивный метод для древовидного вывода (отображение)
     * @param $variable
     */
    protected function view($variable): void
    {
        $type = gettype($variable);

        if (!$this->checkSimple($variable)) {

            echo '<div style="margin-left:'.$this->margin * $this->getLevel().'px">';
            $this->showKey();
            $this->showWrap('magenta', $type);
            $this->showSimple($variable);
            echo '</div>';
            $this->lineDown();
        } else {
            echo '<details'.$this->getOpenedTree().' style="margin-left:'.$this->margin * $this->getLevel().'px">';
            echo '<summary>';
            $opened = $this->getOpenedTree();
            $this->showKey();
            $this->showWrap('magenta', $type);
            echo 'count '.count($variable);

            echo '</summary>';
            foreach ($variable as $key => $value) {
                $this->incrementLevel();
                $this->setKey($key);
                $this->view($value, $opened);
            }
            echo '</details>';
        }
        $this->decrementLevel();

    }

    /** getters and setters*/
    /**
     * @return string
     */
    public function getOpenedTree(): string
    {
        return $this->openedTree;
    }

    /**
     * @param  bool  $opened
     */
    public function setOpenedTree($opened): void
    {
        $this->openedTree = $opened === true ? ' open' : '';
    }
}