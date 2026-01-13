<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\model;


use common\definitions\Common;
use common\helpers\Active;
use common\helpers\Attachment;
use common\helpers\Model;
use common\helpers\Time;
use common\models\Category;
use common\models\Image;
use common\models\Models;
use common\models\Music;
use common\models\MusicCategory;
use common\models\Qa;
use common\models\Singer;
use common\models\Story;
use common\models\StoryModels;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class StoryModelEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\StoryModels::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\StoryModels();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qaModel = \backend\models\StoryModels::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($qaModel) {
                        if ($qaModel->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }

                    break;
                case 'generate_dialog':
                    // AI生成对话功能
                    $description = Net::post('description');
                    $existingDialog = Net::post('existing_dialog');
                    $modelName = Net::post('model_name');

                    try {
                        // 调用DialogGenerator服务生成对话
                        $generator = new \common\services\DialogGenerator();
                        $result = $generator->generateDialog($description, $existingDialog, $modelName);

                        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                        return [
                            'success' => true,
                            'dialog' => $result,
                        ];
                    } catch (\Exception $e) {
                        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                        return [
                            'success' => false,
                            'message' => $e->getMessage(),
                        ];
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

            $model->active_next = Model::encodeActive($model->active_next);

            $model->dialog = Model::encodeDialog($model->dialog, $model);
            $model->dialog2 = Model::encodeDialog($model->dialog2, $model);
            $model->posing = Model::encodeDialog($model->posing, $model);

            if (!empty($model->scan_image_path)){
                $scanImagePathInfo = pathinfo($model->scan_image_path);
                if (!empty($scanImagePathInfo['filename'])) {
                    $model->scan_image_id = $scanImagePathInfo['filename'];
                }
            }

            $model->story_model_html = \common\helpers\Common::encodeJson($model->story_model_html);

            $model->story_model_prop = \common\helpers\Common::encodeJson($model->story_model_prop);

//            $model->story_model_config = \common\helpers\Common::encodeJson($model->story_model_config);

            if (empty($model->story_model_name)) {
                if (!empty($model->model_id)) {
                    $modelModel = \common\models\Models::find()
                        ->where(['id' => $model->model_id])
                        ->one();

                    if ($modelModel) {
                        $model->story_model_name = $modelModel->model_name;
                    }
                }
            }

            if ($model->validate()) {

                if ($model->save()) {

                    Yii::$app->session->setFlash('success', '操作成功');
                } else {
                    $errKey = key($model->getFirstErrors());
                    $error = current($model->getFirstErrors());

                    Yii::$app->session->setFlash('danger', "操作失败：[{$errKey}] {$error}");
                }
            } else {
                Yii::$app->session->setFlash('danger', "操作失败:" . current($model->getFirstErrors()));
            }
            return $this->controller->refresh();
        }

        $scanImageTypes = StoryModels::$scanImageType2Name;

        $storyDatas = Story::find()->orderBy(['id' => SORT_DESC])->all();

        $stories = ArrayHelper::map($storyDatas, 'id', 'title');

        $modelDatas = Models::find()->orderBy(['id' => SORT_DESC])->all();
        $models = ArrayHelper::map($modelDatas, 'id', 'model_name');

        $visibleSelection = StoryModels::$visible2Name;

        $storyModelDetailRet = \common\models\StoryModelDetail::find()->orderBy(['id' => SORT_DESC])->all();
        $storyModelDetails = ArrayHelper::map($storyModelDetailRet, 'id', 'title');
        $storyModelDetails = ['0' => '无'] + $storyModelDetails;

        return $this->controller->render('story_model_edit', [
            'storyModel'    => $model,
            'scanImageTypes'    => $scanImageTypes,
            'visibleSelection' => $visibleSelection,
            'stories'   => $stories,
            'models'    => $models,
            'storyModelDetails' => $storyModelDetails,
        ]);
    }
}