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
    'label' => '商品管理',
];
use yii\web\JsExpression;

$this->title = '商品编辑';
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
            echo $form->field($shopWareModel, 'ware_name')->textInput(['value' => $shopWareModel->ware_name])->label('标题');
            echo $form->field($shopWareModel, 'icon')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=shop_wares/icon/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $shopWareModel->icon],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('Icon');
            echo $form->field($shopWareModel, 'ware_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\ShopWares::$shopWareType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('商品类型');
            echo $form->field($shopWareModel, 'intro')->textarea(['value' => $shopWareModel->intro, 'rows' => 15])->label('简介');
            echo $form->field($shopWareModel, 'link_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\ShopWares::$linkType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('链接类型');
            echo $form->field($shopWareModel, 'link_id')->textInput(['value' => $shopWareModel->link_id])->label('链接ID');
            echo $form->field($shopWareModel, 'price')->textInput(['value' => $shopWareModel->price])->label('价格');
            echo $form->field($shopWareModel, 'discount')->textInput(['value' => $shopWareModel->discount])->label('折扣');
            echo $form->field($shopWareModel, 'pay_way')->widget('\kartik\select2\Select2', [
                'data' => \common\models\ShopWares::$payWay2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('支付方式');
            echo $form->field($shopWareModel, 'store_ct')->textInput(['value' => $shopWareModel->store_ct])->label('库存');
            echo $form->field($shopWareModel, 'ware_status')->widget('\kartik\select2\Select2', [
                'data' => \common\models\ShopWares::$shopWareStatus2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('商品状态');

            ?>

            <div class="form-group">
                <label class="control-label col-sm-3"></label>
                <div class="col-sm-6">
                    <?= \yii\bootstrap\Html::submitButton('提交', ['class' => 'center-block btn btn-success']) ?>
                </div>
            </div>
            <?php
            \yii\bootstrap\ActiveForm::end();
            ?>

        </div>
    </div>

