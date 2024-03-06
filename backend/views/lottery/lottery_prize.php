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
    'label' => '抽奖管理',
];

$this->title = '抽奖列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/lottery/lottery_prize_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($lotteryPrizeModel) {
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
                        'label' => '抽奖活动',
                        'attribute' => 'lottery_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                if (empty($model->lottery)) {
                    return '未知';
                }
                            return $model->lottery->lottery_name;
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'lottery_id'),
                    ],
                    [
                        'label' => '奖品名称',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'prize_name',['placeholder'=>'奖品名称']),
                        'value' => function ($model) {
                            return Html::a($model->prize_name, '/lottery/lottery_prize_edit?id=' . $model->id);
                        }
                    ],
                    [
                        'label' => '奖品类型',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return \common\models\LotteryPrize::$prizeType2Name[$model->prize_type];
                        },
                        'filter' => false,
                    ],
                    [
                        'label' => '奖品等级',
                        'attribute' => 'prize_level',
                    ],
                    [
                        'label' => '奖品等级名称',
                        'attribute' => 'prize_level_name',
                    ],
                    [
                        'attribute' => 'story_model_id',
                        'label' => '模型ID',
                    ],
                    [
                        'label' => '缩略图',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $img = \common\helpers\Attachment::completeUrl($model->thumbnail);
                            $ret = Html::img($img, ['width' => 75, 'height' => 75]);

                            return $ret;
                        },
                    ],
                    [
                        'label' => '奖品状态',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'prize_status',
                            \common\models\LotteryPrize::$lotteryPrizeStatus2Name, ["class" => "form-control ", 'value' => !empty($params['LotteryPrize']['prize_status']) ? $params['LotteryPrize']['prize_status'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\LotteryPrize::$lotteryPrizeStatus2Name[$model->prize_status])
                                ? \common\models\LotteryPrize::$lotteryPrizeStatus2Name[$model->prize_status]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'attribute' => 'total_ct',
                        'label' => '总数',
                        'filter' => false,
                    ],
                    [
                        'label' => '间隔类型',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'interval_type',
                            \common\models\LotteryPrize::$intervalType2Name, ["class" => "form-control ", 'value' => !empty($params['LotteryPrize']['interval_type']) ? $params['LotteryPrize']['interval_type'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\LotteryPrize::$intervalType2Name[$model->interval_type])
                                ? \common\models\LotteryPrize::$intervalType2Name[$model->interval_type]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'attribute' => 'interval_ct',
                        'label' => '时间间隔内最大数',
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'rate',
                        'label' => '中奖概率',
                        'filter' => false,
                    ],
                    [
                        'label' => '中奖方式',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'prize_method',
                            \common\models\LotteryPrize::$prizeMethod2Name, ["class" => "form-control ", 'value' => !empty($params['LotteryPrize']['prize_method']) ? $params['LotteryPrize']['prize_method'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\LotteryPrize::$prizeMethod2Name[$model->prize_method])
                                ? \common\models\LotteryPrize::$prizeMethod2Name[$model->prize_method]
                                : '未知';

                            return $ret;

                        }
                    ],

//                    [
//                        'label' => '中奖条件',
//                        'format' => 'raw',
//                        'filter'    => false,
//                        'value' => function ($model) {
//                            return \common\helpers\Common::isJson($model->prize_option) ? json_decode($model->prize_option, true) : $model->prize_option;
//                        }
//                    ],

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
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['lottery/lottery_prize_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
                            },
//                            'detail' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('详情', \yii\helpers\Url::to(['lottery/detail', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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
echo $form->field($lotteryPrizeModel, 'prize_name')->label('标题');
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


