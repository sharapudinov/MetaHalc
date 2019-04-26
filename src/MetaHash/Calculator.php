<?php


namespace MetaHash;

class Calculator
{
    const SCALE = 10;

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
        $total = (int)$total;
        $own = (int)$own;
        $total_profit=0;
        $total_ROI=0;
        $frozen_amount=bcmul(self::WALLET_FORGING_DEPOSIT_VALUE,$own);

        foreach (self::REWARD_WIN_PLACES_DATA as $place => $place_data) {
            $opposite_probability = bcdiv(
                gmp_binomial($total - $own, $place_data['winners']),
                gmp_binomial($total, $place_data['winners']),
                self::SCALE
            );
            $probability=bcsub(
                1,
                $opposite_probability,
                self::SCALE
            );
            $place_matrix[$place]['PROBABILITY'] = bcmul($probability,100,self::SCALE);

            $place_matrix[$place]['AVG_DAILY_PROFIT'] = bcmul(
                $probability,
                self::DAILY_REWARD_POOL * $place_data['percent'] / 100,
                self::SCALE
            );

            $place_matrix[$place]['PERIODICITY']=bcdiv(1, $probability);

            $daili_roi=bcdiv(
                $place_matrix[$place]['AVG_DAILY_PROFIT'],
                $frozen_amount,
                self::SCALE
            );
            $place_matrix[$place]['DAILY_ROI'] = bcmul($daili_roi,100,self::SCALE);
            $total_profit = bcadd($total_profit,$place_matrix[$place]['AVG_DAILY_PROFIT'],self::SCALE);
            $total_ROI=bcadd($total_ROI,$place_matrix[$place]['DAILY_ROI'],self::SCALE);

        }
        $result['PLACE_MATRIX']=$place_matrix;
        $total_opposite_probability = bcdiv(
            gmp_binomial($total - $own, self::NUMBER_OF_RANDOM_REWARD_WINNERS),
            gmp_binomial($total, self::NUMBER_OF_RANDOM_REWARD_WINNERS),
            self::SCALE
        );
        $total_probability= bcsub(1,$total_opposite_probability,self::SCALE);
        $result['PLACE_MATRIX'][7]=[
            'PROBABILITY' =>bcmul($total_probability,100,self::SCALE),
            'AVG_DAILY_PROFIT'=> $total_profit,
            'PERIODICITY' => bcdiv(1, $total_probability),
            'DAILY_ROI'=>$total_ROI
        ];
        $result['FROZEN_AMOUNT']=$frozen_amount;
        return $result;

    }
}

