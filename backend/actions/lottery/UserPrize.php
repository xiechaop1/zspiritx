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

class UserPrize extends Action
{

    
    public function run()
    {
        $userPrizeId = Net::post('id');
        if ($userPrizeId) {
            $model = \common\models\UserPrize::findOne($userPrizeId);
        } else {
            $model = new \common\models\UserPrize();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $userPrize = \common\models\UserPrize::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($userPrize) {
                        if ($userPrize->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'user_prize_rece':
                    if ($userPrize) {
                        $userPrize->user_prize_status = \common\models\UserPrize::USER_PRIZE_STATUS_RECEIVED;
                        $userPrize->save();
                    }
                    break;
                case 'user_prize_cancel':
                    if ($userPrize) {
                        $userPrize->user_prize_status = \common\models\UserPrize::USER_PRIZE_STATUS_CANCEL;
                        $userPrize->save();
                    }
                    break;
                case 'user_prize_wait':
                    if ($userPrize) {
                        $userPrize->user_prize_status = \common\models\UserPrize::USER_PRIZE_STATUS_WAIT;
                        $userPrize->save();
                    }
                    break;
                case 'reset':
                    if ($userPrize) {
                        $userPrize->is_delete = Common::STATUS_NORMAL;
                        if ($userPrize->save()) {

                        }
                    }
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

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                Yii::$app->session->setFlash('danger', '操作失败');
            }
            return $this->controller->refresh();
        }

        $searchModel = new \backend\models\UserPrize();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('user_prize', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userPrizeModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}