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

$this->title = '剧本模型详情编辑';
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

            echo $form->field($storyModelDetailModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($storyModelDetailModel, 'model_id')->widget('\kartik\select2\Select2', [
                'data' => $models,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型');
            echo $form->field($storyModelDetailModel, 'title')->textInput(['value' => $storyModelDetailModel->title])->label('详情名称');
            echo $form->field($storyModelDetailModel, 'direction')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModels::$direction2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型朝向');
            if (!empty($storyModelDetailModel->dialog)) {
                $dialogTxt = var_export(\common\helpers\Model::decodeDialog($storyModelDetailModel->dialog), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $dialogTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $dialogTxt) . ';';
            } else {
                $dialogTxt = '';
            }
            echo $form->field($storyModelDetailModel, 'dialog')->textarea(['value' => !empty($storyModelDetailModel->dialog) ? $dialogTxt: '', 'rows' => 20])->label('对话');
            echo $form->field($storyModelDetailModel, 'story_model_image')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=story_model/image/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModelDetailModel->story_model_image],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图片/影音文件');
//            echo $form->field($storyModelDetailModel, 'dialog')->textarea(['value' => !empty($storyModelDetailModel->dialog) ? var_export(\common\helpers\Model::decodeDialog($storyModelDetailModel->dialog), true) . ';': '', 'rows' => 20])->label('对话');
            echo $form->field($storyModelDetailModel, 'is_unique')->textInput(['value' => $storyModelDetailModel->is_unique])->label('是否唯一');
            echo $form->field($storyModelDetailModel, 'active_next')->textarea(['value' => \common\helpers\Model::decodeActiveToShow($storyModelDetailModel->active_next)])->label('动作内容');
            echo $form->field($storyModelDetailModel, 'active_expiretime')->textInput(['value' => $storyModelDetailModel->active_expiretime])->label('动作过期时间');


//            echo $form->field($storyModelDetailModel, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $storyModelDetailModel->chorus_url],
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
