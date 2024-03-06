<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/8
 * Time: 10:49 AM
 */

namespace backend\controllers;


use common\models\Music;
use liyifei\base\controllers\ViewController;
use liyifei\base\helpers\Net;
use yii;

class LotteryController extends ViewController
{
    public function behaviors()
    {
        return yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['lottery', 'lottery_prize', 'lottery_prize_edit', 'edit', 'user_lottery'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return yii\helpers\ArrayHelper::merge(parent::actions(), [
            'lottery' => [
                'class' => 'backend\actions\lottery\Lottery',
            ],
            'lottery_prize' => [
                'class' => 'backend\actions\lottery\LotteryPrize',
            ],
            'lottery_prize_edit' => [
                'class' => 'backend\actions\lottery\LotteryPrizeEdit',
            ],
            'edit' => [
                'class' => 'backend\actions\lottery\Edit',
            ],
            'user_lottery' => [
                'class' => 'backend\actions\lottery\UserLottery',
            ],
        ]);
    }
}