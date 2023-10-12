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

class ItemKnowledge extends Action
{

    
    public function run()
    {
        $userKnowledgeId = Net::post('id');
        if ($userKnowledgeId) {
            $model = \common\models\ItemKnowledge::findOne($userKnowledgeId);
        } else {
            $model = new \common\models\ItemKnowledge();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $ItemKnowledge = \common\models\ItemKnowledge::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($ItemKnowledge) {
                        if ($ItemKnowledge->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($ItemKnowledge) {
                        $ItemKnowledge->is_delete = Common::STATUS_NORMAL;
                        if ($ItemKnowledge->save()) {

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

        $searchModel = new \backend\models\ItemKnowledge();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

//        $knowledgeDatas = \common\models\Knowledge::find()->orderBy('id desc')->all();
//        $knowledges = ArrayHelper::map($knowledgeDatas, 'id', 'title');

        return $this->controller->render('item_knowledge', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'itemKnowledgeModel'    => $model,
//            'knowledges'    => $knowledges,
            'params'        => $_GET,
        ]);
    }
}