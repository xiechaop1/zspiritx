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

class StoryModelDetail extends Action
{

    public function run()
    {
        $storyModelId = Net::post('id');
        if ($storyModelId) {
            $model = \common\models\StoryModelDetail::findOne($storyModelId);
        } else {
            $model = new \common\models\StoryModelDetail();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyModel = \common\models\StoryModelDetail::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($storyModel) {
                        if ($storyModel->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($storyModel) {
                        $storyModel->status = Common::STATUS_DELETED;
                        if ($storyModel->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($storyModel) {
                        $storyModel->status = Common::STATUS_NORMAL;
                        if ($storyModel->save()) {

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

        $searchModel = new \backend\models\StoryModelDetail();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('story_model_detail', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'storyModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}