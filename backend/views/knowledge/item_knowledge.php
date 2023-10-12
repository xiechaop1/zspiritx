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
    'label' => '知识管理',
];

$this->title = '知识关联列表';
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
                'afterRow' => function ($model, $key, $index) use ($itemKnowledgeModel, $stories) {
                    Modal::begin([
                        'size' => Modal::SIZE_DEFAULT,
                        'header' => '编辑',
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
                    echo $form->field($itemKnowledgeModel, 'knowledge_id')->textInput(['value' => $model->knowledge_id])->label('知识ID');
                    echo $form->field($itemKnowledgeModel, 'item_id')->textInput(['value' => $model->item_id])->label('关联数据ID');
                    echo $form->field($itemKnowledgeModel, 'item_type')->dropDownList(\common\models\ItemKnowledge::$itemType2Name, ['value' => $model->item_type])->label('关联数据类型');
                    echo $form->field($itemKnowledgeModel, 'story_id')->dropDownList($stories, ['value' => $model->story_id])->label('剧本');
                    echo $form->field($itemKnowledgeModel, 'knowledge_set_status')->textInput(['value' => $model->knowledge_set_status])->label('知识设置状态');

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
                            return !empty($model->story->title)
                                ? $model->story->title
                                : ''
                                ;
                        },
                        'filter' => false
                    ],
                    [
                        'label' => '知识',
                        'attribute' => 'knowledge_id',
                        'format'    => 'raw',
                        'value' => function ($model) {
                            return $model->knowledge->title;
                        },
                        'filter' => false
                    ],
                    [
                        'label' => '关联数据类型',
                        'format' => 'raw',
                        'filter' => Html::activeDropDownList(
                            $searchModel,
                            'item_type',
                            \common\models\ItemKnowledge::$itemType2Name, ["class" => "form-control ", 'value' => !empty($params['ItemKnowledge']['item_type']) ? $params['ItemKnowledge']['item_type'] : '']),
                        'value' => function ($model) {

                            $ret = !empty(\common\models\ItemKnowledge::$itemType2Name[$model->item_type])
                                ? \common\models\ItemKnowledge::$itemType2Name[$model->item_type]
                                : '未知';

                            return $ret;

                        }
                    ],
                    [
                        'label' => '关联数据ID',
                        'attribute' => 'item_id',
                    ],
                    [
                        'label' => '知识执行状态',
                        'attribute' => 'knowledge_set_status',
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
                                return \yii\helpers\Html::a('编辑', 'javascript:void(0);', [
                                    'class' => 'btn btn-xs btn-primary',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#case-form-' . $model->id
                                ]);
                            },
//                            'detail' => function ($url, $model, $key) {
//                                return \yii\helpers\Html::a('详情', \yii\helpers\Url::to(['knowledge/detail', 'id' => $model->id]), ['class' => 'btn btn-xs btn-primary']);
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
echo $form->field($itemKnowledgeModel, 'knowledge_id')->label('知识ID');
echo $form->field($itemKnowledgeModel, 'item_id')->label('关联ID');
echo $form->field($itemKnowledgeModel, 'item_type')->dropDownList(\common\models\ItemKnowledge::$itemType2Name)->label('关联数据类型');
echo $form->field($itemKnowledgeModel, 'story_id')->dropDownList($stories)->label('剧本');
echo $form->field($itemKnowledgeModel, 'knowledge_set_status')->label('知识设置状态');

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


