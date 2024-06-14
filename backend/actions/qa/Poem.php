<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\qa;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class Poem extends Action
{

    
    public function run()
    {
        $poemId = Net::post('id');
        if ($poemId) {
            $model = \common\models\Poem::findOne($poemId);
        } else {
            $model = new \common\models\Poem();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $poem = \common\models\Poem::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($poem) {
                        if ($poem->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($poem) {
                        $poem->is_delete = Common::STATUS_NORMAL;
                        if ($poem->save()) {

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

        $searchModel = new \backend\models\Poem();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $poemTypes = ['0' => '全部'] + \common\models\Poem::$poemType2Name;
        $poemClass = ['0' => '全部'] + \common\models\Poem::$poemClass2Name;
        $poemClass2 = ['0' => '全部'] + \common\models\Poem::$poemClass2Name;

        return $this->controller->render('poemlist', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'poemTypes'   => $poemTypes,
            'poemClass'   => $poemClass,
            'poemClass2'   => $poemClass2,
            'poemModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}