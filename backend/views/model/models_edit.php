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
    'label' => '模型管理',
];
use yii\web\JsExpression;

$this->title = '模型编辑';
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
            echo $form->field($models, 'model_name')->textInput(['value' => $models->model_name])->label('模型名称');
            echo $form->field($models, 'model_u_id')->textInput(['value' => $models->model_u_id])->label('Model UnityID');
            echo $form->field($models, 'model_uri')->textInput(['value' => $models->model_uri])->label('Model Uri');
            echo $form->field($models, 'model_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\Models::$modelType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型类型');
            echo $form->field($models, 'length')->textInput(['value' => $models->length])->label('长');
            echo $form->field($models, 'width')->textInput(['value' => $models->width])->label('宽');
            echo $form->field($models, 'height')->textInput(['value' => $models->height])->label('高');
            echo $form->field($models, 'is_active')->inline(true)->radioList(\common\models\Models::$isActive2Name, ['value' => $models->is_active])->label('是否动画');

            if (!empty($models->model_desc)) {
                $modelDesc = \common\helpers\Common::decodeJsonToVarexport($models->model_desc, false);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $modelDesc = preg_replace('/\s*\d+\s*=>\s*/', "\n", $modelDesc);
            } else {
                $modelDesc = '';
            }

            echo $form->field($models, 'model_desc')->textarea(['value' => $modelDesc])->label('描述');


//            echo $form->field($models, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $models->chorus_url],
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