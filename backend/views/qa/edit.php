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
    'label' => '问答管理',
];
use yii\web\JsExpression;

$this->title = '问答编辑';
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
            echo $form->field($qaModel, 'topic')->textInput(['value' => $qaModel->topic])->label('题目');
            echo $form->field($qaModel, 'attachment')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=qa/attachment/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $qaModel->attachment],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('附件');
            //            echo $form->field($qaModel, 'cover_thumbnail')->widget('\liyifei\uploadOSS\FileUploadOSS', [
            //                'multiple' => false,
            //                'isImage' => true,
            //                'ossHost' => Yii::$app->params['oss.host'],
            //                'signatureAction' => ['/site/oss-signature?dir=thumb/' . Date('Y/m/')],
            //                'clientOptions' => ['autoUpload' => true],
            //                'options' => ['value' => $qaModel->cover_thumbnail],
            ////                'directory' => 'cover_thumb/' . Date('Y/m/')
            //            ])->label('缩略图');
            echo $form->field($qaModel, 'selected')->textarea()->label('选项');
            echo $form->field($qaModel, 'st_answer')->textarea()->label('标答');
            echo $form->field($qaModel, 'st_selected')->textInput(['value' => $qaModel->st_selected])->label('标准选项');
            echo $form->field($qaModel, 'voice')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => false,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=qa/voice/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $qaModel->voice],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('配音');
            echo $form->field($qaModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($qaModel, 'knowledge_id')->widget('\kartik\select2\Select2', [
                'data' => $knowledges,
                'options' => [
                    'multiple' => false
                ],
            ])->label('知识点');
            echo $form->field($qaModel, 'story_stage_id')->textInput(['value' => $qaModel->story_stage_id])->label('场景');

            echo $form->field($qaModel, 'qa_type')->widget('\kartik\select2\Select2', [
                'data' => $qaTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('类型');
//            echo $form->field($qaModel, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $qaModel->chorus_url],
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