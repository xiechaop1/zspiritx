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

\frontend\assets\Qah5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '获取知识';

?>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        消息
                    </div>
                    <?php
                    if ($knowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL) {
                        $tagImg = 'write1.png';
                    } else {
                        if ($act == 'complete') {
                            $tagImg = 'finish.png';
                        } else {
                            $tagImg = 'play.png';
                        }
                    }
                    ?>
<!--                    <div style="position: absolute; z-index: 99999;">-->
                        <div class="m-t-30 float-right m-r-20">
                        <img src="../../static/img/tags/<?= $tagImg ?>" class="img-36  d-inline-block m-r-10 vertical-mid" />
                        </div>
<!--                    </div>-->
                    <?= $msg ?>

                    <?php

                    if ($act != 'complete') {
                    ?>
                    <hr style="color: white; border: 1px solid white;">
                    <span style="color: yellow"><?= $knowledge->title ?></span><br>
                    <?php
                    if (!empty($knowledge->image)) {
                        ?>
                        <img src="<?= \common\helpers\Attachment::completeUrl($knowledge->image) ?>" style="width: 100%;"><br>
                        <?php
                    }
                    echo $knowledge->content;
                    }

//                    if (!empty($next_knowledge)) {
//                        ?>
<!--                        <hr style="color: white; border: 1px solid white;">-->
<!--                    <span style="color: yellow">--><?php //= $next_knowledge->title ?><!--</span><br>-->
<!--                    --><?php
//                    if (!empty($next_knowledge->image)) {
//                        ?>
<!--                        <img src="--><?php //= \common\helpers\Attachment::completeUrl($next_knowledge->image) ?><!--" style="width: 100%;"><br>-->
<!--                        --><?php
//                    }
//                    echo $next_knowledge->content;
//                    }
                    ?>
                </div>
            </div>
            <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn">
                返回
            </div>
        </div>
        </div>

    </div>

</div>
