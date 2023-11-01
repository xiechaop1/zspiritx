<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\model;


use common\helpers\Model;
use common\models\Models;
use common\models\SessionModels;
use common\models\Story;
use common\models\StoryModels;
use common\models\UserModels;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class UserModelEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\UserModel::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\UserModel();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qaModel = \backend\models\UserModel::findOne($id);

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

            $storyModel = StoryModels::find()
                ->where([
                    'id'    => $model->story_model_id,
                ])
                ->one();

            if (!empty($storyModel)) {
                $model->story_model_detail_id = $storyModel->story_model_detail_id;
            }

//            if (empty($model->model_id)) {
                $model->model_id = $storyModel->model_id;
//            }

            $sessionModel = SessionModels::find()
                ->where([
                    'session_id' => $model->session_id,
                    'story_model_id'    => $model->story_model_id,
                ])
                ->one();

            $model->session_model_id = $sessionModel->id;

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

        $stories = ArrayHelper::map(Story::find()->orderBy(['id' => SORT_DESC])->all(), 'id', 'title');
        $sessionData = \common\models\Session::find()->orderBy(['id' => SORT_DESC])->all();
        $sessions = [];
        foreach ($sessionData as $sd) {
            $sessions[$sd->id] = $sd->session_name . ' [' . $sd->id . ']';
        }

        $models = ArrayHelper::map(Models::find()->orderBy(['id' => SORT_DESC])->all(), 'id', 'model_name');

        $storyModelData = StoryModels::find()->orderBy(['id' => SORT_DESC])->all();
        $storyModelDatas = [];
        foreach ($storyModelData as $smd) {
            $storyModelDatas[$smd->id] = !empty($smd->story_model_name)
                    ? $smd->story_model_name
                    : !empty($smd->model->model_name) ? $smd->model->model_name : '未知';
            $storyModelDatas[$smd->id] .= ' [' . $smd->id . '|' . $smd->story_model_detail_id . ']';
        }

        $userData = \common\models\User::find()->orderBy(['id' => SORT_DESC])->all();
        $users = [];
        foreach ($userData as $ud) {
            $users[$ud->id] = $ud->user_name . ' [' . $ud->id . ']';
        }


        return $this->controller->render('user_model_edit', [
            'userModel'    => $model,
            'stories'   => $stories,
            'storyModelDatas' => $storyModelDatas,
            'sessions'  => $sessions,
            'models'    => $models,
            'users'     => $users,
        ]);
    }
}