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

$this->title = '题包列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/qa/package_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($qaPackageModel) {
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
//                    [
//                        'label' => '剧本',
//                        'attribute' => 'story_id',
//                        'format'    => 'raw',
//                        'value' => function ($model) {
//                            return $model->story->title;
//                        },
//                        'filter' => Html::activeInput('text', $searchModel, 'story_id'),
//                    ],
                    [
                        'label' => '题包分类',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'package_type',
                            \common\models\QaPackage::$packageType2Name, ["class" => "form-control ", 'value' => !empty($params['QaPackage']['package_type']) ? $params['QaPackage']['package_type'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\QaPackage::$packageType2Name[$model->package_type])
                                ? \common\models\QaPackage::$packageType2Name[$model->package_type]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '题包科目',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'package_type',
                            \common\models\QaPackage::$packageClass2Name, ["class" => "form-control ", 'value' => !empty($params['QaPackage']['package_class']) ? $params['QaPackage']['package_class'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\QaPackage::$packageClass2Name[$model->package_class])
                                ? \common\models\QaPackage::$packageClass2Name[$model->package_class]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '题包名称',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'package_name',['placeholder'=>'题包名称']),
                        'value' => function ($model) {
                            return Html::a($model->package_name, '/qa/package_edit?id=' . $model->id);
                        }
                    ],
                    [
                        'label' => '题包描述',
                        'format' => 'raw',
                        'filter'    => false,
                        'value' => function ($model) {
                            return mb_substr($model->package_desc, 0, 20);
                        }
                    ],
                    [
                        'label' => '题目',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'qa_ids',['placeholder'=>'题目']),
                        'value' => function ($model) {
                            if (!empty($model->qa_ids)) {
                                $qaIds = explode(',', $model->qa_ids);
                                $qaIds = array_slice($qaIds, 0, 3);
                                $qas = \common\models\Qa::find()->where(['id' => $qaIds])->all();
                                $ret = '';
                                if (!empty($qas)) {
                                    foreach ($qas as $qa) {
                                        $ret .= $qa->topic . '<br>';
                                    }
                                }
                                return $ret;
                            }
                        }
                    ],
                    [
                        'label' => '年级',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'grade',['placeholder'=>'年级']),
                        'value' => function ($model) {
                            return $model->grade;
                        }
                    ],
                    [
                        'label' => '级别',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'level',['placeholder'=>'级别']),
                        'value' => function ($model) {
                            return $model->level;
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
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['qa/package_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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
echo $form->field($qaPackageModel, 'package_name')->label('标题');
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


