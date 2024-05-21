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
    'label' => '模型管理',
];

$this->title = '用户地点模型列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/model/user_model_loc_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($userModelLoc) {
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
                            $storyName = !empty($model->story->title) ?
                                $model->story->title : '未知';
                            $storyName .= ' [' . $model->story_id . ']';
                            return $storyName;
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_id'),
                    ],
                    [
                        'label' => '用户',
                        'attribute' => 'user_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $userName = !empty($model->user->user_name) ?
                                $model->user->user_name : '未知';
                            $userName .= ' [' . $model->user_id . ']';
                            return $userName;
                        },
                    ],
                    [
                        'label' => '模型',
                        'attribute' => 'model_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = !empty($model->model->model_name) ?
                                $model->model->model_name : '未知';
                            $modelName .= ' [' . $model->model_id . ']';
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/user_model_edit', 'id' => $model->id]));
                        },
                    ],
                    [
                        'label' => '剧本模型',
                        'attribute' => 'story_model_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = !empty($model->storyModel->story_model_name) ?
                                $model->storyModel->story_model_name : '未知';
                            $modelName .= ' [' . $model->story_model_id . ']';
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/user_model_loc_edit', 'id' => $model->id]));
                        },
                    ],
                    [
                        'label' => '位置',
                        'attribute' => 'location_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = !empty($model->location->location_name) ?
                                $model->location->location_name : '未知';
                            $modelName .= ' [' . $model->location_id . ']';
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/user_model_loc_edit', 'id' => $model->id]));
                        },
                    ],
                    [
                        'label' => '状态',
                        'attribute' => 'user_model_loc_status',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\UserModelLoc::$userModelLocStatus2Name[$model->user_model_loc_status])
                                ? \common\models\UserModelLoc::$userModelLocStatus2Name[$model->user_model_loc_status] : '未知';
                        },
                    ],
                    [
                        'label' => '是否删除',
                        'attribute' => 'is_delete',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            if ($model->is_delete == \common\definitions\Common::STATUS_DELETED) {
                                return '是';
                            } else {
                                return '否';
                            }
//                            return $model->is_delete == 0 ? '否' : '是';
                        },
                    ],
                    [
                        'label' => '创建时间',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                            return Date('Y-m-d H:i:s', $model->created_at);
                        }
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
                        'template' => '{lines} {edit} {delete} {reset}',
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['model/user_model_loc_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
                            },
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
                            'reset' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('恢复', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'reset',
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
    <div class="form-group">
        <label class="control-label col-sm-2"></label>
        <div class="col-sm-9">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php
ActiveForm::end();
Modal::end();


