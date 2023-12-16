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

\frontend\assets\Askh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '小灵镜';

?>
<style>
    .code-input {
        display: flex;
    }

    .code-input input {
        width: 55px;
        height: 75px;
        margin: 0 10px;
        text-align: center;
        font-size: 50px;
        color: yellow;
        border: 2px solid white;
        border-radius: 14px;
        transition: border-color 0.3s;
    }

    .code-input input:focus {
        border-color: #0c84ff;
        color: yellow;
        outline: none;
        background-color: #0b3452;
    }

    .code-input input[type=button] {
        width: 100px;
        height: 75px;
        margin: 0 10px;
        position: absolute;
        right: 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .keyboard {
        width: 100px;
        height: 75px;
        margin: 0 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .DELETE {
        background-color: #a83800;
        border: 2px solid #a80057;
    }

    .keyboard_area .keyboard_click {
        background-color: #0c84ff;
    }

    .answer-border-response {
        height: 75px;
        margin: 0 10px;
        text-align: center;
        color: yellow;
        border: 2px solid white;
        border-radius: 14px;
        transition: border-color 0.3s;
        font-size: 24px;
    }

    .chat_name_o {
        color: #0c84ff;
        margin: 5px;
        padding: 15px;
    }
    .chat_content_o {
        margin: 5px;
        width: 60%;
        padding: 15px;
        background-color: #0b3452;
        border: 2px solid #0b58a2;
        border-radius: 15px;
    }

    .chat_name_m {
        position: relative;
        float: right;
        right: 5px;
        color: #0c84ff;
        margin: 5px;
        padding: 15px;
    }
    .chat_content_m {
        position: relative;
        float: right;
        right: 5px;
        margin: 5px;
        width: 80%;
        padding: 15px;
        background-color: #0b3452;
        border: 2px solid #0b58a2;
        border-radius: 15px;
        color: yellow;

    }
    .chat_div_r {
        float: right;
        width:80%;
    }
    .row {
        clear:both;
    }
    /*.chat_div {*/
    /*    padding: 15px;*/
    /*    border: 2px solid #0c84ff;*/
    /*    border-radius: 14px;*/
    /*}*/
</style>
<audio autoplay loop>
  <source src="" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="session_stage_id" value="<?= $sessionStageId ?>">
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

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border" style="min-height: 100%;">
                    <div class="npc-name">
                        小问答
                    </div>
                    <div class="npc-name" style="right: 60px;" id="qa_return_btn">
                        X
                    </div>
<!--                     我是小灵镜，你有什么问题呀？-->

            <div class="row" id="answer-box">
                        <div class="m-t-30 col-sm-12 col-md-6">
                            <div id="answer-border-response">
                                <div class="row chat_div_l">
                                <span class="chat_name_o">小灵镜</span>
                                <span class="chat_content_o">我是小灵镜，有什么可以帮您？</span>
                                </div>
<!--                                <div class="row chat_div_r">-->
<!--                                    <span class="chat_content_m">我我我我</span>-->
<!--                                    <span class="chat_name_m">我</span>-->
<!--                                </div>-->

                    </div></div>
            </div>
            <div class="row">
            <!--<label class="fs-30 ask_answer_show" style="color: yellow;" >显示</label>
            <label class="fs-30 ask_answer_hide" style="color: yellow;" >关闭</label>-->
                    <div class="m-t-30 col-sm-12 col-md-6" style="position: fixed; bottom: 10px; left: 5px;">
                    <div class="answer-border" >
                        <form id="ask_form">
                    <input class="form-check-label fs-30" type=text <?= (!empty($str['keyboard']) ? 'readonly' : '') ?> name="ask_answer_txt" class="form-control" placeholder="请输入答案" style="width: 80%; color: yellow;">
                    <label class="fs-30 ask_answer" style="color: yellow;" >提交</label>
                   <!-- <input type="button" name="ask_answer" value="提交" class="fs-30" style="color: yellow;">-->
                    <input type="hidden" name="ask_old_answer" value='<?= json_encode([[
                            'role' => 'assistant',
                            'content' => '我是小灵镜，有什么可以帮您？',
                        ]]); ?>'>
                        </form>
                    </div>
                        </div>
                    <?php
//
//                    if (!empty($str['keyboard'])) {
//                        $optstr .= '<div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                        $keyboard = $str['keyboard'];
//                        $keyboardArray = [];
//                        for ($i = 0; $i < mb_strlen($keyboard, 'UTF8'); $i++) {
//                            $key = mb_substr($keyboard, $i, 1, 'UTF8');
//                            $keyboardArray[$key] = $key;
//                        }
//                        $keyboardArray['←'] = 'DELETE';
//
//                        $i = 0;
//                        foreach ($keyboardArray as $key => $val) {
//                            $optstr .= '<input type="button" name="keyboard" class="keyboard ' . $val . '" id="keyboard-' . $key . '" value="' . $key . '" val="' . $val . '">';
//                            if (($i + 1) % 5 == 0) {
//                                $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                            }
//                            $i++;
//                        }
//                        $optstr .= '</div>';
//                    }
//
//                    $optstr .= '
//                    </div>
//                    ';


                    ?>


            </div>
                    <div>
                    </div>
                    <!--<div class="hpa-ctr">
                        <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        播放语音
                    </div>-->
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
            <label id="answer-info" class="h5-btn-green-big answer-btn hide" data-story="<?php $storyId ?>" data-user="">
                提交
            </label>
        </div>
        </div>


    </div>

</div>



<!-- 按钮：用于打开模态框 -->
    <div class="modal fade" id="h5-process" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
                <div class="p-20-40 relative h5" name="loginStr" style="width: 600px;">
                    <div>
                        <div class="fs-36 text-F6 text-center bold">
                            正在思考中……
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="h5-null" tabindex="-1"  aria-hidden="true">
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
