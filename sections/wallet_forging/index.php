<?php
header('Access-Control-Allow-Origin: *');
?>
<section class="section" id="ajax_result">
    <div class="container">
        <h1 class="title">
            Wallet Forging ROI Calculator
        </h1>
        <div class="columns is-mobile">
            <div class="column is-4">
                <label for="total" class="label">Online wallets quantity</label>
                <input class="input" type="number" value="25000" id="total" name="total">
            </div>
            <div class="column is-4">
                <label for="own" class="label">Your wallets quantity</label>
                <input class="input" type="number" value="1" id="own" name="own">
            </div>
            <div class="column is-4">
                <label for="frozen_amount" class="label">Frozen amount to forging</label>
                <input class="input" id="frozen_amount" name="frozen_amount" readonly=“readonly”
                       type="number"
                       value="1">
            </div>
        </div>
        <div class="columns">
            <div class="column  is-1">
                <a id="calc" class="button is-primary">Calculate!</a>
            </div>
            <div class="column">
                <span id="wait" class="icon is-medium">
                        <i class="fas fa-2x  fa-spinner fa-pulse"></i>
                </span>
            </div>
        </div>
        <div class="table-container" id="table-container"></div>
    </div>
</section>