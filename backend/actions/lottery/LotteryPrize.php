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

class Qa extends Action
{

    
    public function run()
    {
        $qaId = Net::post('id');
        if ($qaId) {
            $model = \common\models\Qa::findOne($qaId);
        } else {
            $model = new \common\models\Qa();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qa = \common\models\Qa::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($qa) {
                        if ($qa->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($qa) {
                        $qa->is_delete = Common::STATUS_NORMAL;
                        if ($qa->save()) {

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

        $searchModel = new \backend\models\Qa();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $qaTypes = \common\models\Qa::$qaType2Name;

        return $this->controller->render('qalist', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'qaTypes'   => $qaTypes,
            'qaModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}