<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\lottery;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Lottery extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 0;

        $lottery = \common\models\Lottery::find()
            ->where([
                'id'    => $lotteryId
            ])
            ->one();

        if (empty($lottery)) {
            throw new NotFoundHttpException('Lottery not found');
        }

        $lotteryPrize = LotteryPrize::find()
            ->where([
                'lottery_id'    => $lotteryId
            ])
            ->all();

        if (!empty($lotteryPrize)) {
            foreach ($lotteryPrize as $prize) {

            }

        }

//        $qaOne = $qaOne->toArray();
//        $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
//        $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);



        return $this->controller->render('puzzle', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'qaId'          => $qaId,
            'storyId'       => $qaOne['story_id'],
            'qa'         => $qaOne,
        ]);
    }
}