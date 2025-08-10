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
    'label' => '电子书管理',
];
use yii\web\JsExpression;

$this->title = '电子书编辑';
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
            echo $form->field($ebookModel, 'story_id')->textInput(['value' => $ebookModel->story_id])->label('故事ID');

            if (!empty($ebookModel->story_params)) {
                $tmpTxt = var_export(\common\helpers\Model::decodeDialog($ebookModel->story_params), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $tmpTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $tmpTxt) . ';';
            } else {
                $tmpTxt = '';
            }
            echo $form->field($ebookModel, 'story_params')->textarea(['value' => $tmpTxt, 'rows' => 15])->label('故事Json');

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

