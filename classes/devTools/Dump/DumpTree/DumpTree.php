<?php

namespace Develop\Dump\DumpTree;

use Develop\Dump\Dump;

class DumpTree extends Dump
{
    private $margin = 10;
    private $openedTree;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Выводит массив в виде дерева
     *
     * @param  mixed - Массив или объект, который надо обойти
     * @param  boolean  $opened  - Раскрыть дерево элементов по-умолчанию или нет?
     *
     * @return void
     */
    public function dump($variable, $opened = true)
    {
        $this->setOpenedTree($opened);

        $this->resetLevel();
        $this->setKey('');
        echo '<div style="'.$this->getStyle().'">';
        $this->view($variable, $opened);
        echo '</div>';
    }
    /**
     * Рекурсивный метод для древовидного вывода (отображение)
     * @param $variable
     */
    private function view($variable)
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
}