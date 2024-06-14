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
    'label' => '诗词管理',
];
use yii\web\JsExpression;

$this->title = '诗词编辑';
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
            echo $form->field($poemModel, 'title')->textInput(['value' => $poemModel->title])->label('题目');
            echo $form->field($poemModel, 'image')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=qa/poem/images/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $poemModel->image],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('附件');
            //            echo $form->field($poemModel, 'cover_thumbnail')->widget('\liyifei\uploadOSS\FileUploadOSS', [
            //                'multiple' => false,
            //                'isImage' => true,
            //                'ossHost' => Yii::$app->params['oss.host'],
            //                'signatureAction' => ['/site/oss-signature?dir=thumb/' . Date('Y/m/')],
            //                'clientOptions' => ['autoUpload' => true],
            //                'options' => ['value' => $poemModel->cover_thumbnail],
            ////                'directory' => 'cover_thumb/' . Date('Y/m/')
            //            ])->label('缩略图');
            echo $form->field($poemModel, 'poem_type')->widget('\kartik\select2\Select2', [
                'data' => $poemTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('类型');
            echo $form->field($poemModel, 'poem_class')->widget('\kartik\select2\Select2', [
                'data' => $poemClass,
                'options' => [
                    'multiple' => false
                ],
            ])->label('分类');
            echo $form->field($poemModel, 'poem_class2')->widget('\kartik\select2\Select2', [
                'data' => $poemClass2,
                'options' => [
                    'multiple' => false
                ],
            ])->label('分类2');
            echo $form->field($poemModel, 'level')->textInput(['value' => $poemModel->level])->label('等级');
            echo $form->field($poemModel, 'author')->textInput(['value' => $poemModel->author])->label('作者');
            echo $form->field($poemModel, 'age')->textInput(['value' => $poemModel->age])->label('时代');
            echo $form->field($poemModel, 'content')->textarea(['rows' => 15])->label('内容');
            echo $form->field($poemModel, 'story')->textarea(['rows' => 15])->label('故事');
            echo $form->field($poemModel, 'prop')->textarea(['rows' => 15])->label('配置');

//            echo $form->field($poemModel, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $poemModel->chorus_url],
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