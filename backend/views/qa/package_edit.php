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
    'label' => '课包管理',
];
use yii\web\JsExpression;

$this->title = '课包编辑';
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
            echo $form->field($qaPackageModel, 'package_name')->textInput(['value' => $qaPackageModel->package_name])->label('题包名称');
            echo $form->field($qaPackageModel, 'package_desc')->textarea(['rows' => 15])->label('题包介绍');

            //            echo $form->field($qaPackageModel, 'cover_thumbnail')->widget('\liyifei\uploadOSS\FileUploadOSS', [
            //                'multiple' => false,
            //                'isImage' => true,
            //                'ossHost' => Yii::$app->params['oss.host'],
            //                'signatureAction' => ['/site/oss-signature?dir=thumb/' . Date('Y/m/')],
            //                'clientOptions' => ['autoUpload' => true],
            //                'options' => ['value' => $qaPackageModel->cover_thumbnail],
            ////                'directory' => 'cover_thumb/' . Date('Y/m/')
            //            ])->label('缩略图');
            echo $form->field($qaPackageModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($qaPackageModel, 'package_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\QaPackage::$packageType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('类型');
            echo $form->field($qaPackageModel, 'package_class')->widget('\kartik\select2\Select2', [
                'data' => \common\models\QaPackage::$packageClass2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('科目');
            echo $form->field($qaPackageModel, 'qaids')->widget('\kartik\select2\Select2', [
                'data' => $qas,
                'options' => [
                    'multiple' => true
                ],
            ])->label('QA');
            echo $form->field($qaPackageModel, 'grade')->widget('\kartik\select2\Select2', [
                'data' => \common\models\UserExtends::$userGrade2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('从属年级');
            echo $form->field($qaPackageModel, 'level')->textInput(['value' => $qaPackageModel->level])->label('级别');
            echo $form->field($qaPackageModel, 'link_story_model_id')->widget('\kartik\select2\Select2', [
                'data' => ['0' => '无'] + $storyModels,
                'options' => [
                    'multiple' => false
                ],
            ])->label('关联模型');
            echo $form->field($qaPackageModel, 'prop')->textarea(['rows' => 15])->label('配置');



            //            echo $form->field($qaPackageModel, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $qaPackageModel->chorus_url],
////                'directory' => 'chorus_music/' . Date('Y/m/')
//            ])->label('副歌');
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