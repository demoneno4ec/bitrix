<?php

namespace Develop\Dump\DumpFile;

use Develop\Dump\Dump;
use RuntimeException;

class DumpFile extends Dump
{
    protected $oneLineDown = "\r\n";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $variable
     * @param  string  $filename
     * @param  bool  $debug
     */
    public function dump($variable, $filename = 'dump.log', $debug = false): void
    {
        if (!is_dir($this->path) && !mkdir($this->path, 0775, true) && !is_dir($this->path)) {
            if($debug === true){
                throw new RuntimeException(sprintf('Directory "%s" was not created', $this->path));
            }
        }else{
            $text = $this->viewStart();
            $text .= $this->view($variable);
            $text .= $this->viewEnd();
            file_put_contents($this->path.$filename, $text, FILE_APPEND);
        }
    }

    protected function viewStart():string
    {
        return '======================='.$this->getLineDown();
    }

    protected function view($variable):string
    {
        return print_r($variable, 1);
    }

    protected function viewEnd():string
    {
        return $this->getLineDown();
    }
}