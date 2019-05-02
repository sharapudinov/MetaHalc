<?php


namespace MetaHash\Calculator;

class WalletFoging
{
    const MIN_SCALE = 50;
    const RESULT_SCALE = 6;

    const NUMBER_OF_RANDOM_REWARD_WINNERS = 1000;
    const WALLET_FORGING_DEPOSIT_VALUE = 101;

    const DAILY_REWARD_POOL = 1940820;
    const REWARD_WIN_PLACES_DATA = [
        ['percent' => 5, 'winners' => 1],
        ['percent' => 1, 'winners' => 1],
        ['percent' => 0.5, 'winners' => 1],
        ['percent' => 0.4, 'winners' => 1],
        ['percent' => 0.35, 'winners' => 1],
        ['percent' => 0.01, 'winners' => 95],
        ['percent' => 0.002, 'winners' => 900]
    ];

    public static function randomRewardMatrix($total, $own)
    {
        bcscale(max(self::MIN_SCALE, $own));
        $total = (int)$total;
        $own = (int)$own;
        $total_profit = 0;
        $total_ROI = 0;
        $frozen_amount = self::WALLET_FORGING_DEPOSIT_VALUE*$own;
        foreach (self::REWARD_WIN_PLACES_DATA as $place => $place_data) {


            $p=bcdiv($place_data['winners'],$total);

            $opposite_probability = bcdiv(
                gmp_binomial($total - $own, $place_data['winners']),
                gmp_binomial($total, $place_data['winners'])
            );

            $probability = bcsub(
                1,
                $opposite_probability
            );

            $place_matrix[$place]['PROBABILITY'] = bcmul($probability, 100, self::RESULT_SCALE - 2);

            $place_matrix[$place]['AVG_DAILY_PROFIT'] = self::avgPlaceProfit($own, $place, $p);

            $place_matrix[$place]['PERIODICITY'] = bcdiv(1, $probability, 0);

            $daily_roi = bcdiv(
                $place_matrix[$place]['AVG_DAILY_PROFIT'],
                $frozen_amount
            );
            $place_matrix[$place]['DAILY_ROI'] = bcmul($daily_roi, 100, self::RESULT_SCALE - 2);
            $total_profit = bcadd($total_profit, $place_matrix[$place]['AVG_DAILY_PROFIT'], self::RESULT_SCALE);
            $total_ROI = bcadd($total_ROI, $place_matrix[$place]['DAILY_ROI'], self::RESULT_SCALE - 1);

        }

        $result['PLACE_MATRIX'] = $place_matrix;
        $total_opposite_probability = bcdiv(
            gmp_binomial($total - $own, self::NUMBER_OF_RANDOM_REWARD_WINNERS),
            gmp_binomial($total, self::NUMBER_OF_RANDOM_REWARD_WINNERS),
            );
        $total_probability = bcsub(1, $total_opposite_probability);
        $result['PLACE_MATRIX'][7] = [
            'PROBABILITY'      => bcmul($total_probability, 100, self::RESULT_SCALE - 2),
            'AVG_DAILY_PROFIT' => $total_profit,
            'PERIODICITY'      => bcdiv(1, $total_probability, 0),
            'DAILY_ROI'        => $total_ROI
        ];
        $result['FROZEN_AMOUNT'] = $frozen_amount;
        return $result;
    }

    public static function bernoulli($n, $k, $p)
    {
        $q = bcsub(1, $p);
        $coef = bcmul(bcpow($p, $k), bcpow($q, $n - $k));
        $result = bcmul(gmp_binomial($n, $k), $coef);
        return $result;
    }

    public static function avgPlaceProfit($own, $place, $probability)
    {
        $result = 0.0;
        $place_reward_amount = self::DAILY_REWARD_POOL * self::REWARD_WIN_PLACES_DATA[$place]['percent'] / 100;

        for ($k = 1; $k <= min($own, self::REWARD_WIN_PLACES_DATA[$place]['winners']); $k++) {
            $bernoulli = self::bernoulli($own, $k, $probability);
            $result = bcadd(
                bcmul(
                    $bernoulli,
                    $place_reward_amount * $k
                ),
                $result,
                self::RESULT_SCALE
            );
        }
        return $result;
    }
}

