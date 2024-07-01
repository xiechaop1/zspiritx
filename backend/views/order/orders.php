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
    'label' => '订单管理',
];

$this->title = '订单列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/order/edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'filterRowOptions' => ['class' => 'filters'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($orderModel) {
                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '编辑订单',
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
                    echo $form->field($orderModel, 'order_status')->widget('\kartik\select2\Select2', [
                        'data'  => $orderModel::$orderStatus,
                        'options' => [
                            'id'    => 'order_status',
                            'multiple' => false,
                            'value' => $model->order_status,
                        ],
                    ])->label('订单状态');
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
                        'filter'    => Html::activeInput('text', $searchModel, 'id', ['size' => 5]),
                    ],
                    [
                        'label' => '剧本',
                        'attribute' => 'story_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return $model->story->title;
                        },
                        'filter'    => Html::activeInput('text', $searchModel, 'story_id', ['value' => !empty($params['Order']['story_id']) ? $params['Order']['story_id'] : '']),
                    ],
                    [
                        'label' => '手机号',
                        'attribute' => 'user_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return '[ID: ' . $model->user_id .'] ' . !empty($model->user->mobile) ? $model->user->mobile : ' - ';
                        },
                        'filter'    => Html::activeInput('text', $searchModel, 'user_id', ['value' => !empty($params['Order']['user_id']) ? $params['Order']['user_id'] : '']),
                    ],
                    [
                        'label' => '支付金额',
                        'attribute' => 'amount',
                    ],
                    [
                        'label' => '剧本原金额',
                        'attribute' => 'story_price',
                    ],
                    [
                        'attribute' => 'pay_method',
                        'label' => '支付方式',
                        'value' => function($model) {
                            return
                                isset (\common\models\Order::$payMethod2Name[$model->pay_method]) ?
                                    \common\models\Order::$payMethod2Name[$model->pay_method] :
                                    '未知'
                                ;
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'pay_method',
                            \common\models\Order::$payMethod2Name, ["class" => "form-control ", 'value' => isset($params['Order']['pay_method']) ? $params['Order']['pay_method'] : ''])
                    ],
                    [
                        'label' => '核销码',
                        'attribute' => 'ver_code',
                    ],
                    [
                        'label' => '核销平台',
                        'attribute' => 'ver_platform',
                    ],
                    [
                        'attribute' => 'order_status',
                        'label' => '订单状态',
                        'value' => function($model) {
                            return
                                isset (\common\models\Order::$orderStatus[$model->order_status]) ?
                                    \common\models\Order::$orderStatus[$model->order_status] :
                                    '未知'
                                ;
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'order_status',
                            $orderStatusList, ["class" => "form-control ", 'value' => isset($params['Order']['order_status']) ? $params['Order']['order_status'] : ''])
                    ],
                    [
                        'attribute' => 'is_delete',
                        'label' => '是否删除',
                        'value' => function($model) {
                            switch($model->is_delete) {
                                case \common\definitions\Common::STATUS_DELETED:
                                    $r = '已删除';
                                    break;
                                case \common\definitions\Common::STATUS_NORMAL:
                                    $r = '正常';
                                    break;
                                default:
                                    $r = '未知';
                            }
                            return $r;
                        },
                    ],
                    [
                        'label' => '过期时间',
                        'format' => 'raw',
//                        'filter'    => Html::activeInput('text', $searchModel, 'created_at'),
                        'value' => function ($model) {
                            if ($model->expire_time <= 0) {
                                return '-';
                            }
                            $ret = Date('Y-m-d H:i:s', $model->expire_time);
                            $ret .= "<br>剩余：" . \common\helpers\Time::minute2friendly(((int)$model->expire_time - (int)time())/60);
                            return $ret;
                        }
                    ],
//                    [
//                        'label' => '剩余时间',
//                        'format' => 'raw',
////                        'filter'    => Html::activeInput('text', $searchModel, 'created_at'),
//                        'value' => function ($model) {
//                            if ($model->expire_time <= 0) {
//                                return '-';
//                            }
//                            return \common\helpers\Time::minute2friendly(((int)$model->expire_time - (int)time())/60);
//                        }
//                    ],
                    [
                        'label' => '创建时间',
                        'format' => 'raw',
//                        'filter'    => Html::activeInput('text', $searchModel, 'created_at'),
                        'value' => function ($model) {
                            return Date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'date_range',
                            \common\definitions\Common::$dateRange, ["class" => "form-control ", 'value' => !empty($params['Order']['date_range']) ? $params['Order']['date_range'] : '']),
                    ],
                    [
                        'label' => '更改时间',
                        'format' => 'raw',
//                        'filter'    => Html::activeInput('text', $searchModel, 'updated_at'),
                        'value' => function ($model) {
                            return Date('Y-m-d H:i:s', $model->updated_at);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'template' => '{lines} {confirm} {edit} {delete}',
                        'buttons' => [
                            'confirm' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('确认', 'javascript:void(0)', [
                                    'class' => 'btn btn-primary btn-xs ajax-status-btn',
                                    'request-confirm' => '确认订单吗?',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'confirm',
                                    'data-id' => $model->id,
                                    'data-value' => '',
                                ]);
                            },
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['order/edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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
echo $form->field($orderModel, 'order_status')->widget('\kartik\select2\Select2', [
    'data'  => $orderStatus,
    'options' => [
        'id'    => 'singer_id',
        'multiple' => false
    ],
])->label('订单状态');
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


