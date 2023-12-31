<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\model;


use common\definitions\Common;
use common\models\Models;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class StoryModelLink extends Action
{

    
    public function run()
    {
        $storyModelLinkId = Net::post('data-id');
        if ($storyModelLinkId) {
            $model = \common\models\StoryModelsLink::findOne($storyModelLinkId);
        } else {
            $model = new \common\models\StoryModelsLink();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyModelLink = \common\models\StoryModelsLink::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($storyModelLink) {
                        if ($storyModelLink->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'copy':
                    if ($storyModelLink) {
                        $newStoryModel = new \common\models\StoryModelsLink();
                        $blackKeyList = ['id', 'status', 'created_at', 'updated_at'];
                        foreach ($storyModelLink as $key => $value) {
                            if (in_array($key, $blackKeyList)) {
                                continue;
                            }
                            $newStoryModel->$key = $value;
                        }
//                        $newStoryModel->story_model_name = $newStoryModel->story_model_name . '_copy';
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

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                Yii::$app->session->setFlash('danger', '操作失败');
            }
            return $this->controller->refresh();
        }

        $searchModel = new \backend\models\StoryModelsLink();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

//        $storyModelDatas = \common\models\StoryModels::find()->orderBy(['id' => SORT_DESC])->all();
//        $storyModelList = ['-1' => '无'] + ['-2' => '部分匹配'];
//        foreach ($storyModelDatas as $storyModel) {
//            $storyModelList[$storyModel->id] = $storyModel->story->title . ' ';
//            $storyModelList[$storyModel->id] .=
//                !empty($storyModel->story_model_name)
//                ? $storyModel->story_model_name . ' [' . $storyModel->id . '|' . $storyModel->story_model_detail_id . ']' :
//                    !empty($storyModel->model)
//                        ? $storyModel->model->model_name
////                    ? 'abc'
//                        . ' [' . $storyModel->id . '|' . $storyModel->story_model_detail_id . ']' : '[' . $storyModel->id . '|' . $storyModel->story_model_detail_id . ']';
//            $storyModelList[$storyModel->id] .= '(' . $storyModel->model_inst_u_id . ')';
//            ;
//        }
//
//        $story = Story::find()->orderBy(['id' => SORT_DESC])->all();
//        $storyList = ArrayHelper::map($story, 'id', 'title');

        return $this->controller->render('story_model_link', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'storyModelLink'    => $model,
//            'storyModelList'    => $storyModelList,
//            'storyList'         => $storyList,
            'params'        => $_GET,
        ]);
    }
}