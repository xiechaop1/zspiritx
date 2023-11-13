<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\knowledge;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Time;
use common\models\Category;
use common\models\Image;
use common\models\Music;
use common\models\MusicCategory;
use common\models\Knowledge;
use common\models\Singer;
use common\models\Story;
use common\models\StoryStages;
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
            $model = \backend\models\Knowledge::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\Knowledge();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $knowledgeModel = \backend\models\Knowledge::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($knowledgeModel) {
                        $knowledgeModel->is_delete = Common::STATUS_DELETED;
                        if ($knowledgeModel->save()) {
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

        $knowledgeTypes = Knowledge::$knowledgeType2Name;

        $storyDatas = Story::find()->orderBy(['id' => SORT_DESC])->all();

        $stories = ArrayHelper::map($storyDatas, 'id', 'title');

        $storyStageDatas = StoryStages::find()->orderBy(['id' => SORT_DESC])->all();

        $storyStages = [];
        foreach ($storyStageDatas as $storyStage) {
            $storyStages[$storyStage->id] = $storyStage->stage_name . ' [' . $storyStage->stage_u_id . ']';
        }

//        $storyStages = ArrayHelper::map($storyStageDatas, 'id', 'stage_name');
//        $storyStages = ['0' => '无'] + $storyStages;


        return $this->controller->render('edit', [
            'knowledgeModel'    => $model,
            'knowledgeTypes'    => $knowledgeTypes,
            'stories'   => $stories,
            'storyStages'   => $storyStages,
        ]);
    }
}