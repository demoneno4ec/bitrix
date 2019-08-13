<?php

namespace Develop\Dump\DumpDev;

use Develop\Dump\Dump;

class DumpDev extends Dump
{
    private $tab = '    ';
    private static $_instance;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(): DumpDev
    {
        if (self::$_instance !== null){
            return self::$_instance;
        }

        return new self;
    }

    /**
     * Выводит любую переменную, в удобочитаемом виде
     * @param  null  $variable
     */
    public static function dump($variable = null): void
    {
        $dump = self::getInstance();
        $dump->resetLevel();
        $dump->setKey(null);
        echo '<pre style="'.$dump->getStyle().'">';
        $dump->view($variable);
        echo '</pre>';
    }
    /**
     * Рекурсивный метод для по уровневого вывода (отображение)
     * @param $variable
     */
    private function view(&$variable): void
    {
        $type = gettype($variable);
        $this->showWrap('magenta', $type);

        if (!$this->checkSimple($variable)) {
            $this->showSimple($variable);
            $this->lineDown();
        } else {
            echo 'count '.count($variable);
            $this->lineDown();
            foreach ($variable as $key => $value) {
                $this->showTab();
                $this->incrementLevel();
                $this->setKey($key);
                $this->showKey();
                $this->view($value);
            }
        }

        $this->decrementLevel();
    }

    /** show*/
    /**
     * Отображение табуляции, для уровней DumpDev
     */
    private function showTab(): void
    {
        echo str_repeat($this->tab, $this->getLevel());
    }
}