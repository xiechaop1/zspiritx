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

$this->title = '剧本模型关联列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
<!--            --><?php //= \yii\bootstrap\Html::button('添加', [
//                'class' => 'btn btn-primary pull-right',
//                'data-toggle' => "modal",
//                'data-target' => '#add-form'
//            ]) ?>
            <?= \yii\bootstrap\Html::a('添加', '/model/story_model_link_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($storyModelLink
//                    ,$storyList, $storyModelList
                ) {
                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '查看详情',
                        'options' => [
                            'id' => 'case-form-' . $model->id
                        ]]);
                    $form = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'enableClientValidation' => true,
                        'enableAjaxValidation' => true,
                        'enableClientScript' => true,
                        'fieldConfig' => [
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-2',
                                'offset' => 'col-sm-offset-1',
                                'wrapper' => 'col-sm-6',
                            ],
                        ],
                    ]);
                    ?>
                    <?php
//                    echo $form->field($storyModelLink, 'story_id')->dropDownList($storyList, ['value' => $model->story_id])->label('剧本');
//                    echo $form->field($storyModelLink, 'story_model_id')->dropDownList($storyModelList, ['value' => $model->story_model_id])->label('模型');
//                    echo $form->field($storyModelLink, 'story_model_id2')->dropDownList($storyModelList, ['value' => $model->story_model_id2])->label('关联模型');
//                    echo $form->field($storyModelLink, 'group_name')->textInput(['value' => $model->group_name])->label('分组名称');
//                    echo $form->field($storyModelLink, 'eff_type')->dropDownList(\common\models\StoryModelsLink::$effType2Name, ['value' => $model->eff_type])->label('类型');
//                    echo $form->field($storyModelLink, 'eff_exec')->textarea(['value' => var_export(json_decode($model->eff_exec, true), true), 'rows' => 15])->label('执行');
                    ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2"></label>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
                    </div>
                    <?php
                    echo Html::hiddenInput('data-id', $model->id);
                    ActiveForm::end();
                    Modal::end();
                },
                'columns' => [
                    [
                        'attribute' => 'id',
//                        'filter'    => Html::activeInput('text', $searchModel, 'id'),
                    ],
                    [
                        'label' => '剧本',
                        'attribute' => 'title',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $title = !empty($model->story->title) ?
                                $model->story->title : '-';
                            return $title;
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_id'),
                    ],
                    [
                        'label' => '模型',
                        'attribute' => 'story_model_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = '';
                            if ($model->story_model_id == '-1') {
                                $modelName = '未匹配';
                            }
                            else if ($model->story_model_id == '-2') {
                                $modelName = '部分匹配';
                            } else {
                                if (!empty($model->storyModel)) {
                                    $modelName = !empty($model->storyModel->story_model_name) ?
                                        $model->storyModel->story_model_name : $model->storyModel->model->model_name;
                                    $modelName .= ' [' . $model->story_model_id . '|' . $model->story_model_detail_id . ']';
                                }
                            }
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/story_model_link_edit', 'id' => $model->id]));
//                            return \yii\helpers\Html::a($modelName, 'javascript:void(0);', [
////                                'class' => 'btn btn-xs btn-primary',
//                                'data-toggle' => 'modal',
//                                'data-target' => '#case-form-' . $model->id
//                            ]);
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_model_id'),
                    ],
                    [
                        'label' => '关联模型(未匹配填-1，部分匹配-2)',
                        'attribute' => 'story_model_id2',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = '';
                            if ($model->story_model_id2 == '-1') {
                                $modelName = '未匹配';
                            }
                            else if ($model->story_model_id2 == '-2') {
                                $modelName = '部分匹配';
                            } else {
                                if (!empty($model->storyModel2)) {
                                    $modelName = !empty($model->storyModel2->story_model_name) ?
                                        $model->storyModel2->story_model_name : $model->storyModel2->model->model_name;
                                    $modelName .= ' [' . $model->story_model_id2 . '|' . $model->story_model_detail_id2 . ']';
                                }
                            }
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/story_model_link_edit', 'id' => $model->id]));
//                            return \yii\helpers\Html::a($modelName, 'javascript:void(0);', [
////                                'class' => 'btn btn-xs btn-primary',
//                                'data-toggle' => 'modal',
//                                'data-target' => '#case-form-' . $model->id
//                            ]);
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_model_id2'),
                    ],
                    [
                        'label' => '分组',
                        'attribute' => 'group_name',
                        'filter' => Html::activeInput('text', $searchModel, 'group_name'),
                    ],
                    [
                        'label' => '标志性',
                        'attribute' => 'is_tag',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\StoryModelsLink::$isTag2Name[$model->is_tag]) ?
                                \common\models\StoryModelsLink::$isTag2Name[$model->is_tag] : ' - ';
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'is_tag'),
                    ],
                    [
                        'label' => '效果类型',
                        'attribute' => 'eff_type',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\StoryModelsLink::$effType2Name[$model->eff_type]) ?
                                \common\models\StoryModelsLink::$effType2Name[$model->eff_type] : ' - ';
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'eff_type'),
                    ],
                    [
                        'label' => '执行',
                        'attribute' => 'eff_exec',
                        'value' => function ($model) {
                            $ret = substr($model->eff_exec, 0, 50);
                            if (strlen($model->eff_exec) > 50)
                                $ret .= '...';
                            return $ret;
                        },
                        'filter' => false,
                    ],
                    [
                        'label' => '更新时间',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                            return Date('Y-m-d H:i:s', $model->updated_at);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{lines} {edit} {copy} {delete}',
                        'buttons' => [
//                            'edit' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('编辑', 'javascript:void(0);', [
//                                    'class' => 'btn btn-xs btn-primary',
//                                    'data-toggle' => 'modal',
//                                    'data-target' => '#case-form-' . $model->id
//                                ]);
//                            },
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['model/story_model_link_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
                            },
                            'copy' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('复制', 'javascript:void(0)', [
                                    'class' => 'btn btn-primary btn-xs ajax-status-btn',
                                    'request-confirm' => '确认复制一份吗?',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'copy',
                                    'data-id' => $model->id,
                                ]);
                            },
//                            'edit' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['model/story_stage_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
//                            },
//                            'detail' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('详情', \yii\helpers\Url::to(['qa/detail', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
//                            },
                            'delete' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('删除', [
                                    'class' => 'btn btn-xs btn-danger delete_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'delete',
                                    'data-id' => $model->id
                                ]);
                            },

                        ],
                    ]
                ],

            ]);
            ?>
        </div>
    </div>

<?php
Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'options' => [
        'id' => 'add-form'
    ]
]);
$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'enableClientScript' => true,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-9',
        ],
    ],
]);
?>
<?php
//echo $form->field($storyModelLink, 'story_id')->dropDownList($storyList)->label('剧本');
//echo $form->field($storyModelLink, 'story_model_id')->dropDownList($storyModelList)->label('模型');
//echo $form->field($storyModelLink, 'story_model_id2')->dropDownList($storyModelList)->label('关联模型');
//echo $form->field($storyModelLink, 'group_name')->label('分组名称');
//echo $form->field($storyModelLink, 'eff_type')->dropDownList(\common\models\StoryModelsLink::$effType2Name)->label('类型');
//echo $form->field($storyModelLink, 'eff_exec')->textarea(['rows' => 15])->label('执行');

?>
    <div class="form-group">
        <label class="control-label col-sm-2"></label>
        <div class="col-sm-9">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php
ActiveForm::end();
Modal::end();


