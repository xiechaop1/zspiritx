<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\story;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class UserStory extends Action
{

    
    public function run()
    {
        $userStoryId = Net::post('id');
        if ($userStoryId) {
            $model = \common\models\UserStory::findOne($userStoryId);
        } else {
            $model = new \common\models\UserStory();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $userStory = \common\models\UserStory::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($userStory) {
                        if ($userStory->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($userStory) {
                        $userStory->status = Common::STATUS_NORMAL;
                        if ($userStory->save()) {

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

        $searchModel = new \backend\models\UserStory();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('user_story', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userStoryModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}