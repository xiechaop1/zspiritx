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

class QaPackage extends Action
{

    
    public function run()
    {
        $qaId = Net::post('id');
        if ($qaId) {
            $model = \common\models\QaPackage::findOne($qaId);
        } else {
            $model = new \common\models\QaPackage();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qa = \common\models\QaPackage::findOne($id);
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

        $searchModel = new \backend\models\QaPackage();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());


        return $this->controller->render('qa_package', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'qaPackageModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}