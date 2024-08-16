<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 3:14 PM
 */

/**
 * @var \yii\web\View $this ;
 */

/**
 * @var \common\models\QA $qa
 */

\frontend\assets\Marginh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '消息';

?>
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
<!--                <div class="fs-30 bold w-100 text-FF title-box-border">-->
<!---->
<!--                </div>-->

                <?php
                if (!empty($model) && $model->ret == \common\models\StoryMatch::STORY_MATCH_RESULT_WIN) {
                ?>
                <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>
                <?php
                } else {
                    ?>
                    <img src="../../static/img/match/bc_lose.png" alt="" class="img-responsive  d-block m-auto"/>
                    <?php
                }
                ?>
            </div>
            <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn"<?= !empty($answerType) ? ' answer_type="' . $answerType . '"' : '' ?>>
                返回
            </div>
        </div>
        </div>

    </div>

</div>
