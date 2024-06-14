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
    'label' => '诗词管理',
];

$this->title = '诗词列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/qa/poem_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($poemModel) {
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
//                        'filter'    => Html::activeInput('text', $searchModel, 'id'),
                    ],
                    [
                        'label' => '类型',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'poem_type',
                            $poemTypes, ["class" => "form-control ", 'value' => !empty($params['Poem']['poem_type']) ? $params['Poem']['poem_type'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\Poem::$poemType2Name[$model->poem_type])
                                ? \common\models\Poem::$poemType2Name[$model->poem_type]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '图片',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                            return Html::img(\common\helpers\Attachment::completeUrl($model->image), ['width' => 75, 'height' => 75]);
                        }
                    ],
                    [
                        'label' => '标题',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'title',['placeholder'=>'标题']),
                        'value' => function ($model) {
                            return Html::a($model->title, '/qa/poem_edit?id=' . $model->id);
                        }
                    ],
                    [
                        'label' => '分类',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'poem_class',
                            $poemClass, ["class" => "form-control ", 'value' => !empty($params['Poem']['poem_class']) ? $params['Poem']['poem_class'] : '', ]),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\Poem::$poemClass2Name[$model->poem_class])
                                ? \common\models\Poem::$poemClass2Name[$model->poem_class]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '分类2',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'poem_class2',
                            $poemClass2, ["class" => "form-control ", 'value' => !empty($params['Poem']['poem_class2']) ? $params['Poem']['poem_class2'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\Poem::$poemClass22Name[$model->poem_class2])
                                ? \common\models\Poem::$poemClass22Name[$model->poem_class2]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '等级',
                        'format' => 'raw',
                        'filter' =>  Html::activeInput('text', $searchModel, 'level',['placeholder'=>'等级', 'size' => 5]),
                        'value' => function ($model) {
                            return $model->level;
                        }
                    ],
                    [
                        'label' => '内容',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                            return mb_substr($model->content, 0, 50) . ' ...';
                        }
                    ],
                    [
                        'label' => '故事',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                            return mb_substr($model->story, 0, 50) . ' ...';
                        }
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
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['qa/poem_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
                            },
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
echo $form->field($poemModel, 'title')->label('标题');
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


