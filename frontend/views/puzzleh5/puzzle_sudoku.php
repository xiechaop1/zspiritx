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

$this->title = 'Puzzle';

?>
<style>
    .puzzle_word_item_active {
        background-color: #0b97c4;
    }
    .puzzle_sudoku_item {
        position: relative;
        float: left;
        text-align: center;
        vertical-align:middle;
        width: 60px;
        height: 60px;
        border: 1px solid grey;
    }
    .bor-bottom {
        border-bottom: 2px solid yellow;
    }
    .bor-left {
        border-left: 2px solid yellow;
    }
    .bor-right {
        border-right: 2px solid yellow;
    }
    .bor-top {
        border-top: 2px solid yellow;
    }
    .item_lock {
        color: yellow;
    }
    .puzzle_sudoku_item::before {
        content: "";
        display: inline-block;
        height: 100%;
        vertical-align: middle;
    }
    .puzzle_sudoku_item_end {
        border: 0px;
    }
    .answer-right {
        position: absolute;
        top: 150px;
        left: 35px;
        background: #333333;
    }
    .keyboard_area .keyboard {
        width: 45px;
        height: 45px;
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

    .keyboard_area .v_s_keyboard {
        width: 45px;
        height: 45px;
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

    .keyboard_area .v_s_keyboard_choosen {
        background-color: #0c84ff;
    }

    .keyboard_area .DELETE_v_s_keyboard_choosen  {
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

<input type="hidden" name="sudoku_current" id="sudoku_current" value="">
<input type="hidden" name="sudoku_size" id="sudoku_size" value="<?= $size ?>">

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
                    <div style="clear: both;">
                    <?php
                    for ($i=0; $i<sizeof($iList); $i++) {
                        $class = '';
                        if ($i == 0) {
                            $class .= ' bor-top';
                        }
                        if (($i+1) % 3 == 0) {
                            $class .= ' bor-bottom';
                        }
                        for ($j=0; $j<sizeof($iList[$i]); $j++) {
                            $classJ = '';
                            if ($j == 0) {
                                $classJ .= ' bor-left';
                            }
                            if (($j+1) % 3 == 0) {
                                $classJ .= ' bor-right';
                            }
                            ?>
                            <div class="puzzle_sudoku_item <?= !empty($iList[$i][$j]) ? 'item_lock' : '' ?> <?= $class ?> <?= $classJ ?>" ro="<?= !empty($iList[$i][$j]) ? '1' : '0' ?>" i="<?= $i ?>" j="<?= $j ?>" val="<?= $iList[$i][$j] ?>" id="puzzle_sudoku_<?= $i ?>_<?= $j ?>">
                            <?= !empty($iList[$i][$j]) ? $iList[$i][$j] : ' ' ?>
                            </div>
                        <?php
                        }
                        echo '</div><div style="clear:both;"';
                        echo '>';
                    }
                    ?>
                    </div>
                    <div class="m-t-30 col-sm-12 col-md-6 keyboard_area">
                    <?php
                    $i = 0;
                        foreach ($keyboardArray as $key => $val) {
//                            echo '<div class="keyboard_area_item" style="border: 1px solid yellow;">';
                            echo '<input type="button" name="keyboard" class="v_s_keyboard ' . $val . '" id="keyboard-' . $key . '" value="' . $key . '" val="' . $val . '">';
//                            echo '</div>';
                            if (($i + 1) % 10 == 0) {
                                echo '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
                            }
                            $i++;
                        }
                    ?>
                    </div>
                    <!--<div class="hpa-ctr">
                        <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        播放语音
                    </div>-->
                </div>
            </div>
            <div class="row hide" id="answer-box">
<!--                <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn" answer_type="1">-->
<!--                    确定-->
<!--                </div>-->
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
