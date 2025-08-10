<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\ebook;


use common\models\EBook;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class Edit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\EBook::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\EBook();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $storyModel = \backend\models\EBook::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($storyModel) {
                        if ($storyModel->delete()) {
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

            if (!empty($model->story_params)) {
                eval('$story_params = ' . $model->story_params);
                $model->story_params = json_encode($story_params, JSON_UNESCAPED_UNICODE);
            }

            if ($model->validate()) {
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


//        $model->guide = !empty($model->guide)
//            ? var_export(json_decode($model->guide, true), true) . ';' : '';
//        $model->story_bg = !empty($model->story_bg)
//            ? var_export(json_decode($model->story_bg, true), true) . ';' : '';


        return $this->controller->render('edit', [
            'ebookModel'    => $model,
        ]);
    }
}