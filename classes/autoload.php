<?php

require_once __DIR__.'/devTools/DevLoader.php';

use Develop\DevLoader;
spl_autoload_register(array('Develop\DevLoader', 'getLoader'), true, true);