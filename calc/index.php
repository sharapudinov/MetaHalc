<?php
header('Access-Control-Allow-Origin: *');
use MetaHash\Calculator\WalletFoging;

require_once dirname(__DIR__).'/vendor/autoload.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '600');


$total=isset($_REQUEST['total'])?$_REQUEST['total']:'25000';
$own=isset($_REQUEST['own'])?$_REQUEST['own']:'50';


echo json_encode(WalletFoging::randomRewardMatrix($total,$own));

?>