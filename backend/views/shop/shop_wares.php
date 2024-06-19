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
    'label' => '商店管理',
];

$this->title = '商店商品列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/shop/shop_ware_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($shopWaresModel) {
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
                        'label' => '商品名称',
                        'attribute' => 'ware_name',
                        'format' => 'raw',
                        'filter' => Html::activeInput('text', $searchModel, 'ware_name', ['class' => 'form-control']),
                        'value' => function ($model) {
                            $ret = '<img src="' . \common\helpers\Attachment::completeUrl($model->icon, true) . '" width="50">';
                            $ret .= '&nbsp; ';
                            $ret .= \yii\helpers\Html::a($model->ware_name, \yii\helpers\Url::to(['shop/shop_ware_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
                            return $ret;
                        },
                    ],
                    [
                        'label' => '商品类型',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'ware_type',
                            \common\models\ShopWares::$shopWareType2Name, ["class" => "form-control ", 'value' => !empty($params['ShopWares']['ware_type']) ? $params['ShopWares']['ware_type'] : '']),
                        'value' => function ($model) {
                            return !empty(\common\models\ShopWares::$shopWareType2Name[$model->ware_type])
                                ? \common\models\ShopWares::$shopWareType2Name[$model->ware_type] : ' - ';
                        },
                    ],
                    [
                        'label' => '关联数据',
                        'format' => 'raw',
                        'filter' => Html::activeInput('text', $searchModel, 'link_id', ['class' => 'form-control']),
                        'value' => function ($model) {
                            $ret = ' - ';
                            if ($model->link_type == \common\models\ShopWares::LINK_TYPE_STORY_MODEL) {
                                $ret = $model->storyModel->story_model_name;
                            }
                            return $ret;
                        },
                    ],
                    [
                        'label' => '关联数据类型',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $ret = !empty(\common\models\ShopWares::$linkType2Name[$model->link_type]) ? \common\models\ShopWares::$linkType2Name[$model->link_type] : ' - ';
                            return $ret;
                        },
                    ],
                    [
                        'label' => '介绍',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return mb_substr($model->intro, 0, 50, 'UTF-8') . ' ...';
                        },
                    ],
                    [
                        'label' => '库存',
                        'attribute' => 'store_ct',
                    ],
                    [
                        'label' => '原价',
                        'attribute' => 'price',
                    ],
                    [
                        'label' => '支付方式',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'pay_way',
                            \common\models\ShopWares::$payWay2Name, ["class" => "form-control ", 'value' => !empty($params['ShopWares']['pay_way']) ? $params['ShopWares']['pay_way'] : '']),
                        'value' => function ($model) {
                            return !empty(\common\models\ShopWares::$payWay2Name[$model->pay_way])
                                ? \common\models\ShopWares::$payWay2Name[$model->pay_way] : ' - ';
                        }
                    ],
                    [
                        'label' => '折扣',
                        'attribute' => 'discount',
                    ],
                    [
                        'label' => '商品状态',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'ware_status',
                            \common\models\ShopWares::$shopWareStatus2Name, ["class" => "form-control ", 'value' => !empty($params['ShopWares']['ware_status']) ? $params['ShopWares']['ware_status'] : '']),
                        'value' => function ($model) {
                            return !empty(\common\models\ShopWares::$shopWareStatus2Name[$model->ware_status])
                                ? \common\models\ShopWares::$shopWareStatus2Name[$model->ware_status] : ' - ';
                        },
                    ],
                    [
                        'label' => '是否删除',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'is_delete',
                            \common\definitions\Common::$deleteStatus, ["class" => "form-control ", 'value' => !empty($params['ShopWares']['is_delete']) ? $params['ShopWares']['is_delete'] : '']),
                        'value' => function ($model) {
                            return !empty(\common\definitions\Common::$deleteStatus[$model->is_delete])
                                ? \common\definitions\Common::$deleteStatus[$model->is_delete] : ' - ';
                        },
                    ],
                    [
                        'label' => '创建时间',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'date_range',
                            \common\definitions\Common::$dateRange, ["class" => "form-control ", 'value' => !empty($params['ShopWares']['date_range']) ? $params['ShopWares']['date_range'] : '']),
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
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['shop/shop_ware_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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


