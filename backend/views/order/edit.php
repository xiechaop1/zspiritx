<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/29
 * Time: 下午8:32
 */

use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->params['breadcrumbs'][] = [
    'label' => '订单管理',
];
use yii\web\JsExpression;

$this->title = '订单管理';
echo \dmstr\widgets\Alert::widget();

?>


    <div class="box box-primary">
        <div class="box-header">
        </div>
        <div class="box-body">
            <?php
            $form = \yii\bootstrap\ActiveForm::begin([
                'layout' => 'horizontal',
                'enableClientValidation' => true,
            ]);
            echo $form->field($orderModel, 'id')->textInput(['value' => $orderModel->id, 'readonly' => true])->label('ID');
            echo $form->field($orderModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($orderModel, 'user_id')->textInput(['value' => $orderModel->user_id])->label('用户名');
            echo $form->field($orderModel, 'mobile')->textInput(['value' => $orderModel->mobile])->label('手机号');
            echo $form->field($orderModel, 'amount')->textInput(['value' => $orderModel->amount])->label('价格');
            echo $form->field($orderModel, 'pay_method')->widget('\kartik\select2\Select2', [
                'data' => \common\models\Order::$payMethod2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('支付方式');
            echo $form->field($orderModel, 'order_status')->widget('\kartik\select2\Select2', [
                'data'  => \common\models\Order::$orderStatus,
                'options' => [
                    'id'    => 'order_status',
                    'multiple' => false,
                    'value' => $orderModel->order_status,
                ],
            ])->label('订单状态');

            ?>
            <div class="form-group">
                <label class="control-label col-sm-3"></label>
                <div class="col-sm-5">
                    <?= \yii\bootstrap\Html::submitButton('提交', ['class' => 'center-block btn btn-primary btn-w-m']) ?>
                </div>
            </div>
            <?php
            \yii\bootstrap\ActiveForm::end();
            ?>

        </div>
    </div>