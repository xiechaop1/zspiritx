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

class StoryModel extends Action
{

    public function run()
    {
        $storyModelId = Net::post('id');
        if ($storyModelId) {
            $model = \common\models\StoryModels::findOne($storyModelId);
        } else {
            $model = new \common\models\StoryModels();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyModel = \common\models\StoryModels::findOne($id);
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
                case 'copy':
                    if ($storyModel) {
                        $newStoryModel = new \common\models\StoryModels();
                        $blackKeyList = ['id', 'status', 'created_at', 'updated_at'];
                        foreach ($storyModel as $key => $value) {
                            if (in_array($key, $blackKeyList)) {
                                continue;
                            }
                            $newStoryModel->$key = $value;
                        }
                        $newStoryModel->story_model_name = $newStoryModel->story_model_name . '_copy';
                        if ($newStoryModel->save()) {
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

        $searchModel = new \backend\models\StoryModels();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('story_model', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'storyModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}