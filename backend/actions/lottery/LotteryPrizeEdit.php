<?php
/**
 * Created by PhpStorm.
 * User: xiechao's group
 * Date: 2023/5/29
 * Time: 下午8:29
 */

namespace backend\actions\lottery;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Time;
use common\models\Category;
use common\models\Image;
use common\models\Knowledge;
use common\models\Music;
use common\models\MusicCategory;
use common\models\LotteryPrize;
use common\models\Singer;
use common\models\Story;
use common\models\StoryStages;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use yii\base\Action;
use Yii;
use yii\helpers\ArrayHelper;

class LotteryPrizeEdit extends Action
{
    public function run()
    {
        $id = Net::get('id');

        if ($id) {
            $model = \backend\models\LotteryPrize::findOne($id);
            $isNew = false;
        } else {
            $model = new \backend\models\LotteryPrize();
            $isNew = true;
        }

        if (Yii::$app->request->isAjax) {
            $id = Net::post('id');
            $lotteryPrizeModel = \backend\models\LotteryPrize::findOne($id);

            switch (Net::post('action')) {
                case 'delete':
                    if ($lotteryPrizeModel) {
                        if ($lotteryPrizeModel->delete()) {
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

                if (!\common\helpers\Common::isJson($model->prize_option)) {
                    $model->prize_option = json_encode($model->prize_option);
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

        $intervalType = LotteryPrize::$intervalType2Name;
        $prizeMethod = LotteryPrize::$prizeMethod2Name;

        $storyDatas = \common\models\Story::find()->all();
        $stories = ['0' => '无'] + array_reverse(ArrayHelper::map($storyDatas, 'id', 'title'), TRUE);

        $lotteryDatas = \common\models\Lottery::find()->all();
        $lotteries = ['0' => '无'] + array_reverse(ArrayHelper::map($lotteryDatas, 'id', 'lottery_name'), TRUE);

        if (\common\helpers\Common::isJson($model->prize_option)) {
            $model->prize_option = json_decode($model->prize_option, true);
            if (is_array($model->prize_option)) {
                $model->prize_option = var_export($model->prize_option, true);
//                $model->selected = preg_replace('/\s*\d+\s*=>\s*/', "\n", $model->selected) . ';';

            }
        }

        return $this->controller->render('lottery_prize_edit', [
            'lotteryPrizeModel'    => $model,
            'intervalType'    => $intervalType,
            'prizeMethod'     => $prizeMethod,
            'stories'   => $stories,
            'lotteries'   => $lotteries,
        ]);
    }
}