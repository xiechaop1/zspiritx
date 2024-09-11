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
 * @var \common\models\Lottery $lottery
 */

\frontend\assets\Lotteryh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '抽奖';

?>
<audio autoplay loop>
  <source src="" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="story_model_id" value="<?= $storyModelId ?>">

<div class="w-100 m-auto">
<audio controls id="audio_right" class="hide">
    <source src="../../static/audio/qa_right.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>
<audio controls id="audio_wrong" class="hide">
    <source src="../../static/audio/qa_wrong.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>

    <audio controls id="audio_voice" class="hide">
        <source src="" type="audio/mpeg">
        您的浏览器不支持 audio 元素。
    </audio>
<div class="w-100 m-auto">
    <label class="close-btn">
        <img src="../../static/img/icon-close.png" class="img-40">
    </label>
    <div class="show" style="display: block;
        background-image: url(<?= !empty($storyModel['story_model_image']) ? $storyModel['story_model_image'] : '' ?>);
        background-size: 100% 100%;
        background-repeat: no-repeat;
        background-position: center center;
        height: 100vh;
        width: 100vw;
        " >
        <div style="background-color: rgba(255,255,255,0.5);
        position: absolute; right: 50px; bottom: 70px;
        font-size: 28px; width: 400px; height: 600px; border: 1px solid #333333;
        padding: 0px;
">
            <div style="color: #c6ff00; background-color: #333333; padding: 12px; font-weight: bold; text-align: center; font-size: 36px;">
                <?= $storyModel['story_model_name'] ?>
            </div>
            <div style="overflow: auto; height: 500px; padding: 12px;">
                <?php
                if (!empty($storyModel['dialog2'])) {
                    $dialog2 = json_decode($storyModel['dialog2'], true);
                    echo $dialog2['desc'];
                } ?>
            </div>
        </div>
    </div>


</div>



    </div>

</div>


