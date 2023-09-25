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
use common\models\Category;
use common\models\Image;
use common\models\Knowledge;
use common\models\Music;
use common\models\MusicCategory;
use common\models\Qa;
use common\models\Singer;
use common\models\Story;
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
            $model = \backend\models\Qa::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\Qa();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qaModel = \backend\models\Qa::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($qaModel) {
                        if ($qaModel->delete()) {
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
                if (!\common\helpers\Common::isJson($model->selected)) {
                    $model->selected = json_encode($model->selected);
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

        $qaTypes = Qa::$qaType2Name;

        $storyDatas = Story::find()->all();

        $stories = ArrayHelper::map($storyDatas, 'id', 'title');

        if (\common\helpers\Common::isJson($model->selected)) {
            $model->selected = json_decode($model->selected, true);
        }

        $knowledgeDatas = Knowledge::find()->all();
        $knowledgeTmps = ArrayHelper::map($knowledgeDatas, 'id', 'title');

        $knowledges = ['0' => '无'] + $knowledgeTmps;


        return $this->controller->render('edit', [
            'qaModel'    => $model,
            'qaTypes'    => $qaTypes,
            'stories'   => $stories,
            'knowledges'    => $knowledges,
        ]);
    }
}