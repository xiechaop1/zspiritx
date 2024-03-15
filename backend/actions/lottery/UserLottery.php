<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\lottery;


use common\definitions\Common;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class UserLottery extends Action
{

    
    public function run()
    {
        $userLotteryId = Net::post('id');
        if ($userLotteryId) {
            $model = \common\models\UserLottery::findOne($userLotteryId);
        } else {
            $model = new \common\models\UserLottery();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $userLottery = \common\models\UserLottery::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($userLottery) {
                        if ($userLottery->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'wait':
                    if ($userLottery) {
                        $userLottery->lottery_status = \common\models\UserLottery::USER_LOTTERY_STATUS_WAIT;
                        if ($userLottery->ct == 0) {
                            $userLottery->ct = 1;
                        }
                        $userLottery->save();
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                case 'used':
                    if ($userLottery) {
                        $userLottery->lottery_status = \common\models\UserLottery::USER_LOTTERY_STATUS_USED;
                        $userLottery->save();
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                case 'cancel':
                    if ($userLottery) {
                        $userLottery->lottery_status = \common\models\UserLottery::USER_LOTTERY_STATUS_CANCEL;
                        $userLottery->save();
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                case 'reset':
                    if ($userLottery) {
                        $userLottery->is_delete = Common::STATUS_NORMAL;
                        if ($userLottery->save()) {

                        }
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                case 'generate':
                    $userId = Net::post('user_id');
                    $storyId = Net::post('story_id');
                    $sessionId = Net::post('session_id');
                    $lotteryId = Net::post('lottery_id');
                    $ct = Net::post('ct');
                    $channelId = 0;
                    Yii::$app->lottery->generateLottery($userId, $storyId, $sessionId, $lotteryId, $channelId, $ct);
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                default:
                    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                    $model->load(Yii::$app->request->post());
                    return ActiveForm::validate($model);
            }

            return $this->controller->responseAjax(1, '');
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if (empty($model->lottery_no)) {
                $lotteryNo = \common\helpers\Common::generateNo('ZWT'
                    . $model->user_id
                    . \common\helpers\Common::generateFullNumber($model->session_id, 2)
                    . \common\helpers\Common::generateFullNumber($model->lottery_id, 2)
                    , Date('YmdH'), '', 1000, 9999);
                $model->lottery_no = $lotteryNo;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                Yii::$app->session->setFlash('danger', '操作失败');
            }
            return $this->controller->refresh();
        }

        $searchModel = new \backend\models\UserLottery();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('user_lottery', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userLotteryModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}