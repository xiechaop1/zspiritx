<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\knowledge;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class UserKnowledge extends Action
{

    
    public function run()
    {
        $userKnowledgeId = Net::post('id');
        if ($userKnowledgeId) {
            $model = \common\models\UserKnowledge::findOne($userKnowledgeId);
        } else {
            $model = new \common\models\UserKnowledge();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $UserKnowledge = \common\models\UserKnowledge::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($UserKnowledge) {
                        if ($UserKnowledge->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($UserKnowledge) {
                        $UserKnowledge->is_delete = Common::STATUS_NORMAL;
                        if ($UserKnowledge->save()) {

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

        $searchModel = new \backend\models\UserKnowledge();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('user_knowledge', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userKnowledgeModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}