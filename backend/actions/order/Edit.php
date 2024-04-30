<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/29
 * Time: 下午8:29
 */

namespace backend\actions\order;


use common\models\Order;
use common\models\Music;
use common\models\Singer;
use common\models\Story;
use common\models\User;
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
            $model = \backend\models\Order::find()->where(['id' => $id]);
            $model = $model->one();
            $isNew = false;
        } else {
            $model = new \backend\models\Order();
            $isNew = true;
        }

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());

            if ($model->validate()) {

                if (!empty($_REQUEST['mobile'])) {
                    $user = User::find()->where(['mobile' => $_REQUEST['mobile']])->one();
                    if (!empty($user)) {
                        $model->user_id = $user->id;
                    }
                }

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '操作成功');
                } else {
                    $errKey = key($model->getFirstErrors());
                    $error = current($model->getFirstErrors());

                    Yii::$app->session->setFlash('danger', "操作失败：[{$errKey}] {$error}");
                }

                return $this->controller->refresh();
            } else {
                Yii::$app->session->setFlash('danger', "操作失败:" . current($model->getFirstErrors()));
            }
            return $this->controller->refresh();
        }

        $storyDatas = Story::find()->orderBy(['id' => SORT_DESC])->all();

        $stories = ArrayHelper::map($storyDatas, 'id', 'title');

        return $this->controller->render('edit', [
            'orderModel'    => $model,
            'stories'       => $stories,
            'orderStatus'   => Order::$orderStatus,
        ]);
    }
}