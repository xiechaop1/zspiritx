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
                'afterRow' => function ($model, $key, $index) use ($lotteryModel) {
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
                    echo $form->field($model, 'lottery_name')->label('标题');
                    echo $form->field($model, 'story_id')->label('剧本ID');
                    echo $form->field($model, 'lottery_type')->dropDownList(\common\models\Lottery::$lotteryType2Name)->label('抽奖类型');

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
                        'label' => '剧本',
                        'attribute' => 'story_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                if (empty($model->story)) {
                    return '暂无';
                }
                            return $model->story->title;
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'story_id'),
                    ],
                    [
                        'label' => '标题',
                        'format' => 'raw',
                        'filter'    => Html::activeInput('text', $searchModel, 'lottery_name',['placeholder'=>'标题']),
                        'value' => function ($model) {
                            return \yii\helpers\Html::a($model->lottery_name, 'javascript:void(0)', [
                                'data-toggle' => 'modal',
                                'data-target' => '#edit-form-' . $model->id
                            ]);
                        }
                    ],
                    [
                        'label' => '抽奖类型',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'lottery_type',
                            $lotteryTypes, ["class" => "form-control ", 'value' => !empty($params['Lottery']['lottery_type']) ? $params['Lottery']['lottery_type'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\Lottery::$lotteryType2Name[$model->lottery_type])
                                ? \common\models\Lottery::$lotteryType2Name[$model->lottery_type]
                                : '未知';

                            return $ret;

                        }
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
echo $form->field($lotteryModel, 'lottery_name')->label('标题');
echo $form->field($lotteryModel, 'story_id')->label('剧本ID');
echo $form->field($lotteryModel, 'lottery_type')->dropDownList(\common\models\Lottery::$lotteryType2Name)->label('抽奖类型');
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


