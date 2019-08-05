<?php

namespace Develop;

class DevTools
{
    public function dump($variable = null)
    {
        echo "<pre style='font-size:10px;
            position: relative;
            top: 0px;
            text-align:left;
            background: rgba(0, 120, 201, 0.15);
            border: 1px solid #888;
        '>";
        $this->filter_tilda_keys($variable);
        echo "</pre>";
    }

    private function wrap($color, $text)
    {
        return "<span style='color:" . $color . ";'>" . htmlspecialchars($text) . "</span>";
    }

    private function filter_tilda_keys(&$outputVariable)
    {
        static $level = 0;
        $tab = "    ";
        if (is_array($outputVariable) || is_object($outputVariable)) {
            if ($level === 0) {
                echo '(' . $this->wrap('magenta', gettype($outputVariable)) . ') ';
                echo 'count ' . count($outputVariable);
                echo "\n";
                $level++;
                if (is_object($outputVariable)) {
                    print_r($outputVariable);
                } else {
                    $this->filter_tilda_keys($outputVariable);
                }
            } else {
                foreach ($outputVariable as $key => $value) {
                    echo str_repeat($tab, $level) . '(' . wrap('magenta', gettype($value)) . ') ';
                    if (is_array($value) or is_object($value)) {
                        echo 'count ' . count((array)$value);
                        echo ' [' . wrap('red', $key) . "]\n";
                        if (!empty($value)) {
                            $level++;
                            if (is_object($value)) {
                                print_r($outputVariable);
                            } else {
                                $this->filter_tilda_keys($value);
                            }
                        }
                    } else {
                        echo '[' . wrap('black', $key) . "] = ";
                        $this->checksimpleVariable($value);
                    }
                }
                $level--;
                echo '';
            }
        } else {
            echo '(' . $this->wrap('magenta', gettype($outputVariable)) . ') ';
            $this->checksimpleVariable($outputVariable);
        }

    }

    private function checksimpleVariable($simpleVariable)
    {
        if (is_null($simpleVariable)) {
            echo 'null' . "\n";
        } elseif (empty($simpleVariable)) {
            echo "''" . "\n";
        } elseif (is_string($simpleVariable)) {
            $simpleVariable = htmlspecialchars($simpleVariable);
            echo (strlen($simpleVariable) < 40 ? $simpleVariable : substr($simpleVariable, 0, 40) . "…") . "\n";
        } else {
            echo $simpleVariable . "\n";
        }
    }


}