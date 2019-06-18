<?php
/**
 * Copyright (c) 2019.
 */

namespace MetaHash\ChainParser;


use Debug\Dump;
use Metahash\Crypto;
use Metahash\Ecdsa;

class NodeParser
{
    private $crypto;
    private $nodeAddress;
    private $history = null;
    private $balance = null;
    private $rewardTimestamp=null;
    private $filteredHistory;

    /**
     * NodeParser constructor.
     * @param $nodeAddress
     * @param string $net
     */
    public function __construct($nodeAddress,\DateTime $date, string $net = 'main')
    {
        $this->nodeAddress = $nodeAddress;
        $this->crypto = new Crypto(new Ecdsa());
        $this->crypto->net = $net;
        $this->rewardTimestamp=$date->getTimestamp();
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        if (is_null($this->history)) {
            $fullHistory = $this->crypto->fetchFullHistory($this->nodeAddress);
            $this->history = $fullHistory['result'];
            if (is_null($this->balance))
                $this->balance = $fullHistory['balance'];
        }

        return $this->history;
    }

    public function getFilteredHistory(){
        if(isset($this->rewardTimestamp)){
            return array_filter(
                $this->getHistory(),
                function ($trx){
                    if($trx['timestamp']<$this->rewardTimestamp-86400)
                        return true;
                    else return false;
                }
            );
        } else return $this->getHistory();

    }

    /**
     * @return array
     */
    public function getBalance()
    {
        if (is_null($this->balance))
            $this->balance = $this->crypto->fetchBalance($this->nodeAddress);
        return $this->balance;
    }

    /**
     * @return array
     */
    public function fetchNodeDelegationHistory()
    {
        return array_filter(
            $this->getFilteredHistory(),
            function ($item) {
                if (key_exists('isDelegate', $item))
                    return $item['isDelegate'] == true;
                else return false;
            }
        );
    }

    /**
     * @return array
     */
    public function fetchNodeUndelegationHistory()
    {
        return array_filter(
            $this->getFilteredHistory(),
            function ($item) {
                return json_decode(hex2bin($item['data']), true)['method'] == 'undelegate';
            }
        );
    }

    /**
     * @return float
     */
    public function calcNodeDelegationFromAddress(string $delegatorAddress)
    {
        return array_reduce(
                $this->getFilteredHistory(),
                function ($carry, $item) use ($delegatorAddress) {
                    if ($delegatorAddress !== $item['from'] || !isset($item['delegate'])) return $carry;
                    $k = 0;
                    $method = json_decode(hex2bin($item['data']), true)['method'];
                    if ($method == 'undelegate') $k = -1;
                    if ($method == 'delegate') $k = 1;

                    return $carry + $item['delegate'] * $k;
                },
                0
            ) / 1000000;

    }

    /**
     * @return array
     */
    public function fetchCurrentNodeDelegatorsList()
    {
        $result['delegator_matrix'] = [];
        $total_sum = 0;
        $is_recorded=[];

        foreach ($this->getFilteredHistory() as $trx) {
            if (!isset($trx['delegate'])) continue;
            if (!isset($is_recorded[$trx['from']])) {
                $delegated = $this->calcNodeDelegationFromAddress($trx['from']);
                if ($delegated > 0) {
                    $is_recorded[$trx['from']]=true;
                    $result['delegator_matrix'][] = [
                        'address' => $trx['from'],
                        'delegated'=> $delegated
                    ];
                    $total_sum += $delegated;
                }
            }
        }
        $result['total_sum'] = $total_sum;

        return $result;
    }

    /**
     * @return array
     */
    public function fetchNodeRewardTrx()
    {
        foreach ($this->getHistory() as $trx) {
            if ($trx['intStatus'] == Parser::TX_STATE_FORGING_N && $trx['timestamp']<=$this->rewardTimestamp)
                return $trx;
        }

    }

}
