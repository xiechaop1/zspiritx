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
use common\models\StoryModelSpecialEff;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class StoryModelSpecialEffEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\StoryModelSpecialEff::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\StoryModelSpecialEff();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qaModel = \backend\models\StoryModelSpecialEff::findOne($id);

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

            $model->prop = \common\helpers\Common::encodeJson($model->prop);

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


        $storyDatas = Story::find()->orderBy(['id' => SORT_DESC])->all();

        $stories = ['0' => '无'] + ArrayHelper::map($storyDatas, 'id', 'title');

        $modelDatas = Models::find()->orderBy(['model_type' => SORT_DESC, 'id' => SORT_DESC])->all();
        $models = ArrayHelper::map($modelDatas, 'id', 'model_name');

        $storyModelDatas = StoryModels::find()->orderBy(['id' => SORT_DESC])->all();
        $storyModels = ['0' => '无'] + ArrayHelper::map($storyModelDatas, 'id', 'story_model_name');


        return $this->controller->render('story_model_special_eff_edit', [
            'storyModelSpecialEff'    => $model,
            'stories'   => $stories,
            'models'    => $models,
            'storyModels' => $storyModels,
        ]);
    }
}