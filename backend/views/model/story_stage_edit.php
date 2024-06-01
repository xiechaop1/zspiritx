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
    'label' => '剧本模型管理',
];
use yii\web\JsExpression;

$this->title = '剧本Stage编辑';
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
            echo $form->field($storyStage, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($storyStage, 'stage_name')->textInput(['value' => $storyStage->stage_name])->label('场景名称');
            echo $form->field($storyStage, 'pre_stage_id')->textInput(['value' => $storyStage->pre_stage_id])->label('前置Stage ID');
            echo $form->field($storyStage, 'stage_u_id')->textInput(['value' => $storyStage->stage_u_id])->label('Stage UnityID');
            echo $form->field($storyStage, 'scan_image_id')->textInput(['value' => $storyStage->scan_image_id])->label('Scan Image ID');
            echo $form->field($storyStage, 'stage_class')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryStages::$stageClass2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('类型');
            echo $form->field($storyStage, 'scan_type')->widget('\kartik\select2\Select2', [
                'data' => $scanImageTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('扫描类型');
            echo $form->field($storyStage, 'misrange')->textInput(['value' => $storyStage->misrange])->label('误差');
            echo $form->field($storyStage, 'lat')->textInput(['value' => $storyStage->lat])->label('经度');
            echo $form->field($storyStage, 'lng')->textInput(['value' => $storyStage->lng])->label('纬度');
            echo $form->field($storyStage, 'rate')->textInput(['value' => $storyStage->rate])->label('出现概率');
            echo $form->field($storyStage, 'timebegin')->textInput(['value' => $storyStage->timebegin])->label('开始时间');
            echo $form->field($storyStage, 'timeend')->textInput(['value' => $storyStage->timeend])->label('结束时间');
            echo $form->field($storyStage, 'show_x')->textInput(['value' => $storyStage->show_x])->label('坐标X');
            echo $form->field($storyStage, 'show_y')->textInput(['value' => $storyStage->show_y])->label('坐标Y');
            echo $form->field($storyStage, 'show_z')->textInput(['value' => $storyStage->show_z])->label('坐标Z');
            echo $form->field($storyStage, 'sort_by')->textInput(['value' => $storyStage->sort_by])->label('排序(0-Unity默认场景，>=1故事场景)');
            echo $form->field($storyStage, 'bgm')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => false,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=bgm/stages/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyStage->bgm],
            //                'directory' => 'chorus_music/' . Date('Y/m/')
            ])->label('背景音乐');

//            echo $form->field($storyStage, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $storyStage->chorus_url],
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