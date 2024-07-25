<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace backend\actions\shop;


use common\definitions\Common;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;

class ShopWare extends Action
{

    
    public function run()
    {
        $shopWaresId = Net::post('data-id');
        if ($shopWaresId) {
            $model = \common\models\ShopWares::findOne($shopWaresId);
        } else {
            $model = new \common\models\ShopWares();
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $shopWares = \common\models\ShopWares::findOne($id);
            switch (Net::post('action')) {
                case 'delete':
                    if ($shopWares) {
                        if ($shopWares->delete()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'offline':
                    if ($shopWares) {
                        $shopWares->status = Common::STATUS_DELETED;
                        if ($shopWares->save()) {
                            Yii::$app->session->setFlash('success', '操作成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '操作失败');
                        }
                    }
                    break;
                case 'reset':
                    if ($shopWares) {
                        $shopWares->status = Common::STATUS_NORMAL;
                        if ($shopWares->save()) {

                        }
                    }
                    Yii::$app->session->setFlash('success', '操作成功');
                    break;
                case 'copy':
                    if ($shopWares) {
                        $newShopWares = new \common\models\ShopWares();
                        $blackKeyList = ['id', 'status', 'created_at', 'updated_at'];
                        foreach ($shopWares as $key => $value) {
                            if (in_array($key, $blackKeyList)) {
                                continue;
                            }
                            $newShopWares->$key = $value;
                        }
                        $newShopWares->ware_name = $newShopWares->ware_name . '_copy';
                        if ($newShopWares->save()) {
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

        $searchModel = new \backend\models\ShopWares();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());

        $storyList = \common\models\Story::find()->orderBy(['id' => SORT_DESC])->all();

        return $this->controller->render('shop_wares', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'shopWaresModel'    => $model,
            'params'        => $_GET,
        ]);
    }
}