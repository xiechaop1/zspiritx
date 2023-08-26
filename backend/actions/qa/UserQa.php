<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\qa;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class UserQa extends Action
{

    
    public function run()
    {
        $userQaId = Net::post('id');
        if ($userQaId) {
            $model = \common\models\UserQa::findOne($userQaId);
        } else {
            $model = new \common\models\UserQa();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $UserQa = \common\models\UserQa::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($UserQa) {
                        if ($UserQa->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($UserQa) {
                        $UserQa->is_delete = Common::STATUS_NORMAL;
                        if ($UserQa->save()) {

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

        $searchModel = new \backend\models\UserQa();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('user_qa', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userQaModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}