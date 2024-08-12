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

class StoryModelSpecialEff extends Action
{

    public function run()
    {
        $StoryModelSpecialEffId = Net::post('id');
        if ($StoryModelSpecialEffId) {
            $model = \common\models\StoryModelSpecialEff::findOne($StoryModelSpecialEffId);
        } else {
            $model = new \common\models\StoryModelSpecialEff();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $StoryModelSpecialEff = \common\models\StoryModelSpecialEff::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($StoryModelSpecialEff) {
                        if ($StoryModelSpecialEff->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($StoryModelSpecialEff) {
                        $StoryModelSpecialEff->status = Common::STATUS_DELETED;
                        if ($StoryModelSpecialEff->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($StoryModelSpecialEff) {
                        $StoryModelSpecialEff->status = Common::STATUS_NORMAL;
                        if ($StoryModelSpecialEff->save()) {

                        }
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                case 'copy':
                    if ($StoryModelSpecialEff) {
                        $newStoryModelSpecialEff = new \common\models\StoryModelSpecialEff();
                        $blackKeyList = ['id', 'status', 'created_at', 'updated_at'];
                        foreach ($StoryModelSpecialEff as $key => $value) {
                            if (in_array($key, $blackKeyList)) {
                                continue;
                            }
                            $newStoryModelSpecialEff->$key = $value;
                        }
                        $newStoryModelSpecialEff->special_eff_name = $newStoryModelSpecialEff->special_eff_name . '_copy';
                        if ($newStoryModelSpecialEff->save()) {
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

        $searchModel = new \backend\models\StoryModelSpecialEff();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('story_model_special_eff', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'StoryModelSpecialEff'    => $model,
            'params'        => $_GET,
        ]);
    }
}