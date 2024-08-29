<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\model;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Time;
use common\models\Category;
use common\models\Image;
use common\models\Models;
use common\models\Story;
use common\models\StoryStages;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class StoryStageEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\StoryStages::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\StoryStages();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qaModel = \backend\models\StoryStages::findOne($id);

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
                default:
                    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
                    $model->load(Yii::$app->request->post());
                    return ActiveForm::validate($model);
            }

            return $this->controller->responseAjax(1, '');
        }

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());

            if ($model->validate()) {
                $model->prop = \common\helpers\Common::encodeJson($model->prop);
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

        $scanImageTypes = StoryStages::$scanType2Name;

        $storyDatas = Story::find()->orderBy(['id' => SORT_DESC])->all();

        $stories = ArrayHelper::map($storyDatas, 'id', 'title');

        return $this->controller->render('story_stage_edit', [
            'storyStage'    => $model,
            'scanImageTypes'    => $scanImageTypes,
            'stories'   => $stories,
        ]);
    }
}