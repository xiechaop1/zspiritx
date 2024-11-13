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

\frontend\assets\Puzzleh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = 'Puzzle';

?>
<style>
    .puzzle_image_img {
        width: 100%;
        height: 100%;
    }

    .puzzle_word_item_active {
        background-color: #0b97c4;
    }
    .puzzle_image_item {
        position: relative;
        float: left;
        text-align: center;
        vertical-align:middle;
        width: 140px;
        height: 140px;
        border: 1px solid grey;
    }
    .choosen {
        border: 2px solid yellow;
    }

    .puzzle_item_end {
        border: 0px;
    }
    .answer-right {
        position: absolute;
        top: 150px;
        left: 35px;
        background: #333333;
    }
    .keyboard_area .keyboard {
        width: 120px;
        height: 120px;
        margin: 0 3px;
        background-color: #0b3452;
        text-align: center;
        font-size: 30px;
        font-weight: bold;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 6px;
        transition: border-color 0.3s;
    }

    .keyboard_area .v_puzzle_image_keyboard {
        float: left;
        width: 120px;
        height: 120px;
        margin: 0 3px;
        background-color: #0b3452;
        text-align: center;
        font-size: 30px;
        font-weight: bold;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 6px;
        transition: border-color 0.3s;
    }

    .keyboard_area .DELETE {
        background-color: #a83800;
        border: 2px solid #a80057;
    }

    .keyboard_area .v_puzzle_image_keyboard .choosen {
        background-color: #0c84ff;
    }

    .keyboard_area .DELETE .v_puzzle_image_keyboard .choosen  {
        background-color: #ff0000;
    }
</style>
<audio controls id="audio_right" class="hide">
    <source src="../../static/audio/qa_right.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>
<audio controls id="audio_wrong" class="hide">
    <source src="../../static/audio/qa_wrong.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>


<audio autoplay loop>
  <source src="" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="session_stage_id" value="<?= $sessionStageId ?>">
<input type="hidden" name="qa_id" id="qa_id" value="<?= $qaId ?>">
<input type="hidden" name="story_id" id="story_id" value="<?= $storyId ?>">
    <input type="hidden" name="rtn_answer_type" id="rtn_answer_type" value="<?= $rtnAnswerType ?>">
<input type="hidden" name="begin_ts" value="<?= time() ?>">

<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
<!--            <div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">-->
<!--                返回-->
<!--            </div>-->
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        问题
                    </div>
                    <div class="npc-name" style="right: 60px;" id="qa_return_btn">
                        X
                    </div>
                    <?= $qa['topic'] ?>
                    <input type="hidden" name="st_answer" id="st_answer" value="<?= $qa['st_selected'] ?>">
                    <div>
                        <img src=" <?= $qa['attachment'] ?>" alt="" class="img-responsive d-block"/>
                    </div>
                    <?php

                    ?>
                    <div id="puzzle_image_area">
                    <div style="clear: both;">
                    <?php
                        $ct = 0;
                        $width = intval(550 / $cols);
                        for ($i=0; $i < $rows; $i++) {

                            for ($j=0; $j < $cols; $j++) {

                                $isLock = !empty($iList[$ct]['conf']['is_lock']) ? '1' : '0';
                    ?>
                        <div class="puzzle_image_item" style="width: <?= $width ?>px; height: <?= $width ?>px;" id="puzzle_image_<?= $ct ?>" lock="<?= $isLock ?>" right_val="<?= !empty($iList[$ct]) ? $ct : '' ?>" val="<?= $ct ?>">
                            <?php
                            if (!empty($iList[$ct])) {
                                if (!empty($iList[$ct]['storyModel'])) {
                                    $storyModel = $iList[$ct]['storyModel'];

                                    $smConf = $iList[$ct]['conf'];
                                    if (!empty($smConf['is_lock']) && $smConf['is_lock'] == '1') {

                                        if (!empty($storyModel->icon)) {
                                            $showItem = '<img src="' . \common\helpers\Attachment::completeUrl($storyModel->icon, true) . '" class="puzzle_image_img">';
                                        } else {
                                            $showItem = $storyModel->story_model_name;
                                        }
                                    } else {
                                        $showItem = '';
                                    }
                                } else {
                                    $showItem = $iList[$ct]['word'];
                                }
                                echo $showItem;
                            }
                            ?>

                        </div>

                                <?php
                                    $ct++;
                            }
                            ?>
                        </div><div style="clear:both;">
                        <?php
                        }
                    ?>
                    </div>
                    </div>
                    <!--<div class="hpa-ctr">
                        <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        播放语音
                    </div>-->
                </div>
            </div>
            <div class="m-t-30 col-sm-12 col-md-6 keyboard_area">
                <?php
                echo \common\helpers\Qa::setKeyboard($keyboard, $keyStoryModels, 'v_puzzle_image_keyboard');
                ?>
            </div>
            <div class="row hide" style="clear:both;" id="answer-box">
                <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn" answer_type="1">
                    确定
                </div>
            </div>
            <div class="row hide answer-right" id="answer-right-box">
                <div class="m-t-30 col-sm-12 col-md-12 p-40">
                    <img src="../../static/img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>
                    <?php
                    if (!empty($qa['score'])) {
                        ?>
                        <div style="clear:both; text-align: center;">
                        <span>
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

                            <span class="answer-detail" id="gold_score" style="color: yellow">
                    +<?= $qa['score'] ?>枚
                        </span>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="answer-title m-t-40" style="color: yellow">
                        <?= $qa['st_selected'] ?>
                    </div>
                    <div class="answer-detail m-t-40" style="color: yellow; line-height: 40px;">
                        <?= $qa['st_answer'] ?>
                    </div>
                </div>

            </div>
            <div class="row hide" id="answer-error-box">
                <div class="m-t-60 col-sm-12 col-md-12">
                    <div class="answer-detail " >
                        <img src="../../static/img/qa/icon_错误提示@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        <span  class=" d-inline-block vertical-mid">很遗憾，答错了…</span>

                    </div>
                </div>
            </div>

                    <div class="text-center m-t-30">
<!--            <label class="btn-m-green" id="submit_btn">-->
<!--                提交-->
<!--            </label>-->
        </div>
        </div>

    </div>

</div>



<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-null" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                       请选择答案
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-right" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        恭喜您答对了
                    </div>
                    <div class="text-center m-t-30">

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-worry" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        很遗憾，打错了
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
