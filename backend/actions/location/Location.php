<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\location;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class Location extends Action
{

    
    public function run()
    {
        $locationId = Net::post('data-id');
        if ($locationId) {
            $model = \common\models\Location::findOne($locationId);
        } else {
            $model = new \common\models\Location();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $location = \common\models\Location::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($location) {
                        if ($location->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($location) {
                        $location->is_delete = Common::STATUS_NORMAL;
                        if ($location->save()) {

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

            if (!empty($model->amap_prop)) {
                $ret = eval('return ' . $model->amap_prop . ';');
                $model->amap_prop = json_encode($ret, JSON_UNESCAPED_UNICODE);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', '操作成功');
            } else {
                Yii::$app->session->setFlash('danger', '操作失败');
            }
            return $this->controller->refresh();
        }

        $searchModel = new \backend\models\Location();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        return $this->controller->render('location', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'locationModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}