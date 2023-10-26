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

class StoryExtend extends Action
{

    
    public function run()
    {
        $storyExtendId = Net::post('data-id');
        if ($storyExtendId) {
            $model = \common\models\StoryExtend::findOne($storyExtendId);
        } else {
            $model = new \common\models\StoryExtend();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyExtend = \common\models\StoryExtend::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($storyExtend) {
                        if ($storyExtend->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($storyExtend) {
                        $storyExtend->status = Common::STATUS_DELETED;
                        if ($storyExtend->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($storyExtend) {
                        $storyExtend->status = Common::STATUS_NORMAL;
                        if ($storyExtend->save()) {

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

        $searchModel = new \backend\models\StoryExtend();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $storyList = \common\models\Story::find()->orderBy(['id' => SORT_DESC])->all();

        return $this->controller->render('story_extend', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'storyExtendModel'    => $model,
            'storyList'     => ArrayHelper::map($storyList, 'id', 'title'),
            'params'        => $_GET,
        ]);
    }
}