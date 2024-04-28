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

$this->title = '剧本模型列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/model/story_model_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($storyModel) {
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
                            return !empty($model->story->title) ?
                                $model->story->title : '未知';
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_id'),
                    ],
                    [
                        'attribute' => 'story_stage_id',
                        'label' => 'Stage ID',
                    ],
                    [
                        'attribute' => 'model_inst_u_id',
                        'label' => 'Model Inst UnityID',
                    ],
                    [
                        'attribute' => 'icon',
                        'label' => '图标',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->icon) ?
                                Html::img( \common\helpers\Attachment::completeUrl($model->icon, true), ['width' => '50px']) : ' - ';
                        },
//                        'filter' => Html::activeInput('text', $searchModel, 'story_model_id'),
                    ],
                    [
                        'attribute' => 'story_model_name',
                        'label' => '剧本模型',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = !empty($model->story_model_name) ?
                                $model->story_model_name : '未知';
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/story_model_edit', 'id' => $model->id]));
                        },
//                        'filter' => Html::activeInput('text', $searchModel, 'story_model_id'),
                    ],
                    [
                        'label' => '模型',
                        'attribute' => 'model_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = !empty($model->model->model_name) ?
                                $model->model->model_name : '未知';
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/story_model_edit', 'id' => $model->id]));
                        },
                    ],
                    [
                        'label' => '模型详情',
                        'attribute' => 'story_model_detail_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $modelName = !empty($model->detail->title) ?
                                $model->detail->title : '未知/无';
                            return \yii\helpers\Html::a($modelName, \yii\helpers\Url::to(['model/story_model_detail', 'id' => $model->story_model_detail_id]));
                        },
                    ],
                    [
                        'label' => '对话',
                        'attribute' => 'dialog',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->dialog) ?
                                mb_substr($model->dialog, 0, 100) . '...' : '-';
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'dialog'),
                    ],
                    [
                        'attribute' => 'model_group',
                        'label' => '模型组',
                    ],
                    [
                        'attribute' => 'scan_image_id',
                        'label' => 'Scan Image ID',
                    ],
                    [
                        'label' => 'Scan Image Type',
                        'attribute' => 'scan_type',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\StoryModels::$scanImageType2Name[$model->scan_type]) ?
                                \common\models\StoryModels::$scanImageType2Name[$model->scan_type] : '未知';
                        },
                    ],
                    [
                        'attribute' => 'lat',
                        'label' => '经度',
                    ],
                    [
                        'attribute' => 'lng',
                        'label' => '纬度',
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
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['model/story_model_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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


