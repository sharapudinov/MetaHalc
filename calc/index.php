<?php
header('Access-Control-Allow-Origin: *');
use MetaHash\Calculator;

require_once dirname(__DIR__).'/vendor/autoload.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '512M');


echo json_encode(Calculator::randomRewardMatrix($_REQUEST['total'],$_REQUEST['own']));

?>