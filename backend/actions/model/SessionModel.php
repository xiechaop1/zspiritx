<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\model;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class SessionModel extends Action
{

    
    public function run()
    {
        $sessionModelId = Net::post('id');
        if ($sessionModelId) {
            $model = \common\models\SessionModels::findOne($sessionModelId);
        } else {
            $model = new \common\models\SessionModels();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $sessionModel = \common\models\SessionModels::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($sessionModel) {
                        if ($sessionModel->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($sessionModel) {
                        $sessionModel->status = Common::STATUS_DELETED;
                        if ($sessionModel->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($sessionModel) {
                        $sessionModel->status = Common::STATUS_NORMAL;
                        if ($sessionModel->save()) {

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

        $searchModel = new \backend\models\SessionModels();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('session_model', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'sessionModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}