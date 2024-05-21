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

class UserModelLoc extends Action
{

    public function run()
    {
        $userModelLocId = Net::post('id');
        if ($userModelLocId) {
            $model = \common\models\UserModelLoc::findOne($userModelLocId);
        } else {
            $model = new \common\models\UserModelLoc();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $userModel = \common\models\UserModelLoc::findOne($id);
            switch (Net::post('action')) {
//                case 'delete':
//                    if ($userModel) {
//                        if ($userModel->delete()) {
//                            Yii::$app->session->setFlash('success', '操作成功');
//                        } else {
//                            Yii::$app->session->setFlash('danger', '操作失败');
//                        }
//                    }
//                    break;
                case 'delete':
                    if ($userModel) {
                        $userModel->is_delete = Common::STATUS_DELETED;
                        if ($userModel->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($userModel) {
                        $userModel->is_delete = Common::STATUS_NORMAL;
                        if ($userModel->save()) {

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

        $searchModel = new \backend\models\UserModelLoc();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('user_model_loc', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userModelLoc'    => $model,
            'params'        => $_GET,
        ]);
    }
}