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
    'label' => '场次管理',
];

$this->title = '场次列表';
echo \dmstr\widgets\Alert::widget();
?>


    <div class="box box-primary">
        <div class="box-header">
            <?= \yii\bootstrap\Html::a('添加', '/story/session_edit', [
                'class' => 'btn btn-primary pull-right',
            ]) ?>
        </div>
        <div class="box-body">
            <?php
            echo \backend\widgets\GridView::widget([
                'filterPosition' => \backend\widgets\GridView::FILTER_POS_HEADER,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'afterRow' => function ($model, $key, $index) use ($sessionModel) {
                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '查看场次玩家',
                        'options' => [
                            'id' => 'session-form-' . $model->id
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
                    foreach ($model->users as $sessionUser) {
                        echo '<p>';
                        if (!empty($sessionUser->user)) {
                            echo '玩家：' . $sessionUser->user->user_name;
                            echo ' 队伍：';
                            if (!empty($sessionUser->team)) {
                                echo $sessionUser->team->team_name;
                            } else {
                                echo '无';
                            }
                        } else {
                            echo '玩家ID：' . $sessionUser->user_id . ' 队伍ID：' . $sessionUser->team_id;
                        }
                    }
                    echo Html::hiddenInput('data-id', $model->id);
                    ActiveForm::end();
                    Modal::end();

                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '编辑场次',
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
                    echo $form->field($sessionModel, 'password_code')->textInput(['value' => $model->password_code])->label('密码');
                    echo $form->field($sessionModel, 'session_status')->dropDownList(\common\models\Session::$sessionStats2Name, ['value' => $model->session_status])->label('场次状态');
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
                        'label' => '场次',
                        'attribute' => 'session_name',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            $sessionName = !empty($model->session_name) ? $model->session_name : '未知';
                            return \yii\helpers\Html::a($sessionName, 'javascript:void(0);', [
                                'data-toggle' => 'modal',
                                'data-target' => '#case-form-' . $model->id
                            ]);
                        },
                        'filter' => Html::activeInput('text', $searchModel, 'session_name',['placeholder'=>'标题']),
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
                        'label' => '创建者',
                        'attribute' => 'user_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty($model->creator->user_name) ? $model->creator->user_name : '未知';
                        },
                        'filter' => false
                    ],

                    [
                        'label' => '密码',
                        'attribute' => 'password_code',
                    ],
                    [
                        'label' => '状态',
                        'attribute' => 'session_status',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return !empty(\common\models\Session::$sessionStats2Name[$model->session_status]) ?
                                \common\models\Session::$sessionStats2Name[$model->session_status] : '未知';
                        },
                        'filter' => false
                    ],
                    [
                        'label' => 'User Agent',
                        'attribute' => 'user_agent',
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
                        'template' => '{lines} {edit} {userslist} {delete}',
                        'buttons' => [
                            'edit' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('编辑', 'javascript:void(0);', [
                                    'class' => 'btn btn-xs btn-primary',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#case-form-' . $model->id
                                ]);
                            },
                            'userslist' => function ($url, $model, $key) {
                                return \yii\helpers\Html::a('玩家列表', 'javascript:void(0);', [
                                    'class' => 'btn btn-xs btn-primary',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#session-form-' . $model->id
                                ]);
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
echo $form->field($sessionModel, 'session_name')->label('标题');
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


