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
    'label' => '知识管理',
];
use yii\web\JsExpression;

$this->title = '知识编辑';
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
            echo $form->field($knowledgeModel, 'title')->textInput(['value' => $knowledgeModel->title])->label('标题');
            echo $form->field($knowledgeModel, 'content')->textarea(['rows' => 20])->label('内容');
            echo $form->field($knowledgeModel, 'suggestion')->textarea(['value' => $knowledgeModel->suggestion, 'rows' => 15])->label('建议对话(json)');
            echo $form->field($knowledgeModel, 'image')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => true,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=knowledge/image/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $knowledgeModel->image],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('图片');

            echo $form->field($knowledgeModel, 'linkurl')->textarea()->label('链接');
            echo $form->field($knowledgeModel, 'voice')->widget('\liyifei\uploadOSS\FileUploadOSS', [
                'multiple' => false,
                'isImage' => false,
                'ossHost' => Yii::$app->params['oss.host'],
                'signatureAction' => ['/site/oss-signature?dir=knowledge/voice/' . Date('Y/m/')],
                'clientOptions' => ['autoUpload' => true],
                'options' => ['value' => $knowledgeModel->voice],
//                'directory' => 'cover/' . Date('Y/m/')
            ])->label('配音');
            echo $form->field($knowledgeModel, 'story_id')->widget('\kartik\select2\Select2', [
                'data' => $stories,
                'options' => [
                    'multiple' => false
                ],
            ])->label('剧本');
            echo $form->field($knowledgeModel, 'story_stage_id')->widget('\kartik\select2\Select2', [
                'data' => $storyStages,
                'options' => [
                    'multiple' => false
                ],
            ])->label('场景');

            echo $form->field($knowledgeModel, 'knowledge_type')->widget('\kartik\select2\Select2', [
                'data' => $knowledgeTypes,
                'options' => [
                    'multiple' => false
                ],
            ])->label('知识类型');
            echo $form->field($knowledgeModel, 'knowledge_class')->widget('\kartik\select2\Select2', [
                'data' => \common\models\Knowledge::$knowledgeClass2Name,
                'options' => [
                    'multiple' => false
                ],
            ])->label('分类');
            echo $form->field($knowledgeModel, 'sort_by')->textInput(['value' => $knowledgeModel->sort_by])->label('排序');
            echo $form->field($knowledgeModel, 'rep_ct')->textInput(['value' => $knowledgeModel->rep_ct])->label('重复次数(0为不限制)');
            if (!empty($knowledgeModel->comp_action)) {
                $compAction = var_export(\common\helpers\Model::decodeDialog($knowledgeModel->comp_action), true);
            }
            echo $form->field($knowledgeModel, 'comp_action')->textarea(['value' => !empty($knowledgeModel->comp_action) ? $compAction: '', 'rows' => 20])->label('完成时动作');
            echo $form->field($knowledgeModel, 'pre_knowledge_id')->textInput(['value' => $knowledgeModel->pre_knowledge_id])->label('上一ID');

//            echo $form->field($knowledgeModel, 'chorus_url')->widget('\liyifei\uploadOSS\FileUploadOSS', [
//                'multiple' => false,
//                'isImage' => false,
//                'ossHost' => Yii::$app->params['oss.host'],
//                'signatureAction' => ['/site/oss-signature?dir=chorus/' . Date('Y/m/')],
//                'clientOptions' => ['autoUpload' => true],
//                'options' => ['value' => $knowledgeModel->chorus_url],
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
