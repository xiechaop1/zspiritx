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
    'label' => '用户抽奖管理',
];

$this->title = '用户抽奖列表';
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
                'afterRow' => function ($model, $key, $index) use ($userPrizeModel) {
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
                        'filter' => false
                    ],
                    [
                        'label' => '场次',
                        'attribute' => 'session_id',
                        'filter' => false
                    ],
                    [
                        'label' => '抽奖活动',
                        'attribute' => 'lottery_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->lottery->lottery_name) ? $model->lottery->lottery_name : '未知';
                        },
                        'filter' => false
                    ],
                    [
                        'label' => '核销码',
                        'attribute' => 'user_prize_no',
                        'filter' => Html::activeInput('text', $searchModel, 'user_prize_no',['placeholder'=>'核销码']),
                    ],
                    [
                        'label' => '奖品状态',
                        'attribute' => 'user_prize_status',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\UserPrize::$userPrizeStatus2Name[$model->user_prize_status])
                                ? \common\models\UserPrize::$userPrizeStatus2Name[$model->user_prize_status] : '未知';
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'user_prize_status',
                            \common\models\UserPrize::$userPrizeStatus2Name, ["class" => "form-control ",
                                'value' => !empty($params['UserPrize']['user_prize_status']) ? $params['UserPrize']['user_prize_status'] : '']),
                    ],
                    [
                        'label' => '发奖方式',
                        'attribute' => 'award_method',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\UserPrize::$userPrizeAwardMethod2Name[$model->award_method])
                                ? \common\models\UserPrize::$userPrizeAwardMethod2Name[$model->award_method] : '未知';
                        },
                        'filter' => false
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
                            \common\definitions\Common::$dateRange, ["class" => "form-control ", 'value' => !empty($params['UserPrize']['date_range']) ? $params['UserPrize']['date_range'] : '']),
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
                        'template' => '{lines} {rece} {wait} {cancel} {delete}',
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', \yii\helpers\Url::to(['lottery/user_prize_edit', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
                            },
//                            'detail' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('详情', \yii\helpers\Url::to(['qa/detail', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
//                            },
                            'rece' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('领取', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'user_prize_rece',
                                    'data-id' => $model->id
                                ]);
                            },
                            'cancel' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('取消', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'user_prize_cancel',
                                    'data-id' => $model->id
                                ]);
                            },
                            'wait' => function ($url, $model, $key) {
                                return \yii\helpers\Html::button('等待', [
                                    'class' => 'btn btn-xs btn-success ajax_single_btn',
                                    'request-url' => '',
                                    'request-type' => 'POST',
                                    'data-action' => 'user_prize_wait',
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


