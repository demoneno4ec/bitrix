<?php

namespace Develop;

class DevTools
{
    private $level = 0;
    private $style = [
        'style' => 'font-size:10px;
            position: relative;
            top: 0px;
            text-align:left;
            background: rgba(0, 120, 201, 0.15);
            border: 1px solid #888;
        ',
    ];

    public function dump($variable = null)
    {
        echo "<pre style='".$this->style['style']."'>";
        $this->filter_tilda_keys($variable, true);
        echo '</pre>';
    }


    private function wrap($color, $text)
    {
        return '(<span style="color:'.$color.';">'.htmlspecialchars($text).'</span>)';
    }

    private function filter_tilda_keys(&$outputVariable, $resetLevel = false)
    {
        $tab = '    ';
        $type = gettype($outputVariable);

        echo $this->wrap('magenta', $type);

        if (is_array($outputVariable) || is_object($outputVariable)) {
            echo 'count '.count($outputVariable);
            echo "\n";
            foreach ($outputVariable as $key => $value) {
                echo str_repeat($tab, $this->level);

                $this->level++;
                echo $this->wrap('red', $key);
                echo ' => ';
                $this->filter_tilda_keys($value);
            }
        } else {
            $this->showSimple($outputVariable);
            echo "\n";
        }

        if ($resetLevel === true) {
            $this->level = 1;
        } else {
            $this->level--;
        }
    }

    private function showSimple($simpleVariable)
    {
        if (is_null($simpleVariable)) {
            echo 'null';
        } elseif (empty($simpleVariable)) {
            echo "''";
        } elseif (is_string($simpleVariable)) {
            $simpleVariable = htmlspecialchars($simpleVariable);
            echo(strlen($simpleVariable) < 40 ? $simpleVariable : substr($simpleVariable, 0, 40).'…');
        } else {
            echo $simpleVariable;
        }
    }



    /**
     * Выводит массив в виде дерева
     *
     * @param  mixed - Массив или объект, который надо обойти
     * @param  boolean - Раскрыть дерево элементов по-умолчанию или нет?
     *
     * @return void
     */
    public function pretty_print($in, $opened = true)
    {
        if ($opened) {
            $opened = ' open';
        }
        if (is_object($in) || is_array($in)) {
            echo '<div style="'.$this->style['style'].'">';
            echo '<details'.$opened.'>';
            echo '<summary>';
            echo is_object($in) ? 'Object {'.count((array) $in).'}' : 'Array ['.count($in).']';
            echo '</summary>';
            $this->pretty_print_rec($in, $opened);
            echo '</details>';
            echo '</div>';
        }
    }

    private function pretty_print_rec($in, $opened, $margin = 10)
    {
        if (!is_object($in) && !is_array($in)) {
            return;
        }

        foreach ($in as $key => $value) {
            if (is_object($value) || is_array($value)) {
                echo '<details style="margin-left:'.$margin.'px" '.$opened.'>';
                echo '<summary>';
                echo is_object($value) ? $key.' {'.count((array) $value).'}' : $key.' ['.count($value).']';
                echo '</summary>';
                $this->pretty_print_rec($value, $opened, $margin + 10);
                echo '</details>';
            } else {
                switch (gettype($value)) {
                    case 'string':
                        $bgc = 'red';
                        break;
                    case 'integer':
                        $bgc = 'green';
                        break;
                }
                echo '<div style="margin-left:'.$margin.'px">'.$key.' : <span style="color:'.$bgc.'">'.$value.'</span> ('.gettype($value).')</div>';
            }
        }
    }


}