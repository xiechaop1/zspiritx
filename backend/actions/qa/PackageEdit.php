<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\qa;


use common\definitions\Common;
use common\helpers\Time;
use common\models\QaPackage;
use common\models\Story;
use common\models\StoryModels;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class PackageEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\QaPackage::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\QaPackage();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $qaModel = \backend\models\QaPackage::findOne($id);

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

            $postData = Yii::$app->request->post();

            if (!empty($postData['QaPackage']['qaids'])) {
                $postData['QaPackage']['qa_ids'] = implode(',', $postData['QaPackage']['qaids']);
                unset($postData['QaPackage']['qaids']);
            }
            $model->load($postData);

//            var_dump(Yii::$app->request->post());
//            if (!empty($model->qa_ids)) {
//                $model->qa_ids = implode(',', $model->qa_ids);
//            }
//            var_dump($model);exit;
//            var_dump($model->qa_ids);exit;
            if ($model->validate()) {
                if (!empty($model->prop)) {
                    $model->prop = json_encode(eval("return {$model->prop};"));
                }
//            var_dump($model);exit;

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


        $storyDatas = Story::find()->all();

        $stories = array_reverse(ArrayHelper::map($storyDatas, 'id', 'title'), TRUE);

        $model->prop = json_decode($model->prop, true);

        $qaDatas = \common\models\Qa::find()->orderBy(['id' => SORT_DESC])->all();
        $qas = ArrayHelper::map($qaDatas, 'id', 'topic');

        $model->qaids = explode(',', $model->qa_ids);

        $storyModelDatas = StoryModels::find()->orderBy(['id' => SORT_DESC])->all();
        $storyModels = ArrayHelper::map($storyModelDatas, 'id', 'story_model_name');

//        $model->qa_ids = \common\models\Qa::find()
//            ->select('id')
//            ->column();

        return $this->controller->render('package_edit', [
            'qaPackageModel'    => $model,
            'stories'   => $stories,
            'qas'       => $qas,
            'storyModels' => $storyModels,
        ]);
    }
}