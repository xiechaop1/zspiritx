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

class Knowledge extends Action
{

    
    public function run()
    {
        $knowledgeId = Net::post('id');
        if ($knowledgeId) {
            $model = \common\models\Knowledge::findOne($knowledgeId);
        } else {
            $model = new \common\models\Knowledge();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $knowledge = \common\models\Knowledge::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($knowledge) {
//                        $knowledge->is_delete = Common::STATUS_DELETED;
                        if ($knowledge->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($knowledge) {
                        $knowledge->is_delete = Common::STATUS_NORMAL;
                        if ($knowledge->save()) {

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

        $searchModel = new \backend\models\Knowledge();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $knowledgeTypes = \common\models\Knowledge::$knowledgeType2Name;

        return $this->controller->render('knowledgelist', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'knowledgeTypes'   => $knowledgeTypes,
            'knowledgeModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}