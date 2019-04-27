<?php


namespace MetaHash;

class Calculator
{
    const SCALE = 6;

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
        bcscale(self::SCALE);
        $total = (int)$total;
        $own = (int)$own;
        $total_profit=0;
        $total_ROI=0;
        $frozen_amount=bcmul(self::WALLET_FORGING_DEPOSIT_VALUE,$own,0);

        foreach (self::REWARD_WIN_PLACES_DATA as $place => $place_data) {
            $opposite_probability = bcdiv(
                gmp_binomial($total - $own, $place_data['winners']),
                gmp_binomial($total, $place_data['winners'])
            );
            $probability=bcsub(
                1,
                $opposite_probability
            );
            $place_matrix[$place]['PROBABILITY'] = bcmul($probability,100,self::SCALE-2);

            $place_matrix[$place]['AVG_DAILY_PROFIT'] = bcmul(
                $probability,
                self::DAILY_REWARD_POOL * $place_data['percent'] / 100
            );

            $place_matrix[$place]['PERIODICITY']=bcdiv(1, $probability,0);

            $daili_roi=bcdiv(
                $place_matrix[$place]['AVG_DAILY_PROFIT'],
                $frozen_amount
            );
            $place_matrix[$place]['DAILY_ROI'] = bcmul($daili_roi,100,self::SCALE-2);
            $total_profit = bcadd($total_profit,$place_matrix[$place]['AVG_DAILY_PROFIT']);
            $total_ROI=bcadd($total_ROI,$place_matrix[$place]['DAILY_ROI'],self::SCALE-1);

        }
        $result['PLACE_MATRIX']=$place_matrix;
        $total_opposite_probability = bcdiv(
            gmp_binomial($total - $own, self::NUMBER_OF_RANDOM_REWARD_WINNERS),
            gmp_binomial($total, self::NUMBER_OF_RANDOM_REWARD_WINNERS),
        );
        $total_probability= bcsub(1,$total_opposite_probability);
        $result['PLACE_MATRIX'][7]=[
            'PROBABILITY' =>bcmul($total_probability,100,self::SCALE-2),
            'AVG_DAILY_PROFIT'=> $total_profit,
            'PERIODICITY' => bcdiv(1, $total_probability,0),
            'DAILY_ROI'=>$total_ROI
        ];
        $result['FROZEN_AMOUNT']=$frozen_amount;
        return $result;

    }
}

