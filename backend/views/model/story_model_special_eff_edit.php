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
    'label' => '模型特效管理',
];
use yii\web\JsExpression;

$this->title = '模型特效编辑';
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

            echo $form->field($storyModelSpecialEff, 'id')->textInput(['value' => $storyModelSpecialEff->id, 'readonly' => true])->label('ID');
            echo $form->field($storyModelSpecialEff, 'model_id')->widget('\kartik\select2\Select2', [
                'data' => $models,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型');
            echo $form->field($storyModelSpecialEff, 'special_eff_name')->textInput(['value' => $storyModelSpecialEff->special_eff_name])->label('特效名称');
            echo $form->field($storyModelSpecialEff, 'icon')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=story_model/icon/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModelSpecialEff->icon],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图标');
            echo $form->field($storyModelSpecialEff, 'special_eff_desc')->textarea(['value' => $storyModelSpecialEff->special_eff_desc])->label('特效描述');

            echo $form->field($storyModelSpecialEff, 'model_inst_u_id')->textInput(['value' => $storyModelSpecialEff->model_inst_u_id])->label('Model Inst UID');
            echo $form->field($storyModelSpecialEff, 'eff_class')->widget('\kartik\select2\Select2', [
                'data' => \common\models\storyModelSpecialEff::$effClass2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型类型');
            echo $form->field($storyModelSpecialEff, 'eff_mode')->widget('\kartik\select2\Select2', [
                'data' => \common\models\storyModelSpecialEff::$effMode2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模式');
            echo $form->field($storyModelSpecialEff, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($storyModelSpecialEff, 'level')->textInput(['value' => $storyModelSpecialEff->level])->label('等级');
            echo $form->field($storyModelSpecialEff, 'during_ti')->textInput(['value' => $storyModelSpecialEff->during_ti])->label('持续时间');
            echo $form->field($storyModelSpecialEff, 'cd')->textInput(['value' => $storyModelSpecialEff->cd])->label('CD');
            echo $form->field($storyModelSpecialEff, 'link_story_model_id')->widget('\kartik\select2\Select2', [
                'data' => $storyModels,
                'options' => [
                    'multiple' => false
                ],
            ])->label('关联模型');
            echo $form->field($storyModelSpecialEff, 'own_story_model_id')->widget('\kartik\select2\Select2', [
                'data' => $storyModels,
                'options' => [
                    'multiple' => false
                ],
            ])->label('拥有模型');
            if (!empty($storyModelSpecialEff->prop)) {
                $dialogTxt = \common\helpers\Common::decodeJsonToVarexport($storyModelSpecialEff->prop, false);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $dialogTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $dialogTxt);
            } else {
                $dialogTxt = '';
            }
            echo $form->field($storyModelSpecialEff, 'prop')->textarea(['value' => !empty($storyModelSpecialEff->prop) ? $dialogTxt: '', 'rows' => 20])->label('效果');
            echo $form->field($storyModelSpecialEff, 'env_eff')->textInput(['value' => $storyModelSpecialEff->env_eff])->label('环境效果');


//            echo $form->field($storyModelSpecialEff, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $storyModelSpecialEff->chorus_url],
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
