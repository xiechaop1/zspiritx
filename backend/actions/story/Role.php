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

class Role extends Action
{

    
    public function run()
    {
        $roleId = Net::post('id');
        if ($roleId) {
            $model = \common\models\StoryRole::findOne($roleId);
        } else {
            $model = new \common\models\StoryRole();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $role = \common\models\StoryRole::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($role) {
                        if ($role->delete()) {
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

        $searchModel = new \backend\models\StoryRole();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $stories = \common\models\Story::find()->select(['id', 'title'])->orderBy(['id' => SORT_DESC])->asArray()->all();

        return $this->controller->render('role', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'roleModel'    => $model,
            'params'        => $_GET,
            'stories'       => ArrayHelper::map($stories, 'id', 'title'),
        ]);
    }
}