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
    'label' => '用户模型管理',
];
use yii\web\JsExpression;

$this->title = '用户模型编辑';
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

            echo $form->field($userModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($userModel, 'user_id')->widget('\kartik\select2\Select2', [
                'data' => $users,
                'options' => [
                    'multiple' => false
                ],
            ])->label('用户');

//            echo $form->field($userModel, 'model_id')->widget('\kartik\select2\Select2', [
//                'data' => $models,
//                'options' => [
//                    'multiple' => false
//                ],
//            ])->label('模型');
            echo $form->field($userModel, 'session_id')->widget('\kartik\select2\Select2', [
                'data' => $sessions,
                'options' => [
                    'multiple' => false
                ],
            ])->label('场次');
            echo $form->field($userModel, 'story_model_id')->widget('\kartik\select2\Select2', [
                'data' => $storyModelDatas,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本模型');
            echo $form->field($userModel, 'use_ct')->textInput(['value' => $userModel->use_ct])->label('剩余次数');

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
