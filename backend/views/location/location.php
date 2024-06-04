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
    'label' => '地盘管理',
];

$this->title = '地盘列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::button('添加', [
                'class' => 'btn btn-primary pull-right',
                'data-toggle' => "modal",
                'data-target' => '#add-form'
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($locationModel) {
                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '查看日志',
                        'options' => [
                            'id' => 'edit-form-' . $model->id
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
                    echo $form->field($model, 'location_name')->label('地盘名称');
                    echo $form->field($model, 'location_type')->label('地盘类型');
                    echo $form->field($model, 'lng')->label('经度');
                    echo $form->field($model, 'lat')->label('纬度');
                    echo $form->field($model, 'amap_prop')->textarea([
                        'value' => !empty($model->amap_prop) ? var_export(json_decode($model->amap_prop, true), true) : '',
                        'rows' => 20,
                    ])->label('高德属性');
                    echo $form->field($model, 'address')->label('地址');
                    echo $form->field($model, 'businessarea')->label('商圈');
                    echo $form->field($model, 'adcode')->label('区域编码');
                    echo $form->field($model, 'tel')->label('电话');
                    echo $form->field($model, 'aoi_type')->label('aoi类型');
                    echo $form->field($model, 'resource')->label('资源');


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
                        'label' => '标题',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'location_name',['placeholder'=>'标题']),
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->location_name, 'javascript:void(0)', [
                                'data-toggle' => 'modal',
                                'data-target' => '#edit-form-' . $model->id
                            ]);
                        }
                    ],
                    [
                        'label' => '地盘类型',
                        'attribute' => 'location_type',
                    ],
                    [
                        'label' => '经度',
                        'attribute' => 'lng',
                    ],
                    [
                        'label' => '纬度',
                        'attribute' => 'lat',
                    ],
                    [
                        'label' => '地址',
                        'attribute' => 'address',
                    ],
                    [
                        'label' => '商圈',
                        'attribute' => 'businessarea',
                    ],
                    [
                        'label' => '区域编码',
                        'attribute' => 'adcode',
                    ],
                    [
                        'label' => '电话',
                        'attribute' => 'tel',
                    ],
                    [
                        'label' => 'aoi类型',
                        'attribute' => 'aoi_type',
                    ],
                    [
                        'label' => '高德属性',
                        'attribute' => 'amap_prop',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return mb_substr($model->amap_prop, 0, 50, 'utf-8') . ' ...';
                        }
                    ],
                    [
                        'label' => '资源',
                        'attribute' => 'resource',
                    ],
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
                                return \yii\helpers\Html::a('编辑', 'javascript:void(0)', [
                                    'class' => 'btn btn-xs btn-primary',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#edit-form-' . $model->id
                                ]);
                            },
//                            'detail' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('详情', \yii\helpers\Url::to(['location/detail', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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
echo $form->field($locationModel, 'location_name')->label('标题');
echo $form->field($locationModel, 'location_type')->label('地盘类型');
echo $form->field($locationModel, 'lng')->label('经度');
echo $form->field($locationModel, 'lat')->label('纬度');
echo $form->field($locationModel, 'amap_prop')->textarea([
    'rows' => 20,
])->label('高德属性');
echo $form->field($locationModel, 'address')->label('地址');
echo $form->field($locationModel, 'businessarea')->label('商圈');
echo $form->field($locationModel, 'adcode')->label('区域编码');
echo $form->field($locationModel, 'tel')->label('电话');
echo $form->field($locationModel, 'aoi_type')->label('aoi类型');
echo $form->field($locationModel, 'resource')->label('资源');

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


