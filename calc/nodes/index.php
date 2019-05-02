<?php
/**
 * Copyright (c) 2019.
 */

use \MetaWatch\Api;

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '600');




\Debug\Dump::toPre(Api::getNodes());
