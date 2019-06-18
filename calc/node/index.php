<?php
/**
 * Copyright (c) 2019.
 */
header('Access-Control-Allow-Origin: *');

use \MetaWatch\Api;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '1G');
ini_set('max_execution_time', '600');

$nodeAddress = isset($_REQUEST['address']) ? $_REQUEST['address'] : '0x00a5cea95d916104a42d9f2e799155da56e7658f9bfc0e8b51';
$date =$date = new DateTime($_REQUEST['date']);


$parser = new \MetaHash\ChainParser\NodeParser($nodeAddress,$date);

$result = $parser->fetchCurrentNodeDelegatorsList();

$result['last_reward'] = $parser->fetchNodeRewardTrx()['value'] / 1000000;
$result['balance'] = $parser->getBalance();

echo json_encode($result);
