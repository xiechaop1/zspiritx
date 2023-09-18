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

$this->title = '剧本模型编辑';
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

            echo $form->field($storyModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');

            echo $form->field($storyModel, 'model_id')->widget('\kartik\select2\Select2', [
                'data' => $models,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型');
            echo $form->field($storyModel, 'story_model_name')->textInput(['value' => $storyModel->story_model_name])->label('剧本模型名称');
            echo $form->field($storyModel, 'story_model_desc')->textarea(['value' => $storyModel->story_model_desc])->label('剧本模型描述');

            echo $form->field($storyModel, 'story_stage_id')->textInput(['value' => $storyModel->story_stage_id])->label('Stage ID');
            echo $form->field($storyModel, 'model_inst_u_id')->textInput(['value' => $storyModel->model_inst_u_id])->label('Model Inst UID');
            echo $form->field($storyModel, 'scan_image_id')->textInput(['value' => $storyModel->scan_image_id])->label('Scan Image ID');
            echo $form->field($storyModel, 'scan_type')->widget('\kartik\select2\Select2', [
                'data' => $scanImageTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('扫描类型');
            echo $form->field($storyModel, 'direction')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$direction2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型朝向');
            echo $form->field($storyModel, 'misrange')->textInput(['value' => $storyModel->misrange])->label('误差');
            echo $form->field($storyModel, 'act_misrange')->textInput(['value' => $storyModel->act_misrange])->label('动作误差距离');
            echo $form->field($storyModel, 'lat')->textInput(['value' => $storyModel->lat])->label('经度');
            echo $form->field($storyModel, 'lng')->textInput(['value' => $storyModel->lng])->label('纬度');
            echo $form->field($storyModel, 'rate')->textInput(['value' => $storyModel->rate])->label('出现概率');
            echo $form->field($storyModel, 'timebegin')->textInput(['value' => $storyModel->timebegin])->label('开始时间');
            echo $form->field($storyModel, 'timeend')->textInput(['value' => $storyModel->timeend])->label('结束时间');
            echo $form->field($storyModel, 'show_x')->textInput(['value' => $storyModel->show_x])->label('坐标X');
            echo $form->field($storyModel, 'show_y')->textInput(['value' => $storyModel->show_y])->label('坐标Y');
            echo $form->field($storyModel, 'show_z')->textInput(['value' => $storyModel->show_z])->label('坐标Z');
            echo $form->field($storyModel, 'is_unique')->textInput(['value' => $storyModel->is_unique])->label('是否唯一');
            echo $form->field($storyModel, 'is_visable')->textInput(['value' => $storyModel->is_visable])->label('是否显示');
            echo $form->field($storyModel, 'active_next')->textarea(['value' => \common\helpers\Active::decodeActiveToShow($storyModel->active_next)])->label('动作内容');
            echo $form->field($storyModel, 'active_expiretime')->textInput(['value' => $storyModel->active_expiretime])->label('动作过期时间');


//            echo $form->field($storyModel, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $storyModel->chorus_url],
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
