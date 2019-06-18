<?php
/**
 * Copyright (c) 2019.
 */

namespace MetaHash\ChainParser;

class Parser
{
    const TX_STATE_APPROVE = 1;    // block approve transaction
    const TX_STATE_ACCEPT = 20;   // transaction accepted (data & move)
    const TX_STATE_WRONG_DATA = 40;   // transaction not accepted (data method rejected)
    const TX_STATE_FORGING = 100;  // forging transaction
    const TX_STATE_FORGING_W = 101;  // wallet reward forging transaction
    const TX_STATE_FORGING_N = 102;  // node reward forging transaction
    const TX_STATE_FORGING_C = 103;  // coin reward forging transaction
    const TX_STATE_FORGING_R = 104;  // random reward forging transaction
    const TX_STATE_STATE = 200;  // state block transaction
    const TX_STATE_TECH_NODE_STAT = 4353; // node statistic (0x1101)

    const DAGGER_NODE_ADDRESS = '0x00a5cea95d916104a42d9f2e799155da56e7658f9bfc0e8b51';
}
