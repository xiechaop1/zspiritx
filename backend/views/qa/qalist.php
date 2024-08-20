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
    'label' => '问答管理',
];

$this->title = '问答列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/qa/edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($qaModel) {
                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '查看日志',
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
                        'filter'    => Html::activeInput('text', $searchModel, 'id', ['size' => 3]),
                    ],
                    [
                        'label' => '剧本',
                        'attribute' => 'story_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return $model->story->title;
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_id'),
                    ],
                    [
                        'label' => '题目类型',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'qa_type',
                            $qaTypes, ["class" => "form-control ", 'value' => !empty($params['Qa']['qa_type']) ? $params['Qa']['qa_type'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\Qa::$qaType2Name[$model->qa_type])
                                ? \common\models\Qa::$qaType2Name[$model->qa_type]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '题目分类',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'qa_class',
                            $qaClass, ["class" => "form-control ", 'value' => !empty($params['Qa']['qa_class']) ? $params['Qa']['qa_class'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\Qa::$qaClass2Name[$model->qa_class])
                                ? \common\models\Qa::$qaClass2Name[$model->qa_class]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '标题',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'topic',['placeholder'=>'标题']),
                        'value' => function ($model) {
                            return Html::a($model->topic, '/qa/edit?id=' . $model->id);
                        }
                    ],
//                    [
//                        'label' => '选项',
//                        'format' => 'raw',
//                        'filter'    => false,
//                        'value' => function ($model) {
//                            return \common\helpers\Common::isJson($model->selected) ? json_decode($model->selected, true) : $model->selected;
//                        }
//                    ],
                    [
                        'label' => '答案选项',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                                return $model->st_selected;
//                            return \common\helpers\Common::isJson($model->st_selected) ? json_decode($model->st_selected, true) : $model->st_selected;
                        }
                    ],
                    [
                        'attribute' => 'level',
                        'label' => '等级',
                    ],
                    [
                        'label' => '分类',
                        'value' => function ($model) {
                            return !empty(\common\models\Qa::$qaClass2Name[$model->qa_class])
                                ? \common\models\Qa::$qaClass2Name[$model->qa_class] : '未知';

                        },
                    ],
                    [
                        'label' => '模式',
                        'value' => function ($model) {
                            return !empty(\common\models\Qa::$qaMode2Name[$model->qa_mode])
                                ? \common\models\Qa::$qaMode2Name[$model->qa_mode] : '未知';
                        },
                    ],
                    [
                        'label' => '类型',
                        'value' => function ($model) {
                            return !empty(\common\models\Qa::$qaType2Name[$model->qa_type])
                                ? \common\models\Qa::$qaType2Name[$model->qa_type] : '未知';
                        },
                    ],

//                    [
//                        'label' => '封面图片',
//                        'attribute' => 'verse_url',
//                        'format'    => 'raw',
//                        'value' => function ($model) {
//                            $img = \common\helpers\Attachment::completeUrl($model->verse_url);
//                            $ret = Html::img($img, ['width' => 150, 'height' => 75]);
//
//                            return $ret;
//                        },
//                        'filter' => false
//                    ],
                    [
                        'label' => '创建时间',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'date_range',
                            \common\definitions\Common::$dateRange, ["class" => "form-control ", 'value' => !empty($params['Music']['date_range']) ? $params['Music']['date_range'] : '']),
                        'value' => function ($model) {
                            return Date('Y-m-d H:i:s', $model->created_at);
                        },
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
                        'template' => '{lines} {edit} {delete}',
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['qa/edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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
echo $form->field($qaModel, 'topic')->label('标题');
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


