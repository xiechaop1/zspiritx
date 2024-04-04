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
    'label' => '用户奖券管理',
];

$this->title = '用户奖券列表';
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
                'afterRow' => function ($model, $key, $index) use ($userLotteryModel) {
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
                    echo $form->field($userLotteryModel, 'story_id')->textInput(['value' => $model->story_id])->label('剧本ID');
                    echo $form->field($userLotteryModel, 'session_id')->textInput(['value' => $model->session_id])->label('场次ID');
                    echo $form->field($userLotteryModel, 'user_id')->textInput(['value' => $model->user_id])->label('用户ID');
                    echo $form->field($userLotteryModel, 'lottery_id')->textInput(['value' => $model->lottery_id])->label('抽奖活动ID');
                    echo $form->field($userLotteryModel, 'lottery_no')->textInput(['value' => $model->lottery_no])->label('核销码');
                    echo $form->field($userLotteryModel, 'ct')->textInput(['value' => $model->ct])->label('次数');
                    ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2"></label>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
                    </div>
                    <?php
                    echo Html::hiddenInput('id', $model->id);
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
                        'attribute' => 'story_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->story->title) ? $model->story->title : '未知';
                        },
                        'filter' => false
                    ],
                    [
                        'label' => '用户',
                        'attribute' => 'user_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->user->user_name) ? $model->user->user_name : '未知';
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'user_name',['placeholder'=>'用户名']),
                    ],
                    [
                        'label' => '手机号',
                        'attribute' => 'mobile',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->user->mobile) ? $model->user->mobile : '未知';
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'mobile',['placeholder'=>'手机号']),
                    ],
                    [
                        'label' => '场次',
                        'attribute' => 'session_id',
                        'filter' => false
                    ],
                    [
                        'label' => '奖券活动',
                        'attribute' => 'lottery_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->lottery->lottery_name) ? $model->lottery->lottery_name : '未知';
                        },
                        'filter' => false
                    ],
                    [
                        'label' => '次数',
                        'attribute' => 'ct',
                        'filter' => false,
                    ],
                    [
                        'label' => '核销码',
                        'attribute' => 'lottery_no',
                        'filter' => Html::activeInput('text', $searchModel, 'lottery_no',['placeholder'=>'核销码']),
                    ],
                    [
                        'label' => '奖品状态',
                        'attribute' => 'lottery_status',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\UserLottery::$userLotteryStatus2Name[$model->lottery_status])
                                ? \common\models\UserLottery::$userLotteryStatus2Name[$model->lottery_status] : '未知';
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'lottery_status',
                            \common\models\UserLottery::$userLotteryStatus2Name, ["class" => "form-control ",
                                'value' => !empty($params['UserLottery']['lottery_status']) ? $params['UserLottery']['lottery_status'] : '']),
                    ],
                    [
                        'label' => '过期时间',
                        'attribute' => 'expire_time',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->expire_time) ?
                                Date('Y-m-d H:i:s', $model->expire_time) : '永久';
                        },
                        'filter' => false
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
                            \common\definitions\Common::$dateRange, ["class" => "form-control ", 'value' => !empty($params['UserLottery']['date_range']) ? $params['UserLottery']['date_range'] : '']),
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
                        'template' => '{lines} {rece} {wait} {cancel} {edit} {delete}',
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', 'javascript:void(0);', [
                                    'class' => 'btn btn-xs btn-primary',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#case-form-' . $model->id
                                ]);
                            },
//                            'detail' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('详情', \yii\helpers\Url::to(['qa/detail', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
//                            },
                            'rece' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('使用', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'used',
                                    'data-id' => $model->id
                                ]);
                            },
                            'cancel' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('取消', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'cancel',
                                    'data-id' => $model->id
                                ]);
                            },
                            'wait' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('等待', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'wait',
                                    'data-id' => $model->id
                                ]);
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
echo Html::hiddenInput('data-action', 'generate');
echo $form->field($userLotteryModel, 'story_id')->textInput()->label('剧本ID');
echo $form->field($userLotteryModel, 'session_id')->textInput()->label('场次ID');
echo $form->field($userLotteryModel, 'user_id')->textInput()->label('用户ID');
echo $form->field($userLotteryModel, 'lottery_id')->textInput()->label('抽奖活动ID');
//echo $form->field($userLotteryModel, 'lottery_no')->textInput()->label('核销码');
echo $form->field($userLotteryModel, 'ct')->textInput()->label('次数');
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


