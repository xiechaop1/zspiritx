<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\ebook;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class Story extends Action
{

    
    public function run()
    {
        $storyId = Net::post('id');
        if ($storyId) {
            $model = \common\models\Story::findOne($storyId);
        } else {
            $model = new \common\models\Story();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $story = \common\models\Story::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($story) {
                        if ($story->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($story) {
                        $story->status = Common::STATUS_DELETED;
                        if ($story->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($story) {
                        $story->status = Common::STATUS_NORMAL;
                        if ($story->save()) {

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

        $searchModel = new \backend\models\EBook();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('story', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'ebookModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}