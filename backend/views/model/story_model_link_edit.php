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

$this->title = '剧本模型关系编辑';
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
            echo $form->field($storyModelLink, 'id')->textInput(['value' => $storyModelLink->id, 'readonly' => true])->label('ID');
            echo $form->field($storyModelLink, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $storyList,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');

            echo $form->field($storyModelLink, 'story_model_id')->widget('\kartik\select2\Select2', [
                'data' => $storyModelList,
                'options' => [
                    'multiple' => false
                ],
            ])->label('模型');
            echo $form->field($storyModelLink, 'story_model_id2')->widget('\kartik\select2\Select2', [
                'data' => $storyModelList,
                'options' => [
                    'multiple' => false
                ],
            ])->label('关联模型');

            echo $form->field($storyModelLink, 'group_name')->textInput(['value' => $storyModelLink->group_name])->label('分组');
            echo $form->field($storyModelLink, 'is_tag')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModelsLink::$isTag2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('是否标志性');
            echo $form->field($storyModelLink, 'eff_type')->widget('\kartik\select2\Select2', [
                'data' => \common\models\StoryModelsLink::$effType2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('执行类型');
            echo $form->field($storyModelLink, 'eff_exec')->textarea(['value' => !empty($storyModelLink->eff_exec) ? var_export(json_decode($storyModelLink->eff_exec, true), true) : '', 'rows' => 15])->label('执行');
            echo $form->field($storyModelLink, 'min_ct')->textInput(['value' => $storyModelLink->min_ct])->label('执行成功减少数量');

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
