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

class StoryStageLink extends Action
{

    
    public function run()
    {
        $storyStageLinkId = Net::post('data-id');
        if ($storyStageLinkId) {
            $model = \common\models\StoryStageLink::findOne($storyStageLinkId);
        } else {
            $model = new \common\models\StoryStageLink();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('data-id');
            $storyStageLink = \common\models\StoryStageLink::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($storyStageLink) {
                        if ($storyStageLink->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
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

        $searchModel = new \backend\models\StoryStageLink();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('story_stage_link', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'storyStageLink'    => $model,
            'params'        => $_GET,
        ]);
    }
}