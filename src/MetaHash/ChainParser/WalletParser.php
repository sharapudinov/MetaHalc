<?php
/**
 * Copyright (c) 2019.
 */

namespace MetaHash\ChainParser;


use Metahash\Crypto;
use Metahash\Ecdsa;
use MetaHash\ChainParser\Parser;
use function PHPSTORM_META\type;


class WalletParser extends Parser
{

    private $last;




    public function fetchLast()
    {
        if (!isset($this->last)) {
            $history = $this->crypto->fetchHistory(DAGGER_NODE_ADDRESS);

            foreach ($history['result'] as $trx) {
                if ($trx['type'] == 'forging') {
                    $blockNumber = $trx['blockNumber'];
                    break;
                }
            }
            $this->last = $this->crypto->getBlockByNumber($blockNumber, 2);
        }
        return $this->last;
    }

    public function fetchRandomList()
    {
        return array_filter(
            $this->fetchLast()['result']['txs'], function ($item) {
            return $item['intStatus'] == TX_STATE_FORGING_R;
        }
        );
    }

    public function fetchWalletRewardList()
    {
        return array_filter(
            $this->fetchLast()['result']['txs'], function ($item) {
            return $item['intStatus'] == TX_STATE_FORGING_W;
        }
        );
    }

    public function getFirstPlace()
    {
        foreach ($this->fetchLast()['result']['txs'] as $trx) {
            if ($trx['value'] == '77632882793') {
                return $trx['to'];
            }
        }
    }



}
