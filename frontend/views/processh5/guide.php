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

$this->title = '向导';

?>
<!--<input type="hidden" name="user_id" value="--><?php //= $userId ?><!--">-->
<!--<input type="hidden" name="session_id" value="--><?php //= $sessionId ?><!--">-->
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        <?= !empty($content['title']) ? $content['title'] : '-' ?>
                    </div>
                    <?= !empty($content['content']) ? $content['content'] : '-' ?>
                    <?php
                    if (!empty($content['image'])) {
                    ?>
                    <div>
                        <img src=" <?= $content['image'] ?>" alt=""/>
                    </div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($page < $pageCount - 1) {
                        echo '<a href="?story_id=' . $storyId . '&page=' . ($page + 1) . '" class="btn-m-green m-t-30 float-right m-r-20">下一页</a>';
                    }
                    if ($page > 1) {
                        echo '<a href="?story_id=' . $storyId . '&page=' . ($page - 1) . '" class="btn-m-green m-t-30 float-right m-r-20">上一页</a>';
                    }
                    ?>
                </div>
            </div>
            <div class="btn-m-green m-t-30 float-right m-r-20" id="guide_confirm_return_btn">
                继续
            </div>
            <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn">
                返回
            </div>
        </div>
        </div>

    </div>

</div>
