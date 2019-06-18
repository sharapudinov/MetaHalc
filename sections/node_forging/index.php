<?php
header('Access-Control-Allow-Origin: *');
?>
<section class="hero content" id="ajax_result">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                Node Reward Sharing Tool
            </h1>
            <div class="columns">
                <div class="column is-5-desktop is-12-mobile">
                    <label for="total" class="label">Node wallet address</label>
                    <input class="input" type="text" placeholder="Enter node wallet public key" id="nodeAddress">
                </div>

                <div class="column is-2-desktop is-12-mobile">
                    <label for="lastRewardValue" class="label">Shared reward value</label>
                    <input class="input" type="text" value="" id="lastRewardValue" readonly>
                </div>
                <div class="column is-2-desktop is-12-mobile">
                    <label for="share_%" class="label">Share %</label>
                    <input class="input" type="number" value="80" id="share">
                </div>
                <div class="column is-3-desktop is-12-mobile">
                        <label for="calendar" class="label">Select date of reward</label>
                        <input name="calendar" class="input" type="date" id="date"">
                </div>
            </div>
            <div class="columns">
                <div class="column  is-1">
                    <a id="fetchDelegatorsList" class="button is-primary">Fetch!</a>
                </div>
                <div class="column">
                <span id="fetch-wait" class="icon is-medium">
                        <i class="fas fa-2x  fa-spinner fa-pulse"></i>
                </span>
                </div>
            </div>
            <div class="table-container" id="table-container"></div>

        </div>
    </div>
</section>

