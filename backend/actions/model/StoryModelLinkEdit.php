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

class StoryModelLinkEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\StoryModelsLink::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\StoryModelsLink();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyLinkModel = \backend\models\StoryModelsLink::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($storyLinkModel) {
                        if ($storyLinkModel->delete()) {
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

            if (!\common\helpers\Common::isJson($model->eff_exec)
                && !empty($model->eff_exec)
            ) {
                if (strpos($model->eff_exec, -1) != ';') {
                    $model->eff_exec .= ';';
                }
                eval('$tmp = ' . $model->eff_exec);
                $model->eff_exec = json_encode($tmp);
            }

            if ($model->story_model_id >= 0) {
                $storyModel = \common\models\StoryModels::findOne($model->story_model_id);

                if (empty($storyModel)) {
                    Yii::$app->session->setFlash('danger', '模型没找到，操作失败');
                    return $this->controller->refresh();
                }
                $model->story_model_detail_id = $storyModel->story_model_detail_id;
            }

            if ($model->story_model_id2 >= 0) {
                $storyModel2 = \common\models\StoryModels::findOne($model->story_model_id2);
                if (empty($storyModel2)) {
                    Yii::$app->session->setFlash('danger', '目标模型没找到，操作失败');
                    return $this->controller->refresh();
                }
                $model->story_model_detail_id2 = $storyModel2->story_model_detail_id;
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

        $storyModelDatas = \common\models\StoryModels::find()->orderBy(['id' => SORT_DESC])->all();
        $storyModelList = ['-1' => '无'] + ['-2' => '部分匹配'];
        foreach ($storyModelDatas as $storyModel) {
            $storyModelList[$storyModel->id] = $storyModel->story->title . ' ';
            $storyModelList[$storyModel->id] .=
                !empty($storyModel->story_model_name)
                    ? $storyModel->story_model_name . ' [' . $storyModel->id . '|' . $storyModel->story_model_detail_id . ']' :
                    !empty($storyModel->model)
                        ? $storyModel->model->model_name
//                    ? 'abc'
                        . ' [' . $storyModel->id . '|' . $storyModel->story_model_detail_id . ']' : '[' . $storyModel->id . '|' . $storyModel->story_model_detail_id . ']';
            $storyModelList[$storyModel->id] .= '(' . $storyModel->model_inst_u_id . ')';
            ;
        }

        $story = Story::find()->orderBy(['id' => SORT_DESC])->all();
        $storyList = ArrayHelper::map($story, 'id', 'title');

        return $this->controller->render('story_model_link_edit', [
            'storyModel'    => $model,
            'storyModelLink'    => $model,
            'storyModelList'    => $storyModelList,
            'storyList'         => $storyList,
        ]);
    }
}