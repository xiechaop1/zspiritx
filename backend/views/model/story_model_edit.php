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

            echo $form->field($storyModel, 'id')->textInput(['value' => $storyModel->id, 'readonly' => true])->label('ID');
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
            echo $form->field($storyModel, 'icon')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=story_model/icon/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModel->icon],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图标');
            echo $form->field($storyModel, 'story_model_desc')->textarea(['value' => $storyModel->story_model_desc])->label('剧本模型描述');

            echo $form->field($storyModel, 'story_stage_id')->textInput(['value' => $storyModel->story_stage_id])->label('Stage ID');
            echo $form->field($storyModel, 'pos_story_model_id')->textInput(['value' => $storyModel->pos_story_model_id])->label('位置基本模型ID');
            echo $form->field($storyModel, 'model_inst_u_id')->textInput(['value' => $storyModel->model_inst_u_id])->label('Model Inst UID');
            echo $form->field($storyModel, 'target_model_u_id')->textInput(['value' => $storyModel->target_model_u_id])->label('目标模型UnityID');
            echo $form->field($storyModel, 'scan_image_id')->textInput(['value' => $storyModel->scan_image_id])->label('Scan Image ID');
            echo $form->field($storyModel, 'scan_image_path')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=resourcepackage/image/imglib/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModel->scan_image_path],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('识别图片文件');
            echo $form->field($storyModel, 'scan_type')->widget('\kartik\select2\Select2', [
                'data' => $scanImageTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('扫描类型');
            echo $form->field($storyModel, 'story_model_class')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$storyModelClass2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型类型');
            echo $form->field($storyModel, 'misrange')->textInput(['value' => $storyModel->misrange])->label('误差');
            echo $form->field($storyModel, 'trigger_misrange')->textInput(['value' => $storyModel->trigger_misrange])->label('触发误差距离');
            echo $form->field($storyModel, 'act_misrange')->textInput(['value' => $storyModel->act_misrange])->label('动作误差距离');
            echo $form->field($storyModel, 'scale')->textInput(['value' => $storyModel->scale])->label('缩放比例');
            echo $form->field($storyModel, 'lat')->textInput(['value' => $storyModel->lat])->label('经度');
            echo $form->field($storyModel, 'lng')->textInput(['value' => $storyModel->lng])->label('纬度');
//            echo $form->field($storyModel, 'story_model_detail_id')->textInput(['value' => $storyModel->story_model_detail_id])->label('模型详细ID');
            echo $form->field($storyModel, 'story_model_detail_id')->widget('\kartik\select2\Select2', [
                'data' => $storyModelDetails,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型详细ID');
            echo $form->field($storyModel, 'model_group')->textInput(['value' => $storyModel->model_group])->label('互斥模型分组');
            echo $form->field($storyModel, 'direction')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$direction2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型朝向');
            echo $form->field($storyModel, 'is_visable')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$visible2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('是否显示');
            echo $form->field($storyModel, 'is_placing_hint')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$placingHint2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('是否显示提示');
            echo $form->field($storyModel, 'selected_permission')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$selectedPermission2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('是否可选');
            echo $form->field($storyModel, 'namecard_display')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$namecardDisplay2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('是否显示名牌');
            echo $form->field($storyModel, 'price')->textInput(['value' => $storyModel->price])->label('价格');
//            echo $form->field($storyModel, 'is_undertake')->widget('\kartik\select2\Select2', [
//                'data' => \common\models\StoryModels::$isUndertake2Name,
//                'options' => [
//                    'multiple' => false
//                ],
//            ])->label('是否兜底');
//            echo $form->field($storyModel, 'undertake_trigger_timeout')->textInput(['value' => $storyModel->undertake_trigger_timeout])->label('兜底执行时间（s）');
//            echo $form->field($storyModel, 'undertake_alive_timeout')->textInput(['value' => $storyModel->undertake_alive_timeout])->label('兜底持续时间（s）');

            // AI生成对话输入框
            ?>
            <div class="form-group">
                <label class="control-label col-sm-3">AI生成对话</label>
                <div class="col-sm-6">
                    <textarea id="ai-dialog-description" class="form-control" rows="3"
                        placeholder="请描述你想要生成的对话内容，例如：小灵语和玩家讨论数学问题，有3个选择分支"></textarea>
                    <button type="button" id="generate-dialog-btn" class="btn btn-primary" style="margin-top: 10px;">
                        <i class="fa fa-magic"></i> 生成对话
                    </button>
                    <span id="generate-loading" style="display:none; margin-left: 10px;">
                        <i class="fa fa-spinner fa-spin"></i> 正在生成中...
                    </span>
                    <div id="generate-error" class="alert alert-danger" style="display:none; margin-top: 10px;"></div>
                </div>
            </div>
            <?php

            if (!empty($storyModel->dialog)) {
                $dialogTxt = var_export(\common\helpers\Model::decodeDialog($storyModel->dialog), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $dialogTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $dialogTxt) . ';';
            } else {
                $dialogTxt = '';
            }
            echo $form->field($storyModel, 'dialog')->textarea(['value' => !empty($storyModel->dialog) ? $dialogTxt: '', 'rows' => 20])->label('对话');
            if (!empty($storyModel->dialog2)) {
                $dialogTxt = var_export(\common\helpers\Model::decodeDialog($storyModel->dialog2), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $dialogTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $dialogTxt) . ';';
            } else {
                $dialogTxt = '';
            }
            echo $form->field($storyModel, 'dialog2')->textarea(['value' => !empty($storyModel->dialog2) ? $dialogTxt: '', 'rows' => 20])->label('对话2');

            if (!empty($storyModel->posing)) {
                $dialogTxt = var_export(\common\helpers\Model::decodeDialog($storyModel->posing), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $dialogTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $dialogTxt) . ';';
            } else {
                $dialogTxt = '';
            }
            echo $form->field($storyModel, 'posing')->textarea(['value' => !empty($storyModel->posing) ? $dialogTxt: '', 'rows' => 20])->label('放置姿势');
//            echo $form->field($storyModel, 'dialog')->textarea(['value' => !empty($storyModel->dialog) ? var_export(\common\helpers\Model::decodeDialog($storyModel->dialog), true) . ';': '', 'rows' => 20])->label('对话');
            echo $form->field($storyModel, 'active_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$activeType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('动作类型');
            echo $form->field($storyModel, 'active_model_inst_u_id')->textInput(['value' => $storyModel->active_model_inst_u_id])->label('动作目标模型Unity ID');
            echo $form->field($storyModel, 'use_allow')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$useAllow2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('使用规范');
            echo $form->field($storyModel, 'story_model_image')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=story_model/image/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModel->story_model_image],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图片/影音文件');
            echo $form->field($storyModel, 'story_model_prop')->textarea(['value' => \common\helpers\Common::decodeJsonToVarexport($storyModel->story_model_prop), 'rows' => 20])->label('模型属性配置');
            echo $form->field($storyModel, 'story_model_html')->textarea(['value' => !empty($storyModel->story_model_html) ? \common\helpers\Common::decodeJsonToVarexport($storyModel->story_model_html): '', 'rows' => 20])->label('模型Html配置');
            echo $form->field($storyModel, 'use_group_name')->textInput(['value' => $storyModel->use_group_name])->label('道具使用背包分组');
            echo $form->field($storyModel, 'is_random')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$isRandom2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('是否在随机范围');
            echo $form->field($storyModel, 'sort_by')->textInput(['value' => $storyModel->sort_by])->label('出现顺序');
            echo $form->field($storyModel, 'rate')->textInput(['value' => $storyModel->rate])->label('出现概率');
            echo $form->field($storyModel, 'set_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$setType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('放置方式');
            echo $form->field($storyModel, 'timebegin')->textInput(['value' => $storyModel->timebegin])->label('开始时间');
            echo $form->field($storyModel, 'timeend')->textInput(['value' => $storyModel->timeend])->label('结束时间');
            echo $form->field($storyModel, 'show_x')->textInput(['value' => $storyModel->show_x])->label('坐标X(右)');
            echo $form->field($storyModel, 'show_y')->textInput(['value' => $storyModel->show_y])->label('坐标Y(高)');
            echo $form->field($storyModel, 'show_z')->textInput(['value' => $storyModel->show_z])->label('坐标Z(前)');
//            echo $form->field($storyModel, 'story_model_config')->textarea(['value' => \common\helpers\Common::decodeJsonToVarexport($storyModel->story_model_config), 'rows' => 20])->label('扩展配置');
            echo $form->field($storyModel, 'is_unique')->textInput(['value' => $storyModel->is_unique])->label('是否唯一');
            echo $form->field($storyModel, 'active_next')->textarea(['value' => \common\helpers\Model::decodeActiveToShow($storyModel->active_next)])->label('动作内容');
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

<script>
$(document).ready(function() {
    $('#generate-dialog-btn').click(function() {
        var description = $('#ai-dialog-description').val();
        if (!description) {
            alert('请输入对话描述');
            return;
        }

        var existingDialog = $('#storymodels-dialog').val();
        var modelName = $('#storymodels-story_model_name').val();

        // 如果模型名为空,使用默认名称
        if (!modelName) {
            modelName = 'Model';
        }

        // 隐藏错误提示
        $('#generate-error').hide();

        // 显示加载状态
        $('#generate-loading').show();
        $('#generate-dialog-btn').prop('disabled', true);

        $.ajax({
            url: window.location.href,
            method: 'POST',
            data: {
                action: 'generate_dialog',
                description: description,
                existing_dialog: existingDialog,
                model_name: modelName
            },
            dataType: 'json',
            timeout: 60000, // 60秒超时
            success: function(response) {
                if (response.success) {
                    $('#storymodels-dialog').val(response.dialog);
                    alert('对话生成成功!');
                    // 清空输入框
                    $('#ai-dialog-description').val('');
                } else {
                    $('#generate-error').text('生成失败: ' + (response.message || '未知错误')).show();
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = '请求失败';
                if (status === 'timeout') {
                    errorMsg = '请求超时,AI生成时间过长,请稍后重试';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else {
                    errorMsg = '网络错误: ' + error;
                }
                $('#generate-error').text(errorMsg).show();
            },
            complete: function() {
                $('#generate-loading').hide();
                $('#generate-dialog-btn').prop('disabled', false);
            }
        });
    });
});
</script>
