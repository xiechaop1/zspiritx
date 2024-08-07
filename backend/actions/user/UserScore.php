<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\user;


use common\definitions\Common;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class UserScore extends Action
{
    public function run()
    {
        $userScoreId = Net::post('data-id');
        if ($userScoreId) {
            $model = \backend\models\UserScore::findOne($userScoreId);
            $isNew = false;
        }

        if (empty($model)) {
            $model = new \backend\models\UserScore();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $userScore = \backend\models\UserScore::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($userScore) {
                        if ($userScore->delete()) {

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

        $searchModel = new \backend\models\UserScore();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $stories = [0 => '无'] + ArrayHelper::map(\common\models\Story::find()->orderBy('id desc')->all(), 'id', 'title');

        return $this->controller->render('user_score', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userScoreModel'    => $model,
            'stories'      => $stories,
            'params'        => $_GET,
        ]);
    }
}