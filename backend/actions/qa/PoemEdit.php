<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\qa;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Time;
use common\models\Knowledge;
use common\models\Poem;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class PoemEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\Poem::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\Poem();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $poemModel = \backend\models\Poem::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($poemModel) {
                        if ($poemModel->delete()) {
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

            if ($model->validate()) {

                if (!empty($model->prop)) {
                    $model->prop = json_encode(eval("return {$model->prop};"));
                }
                
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

        $model->prop = json_decode($model->prop, true);
        if (is_array($model->prop)) {
            $model->prop = var_export($model->prop, true);
        }

        $poemTypes = ['0' => '全部'] + \common\models\Poem::$poemType2Name;
        $poemClass = ['0' => '全部'] + \common\models\Poem::$poemClass2Name;
        $poemClass2 = ['0' => '全部'] + \common\models\Poem::$poemClass2Name;

        return $this->controller->render('poem_edit', [
            'poemModel'    => $model,
            'poemTypes'    => $poemTypes,
            'poemClass'    => $poemClass,
            'poemClass2'   => $poemClass2,
        ]);
    }
}