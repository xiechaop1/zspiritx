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
                        if ($userScore->deleve()) {

                        }
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                default:
                    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                    if ($isNew) {
                        $count = UserScore::find()
                            ->where(['mobile' => $model->mobile])
                            ->andFilterWhere(['is_delete' => Common::STATUS_NORMAL])
                            ->count();
                        if ($count > 0) {
                            Yii::$app->session->setFlash('danger', '手机号已存在');
                            return $this->controller->responseAjax(1, '');
                        }

//                        $count = UserScore::find()
//                            ->where(['user_name' => $model->user_name])
//                            ->andFilterWhere(['is_delete' => Common::STATUS_NORMAL])
//                            ->count();
//                        if ($count > 0) {
//                            Yii::$app->session->setFlash('danger', '用户已存在');
//                            return $this->controller->responseAjax(1, '');
//                        }
                    }
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

        return $this->controller->render('user_score', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userScoreModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}