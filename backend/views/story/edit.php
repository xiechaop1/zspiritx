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
    'label' => '剧本管理',
];
use yii\web\JsExpression;

$this->title = '剧本编辑';
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
            echo $form->field($storyModel, 'title')->textInput(['value' => $storyModel->title])->label('标题');
            echo $form->field($storyModel, 'desc')->textarea(['value' => $storyModel->desc, 'rows' => 15])->label('简介');
            echo $form->field($storyModel, 'story_type')->widget('\kartik\select2\Select2', [
                'data' => $storyTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('类型');
            echo $form->field($storyModel, 'thumbnail')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=story/thumbnail/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModel->thumbnail],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('缩略图');
            echo $form->field($storyModel, 'cover_image')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=story/cover_image/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $storyModel->thumbnail],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图标');
            if (!empty($storyModel->story_bg)) {
                $tmpTxt = var_export(\common\helpers\Model::decodeDialog($storyModel->story_bg), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $tmpTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $tmpTxt) . ';';
            } else {
                $tmpTxt = '';
            }
            echo $form->field($storyModel, 'story_bg')->textarea(['value' => $tmpTxt, 'rows' => 15])->label('故事背景(json)');
            if (!empty($storyModel->guide)) {
                $tmpTxt = var_export(\common\helpers\Model::decodeDialog($storyModel->guide), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $tmpTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $tmpTxt) . ';';
            } else {
                $tmpTxt = '';
            }
            echo $form->field($storyModel, 'guide')->textarea(['value' => $tmpTxt, 'rows' => 15])->label('游戏方式介绍(json)');
            if (!empty($storyModel->resources)) {
                $tmpTxt = var_export(\common\helpers\Model::decodeDialog($storyModel->resources), true);
                // 去掉数组中下标
                // 让数组内容在textarea中文本显示
                $tmpTxt = preg_replace('/\s*\d+\s*=>\s*/', "\n", $tmpTxt) . ';';
            } else {
                $tmpTxt = '';
            }
            echo $form->field($storyModel, 'resources')->textarea(['value' => $tmpTxt, 'rows' => 15])->label('资源(ios,android,huawei)(json)');
            echo $form->field($storyModel, 'latest_unity_version')->textInput(['value' => $storyModel->latest_unity_version])->label('最低版本');
            echo $form->field($storyModel, 'persons_ct')->textInput(['value' => $storyModel->persons_ct])->label('人数');
            echo $form->field($storyModel, 'roles_ct')->textInput(['value' => $storyModel->roles_ct])->label('角色数');
            echo $form->field($storyModel, 'is_debug')->textInput(['value' => $storyModel->is_debug])->label('是否测试');
            echo $form->field($storyModel, 'sort_by')->textInput(['value' => $storyModel->sort_by])->label('排序');
            echo $form->field($storyModel, 'story_status')->widget('\kartik\select2\Select2', [
                'data' => \common\models\Story::$storyStatus2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本状态');

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

