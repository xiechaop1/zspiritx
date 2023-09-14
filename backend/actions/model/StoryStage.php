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

class StoryStage extends Action
{

    
    public function run()
    {
        $storyStageId = Net::post('id');
        if ($storyStageId) {
            $model = \common\models\StoryStages::findOne($storyStageId);
        } else {
            $model = new \common\models\StoryStages();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyStage = \common\models\StoryStages::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($storyStage) {
                        if ($storyStage->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($storyStage) {
                        $storyStage->status = Common::STATUS_DELETED;
                        if ($storyStage->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($storyStage) {
                        $storyStage->status = Common::STATUS_NORMAL;
                        if ($storyStage->save()) {

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

        $searchModel = new \backend\models\StoryStages();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('story_stage', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'storyStage'    => $model,
            'params'        => $_GET,
        ]);
    }
}