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
    'label' => '抽奖管理',
];
use yii\web\JsExpression;

$this->title = '抽奖编辑';
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
            echo $form->field($lotteryPrizeModel, 'prize_name')->textInput(['value' => $lotteryPrizeModel->prize_name])->label('奖品名称');
            echo $form->field($lotteryPrizeModel, 'prize_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\LotteryPrize::$prizeType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('奖品类型');
            echo $form->field($lotteryPrizeModel, 'story_model_id')->textInput(['value' => $lotteryPrizeModel->story_model_id])->label('模型ID');
            echo $form->field($lotteryPrizeModel, 'prize_level')->textInput(['value' => $lotteryPrizeModel->prize_level])->label('奖品等级');
            echo $form->field($lotteryPrizeModel, 'prize_level_name')->textInput(['value' => $lotteryPrizeModel->prize_level_name])->label('奖品等级描述');
            echo $form->field($lotteryPrizeModel, 'prize_method')->widget('\kartik\select2\Select2', [
                'data' => \common\models\LotteryPrize::$prizeMethod2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('中奖方式');
            echo $form->field($lotteryPrizeModel, 'rate')->textInput(['value' => $lotteryPrizeModel->rate])->label('中奖概率（万分之）');
            echo $form->field($lotteryPrizeModel, 'total_ct')->textInput(['value' => $lotteryPrizeModel->total_ct])->label('总数');
            echo $form->field($lotteryPrizeModel, 'interval_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\LotteryPrize::$intervalType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('间隔类型');
            echo $form->field($lotteryPrizeModel, 'interval_ct')->textInput(['value' => $lotteryPrizeModel->interval_ct])->label('间隔内数量');
            echo $form->field($lotteryPrizeModel, 'thumbnail')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=lottery_prize/image/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $lotteryPrizeModel->thumbnail],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('缩略图');
            echo $form->field($lotteryPrizeModel, 'image')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=lottery_prize/image/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $lotteryPrizeModel->image],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图片');

            echo $form->field($lotteryPrizeModel, 'prize_option')->textarea(['rows' => 15])->label('中奖条件');
            echo $form->field($lotteryPrizeModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($lotteryPrizeModel, 'lottery_id')->widget('\kartik\select2\Select2', [
                'data' => $lotteries,
                'options' => [
                    'multiple' => false
                ],
            ])->label('活动');


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


<script>
    $(document).ready(function () {
        console.log($('#view_city'));
        $("#view_city").select2({
            //tags: true
        })
        ;

        $("#view_city").on("select2:select", function (evt) {
            console.log($(this));
            var element = evt.params.data.element;
            var $element = $(element);

            window.setTimeout(function () {
                console.log($("select#view_city"));
                console.log($("select#view_city").find(":selected"));
                console.log($("select#view_city").find(":selected").length);
                if ($("select#view_city").find(":selected").length > 1) {
                    var $second = $("select#view_city").find(":selected").eq(-1);
                    console.log($second);
                    console.log($element);
                    if ($second.val() != $element.val()) {
                        $element.detach();
                        $second.after($element);
                    }
                } else {
                    $element.detach();
                    $("select#view_city").prepend($element);
                }

                $("select#view_city").trigger("change");
            }, 1);
        });

//        $("select#view_city").on("select2:unselect", function (evt) {
//            if ($("select#view_city").find(":selected").length) {
//                var element = evt.params.data.element;
//                var $element = $(element);
//                $
//                ("select#view_city").find(":selected").after($element);
//            }
//        });
    });
</script>