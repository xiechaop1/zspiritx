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

$this->title = '拾取';

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
                    <?= $msg ?>
                    <?php
                    if (!empty($storyModel->icon)) {
                        ?>
                    <div style="clear:both;" align="center">
                    <img src="<?= \common\helpers\Attachment::completeUrl($storyModel->icon) ?>" class="img-160" style="border-radius: 20px; ">
                    </div>
                        <?php
                    }
                    ?>
                    <?php
if (!empty($knowledge)) {
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
