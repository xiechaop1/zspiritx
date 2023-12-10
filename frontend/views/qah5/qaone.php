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

$this->title = $qa['topic'];

?>
<audio autoplay loop>
  <source src="<?= $qa['voice'] ?>" type="audio/mpeg">
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


    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        问题
                    </div>
                    <div class="npc-name" style="right: 60px;" id="qa_return_btn">
                        X
                    </div>
                     <?= $qa['topic'] ?>
                    <div>
                     <img src=" <?= $qa['attachment'] ?>" alt="" class="img-responsive d-block"/>
                    </div>
                    <!--<div class="hpa-ctr">
                        <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        播放语音
                    </div>-->
                </div>
            </div>
            <div class="row" id="answer-box">
                <?php
                $str = $qa['selected_json'];
//                $str = str_replace("[div]", '<div>', $str);
//                $str = str_replace("[/div]", '</div>', $str);
//                    echo $qa['selected_json'];
                ?>
                <?php
                switch ($qa['qa_type']) {
                    case \common\models\Qa::QA_TYPE_MULTI:
                        $inputType = 'checkbox';
                        break;
                    case \common\models\Qa::QA_TYPE_WORD:
                        $inputType = 'text';
                        break;
                    case \common\models\Qa::QA_TYPE_SINGLE:
                    default:
                        $inputType = 'radio';
                        break;
                }
//                if ($qa['qa_type'] == \common\models\Qa::QA_TYPE_MULTI) {
//                    $inputType = 'checkbox';
//                } else {
//                    $inputType = 'radio';
//                }

                if ($inputType == 'radio') {
                    $selected = explode("\n", $str);

                    $optstr = '';
                    foreach ($selected as $sel) {
                        preg_match('/\[(\w+)\]/', $sel, $labelArr);
                        $label = count($labelArr) > 1 ? $labelArr[1] : '';
                        $txt = str_replace('[' . $label . ']', '', $sel);

                        $optstr .= '
                    <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="' . $label . '" id="legal_person_yes_' . $label . '" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' . $label . '">
                            <span class="answer-tag">' . $label . '</span>
                    ' . $txt . '
                    </label>
                    </div>
                </div>
                    ';


                    }
                } else {
                    $optstr = '<div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                    <input class="form-check-label fs-30" type=text name="answer_txt" class="form-control" placeholder="请输入答案" style="width: 80%; color: yellow;">
                   <input type="button" name="answer" value="提交" class="fs-30" style="color: yellow;">
                    </div>
                    </div>
                    ';
                }
                echo $optstr;

                ?>

<!--
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_A" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_A">
                            <span class="answer-tag">A</span>
                            8跟
                        </label>
                    </div>
                </div>
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_B" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_B">
                            <span class="answer-tag">B</span>
                            6跟
                        </label>
                    </div>
                </div>
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_C" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_C">
                            <span class="answer-tag">C</span>
                            5跟
                        </label>
                    </div>
                </div>
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_D" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_D">
                            <span class="answer-tag">D</span>
                            3跟
                        </label>
                    </div>
                </div>
-->
            </div>
            <div class="row hide" id="answer-right-box">
                <div class="m-t-30 col-sm-12 col-md-12 p-40">
                    <img src="../../static/img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>
                    <?php
                    if (!empty($qa['score'])) {
                        ?>
                        <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

                            <span class="answer-detail" style="color: yellow">
                    +<?= $qa['score'] ?>枚
                        </span>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="answer-title m-t-40">
                        <?php echo $qa['st_selected']; ?>
                    </div>
                    <div class="answer-detail m-t-40" style="line-height: 40px;">
                         <?php echo $qa['st_answer']; ?>
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
            <label id="answer-info" class="h5-btn-green-big answer-btn hide"  data-value="<?php echo $qa['st_selected']; ?>
" data-qa="<?php echo $qa['id']; ?>" data-type="<?php echo $qa['qa_type']; ?>" data-story="<?php echo $qa['story_id']; ?>" data-user="">
                提交
            </label>
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
                        <?php echo $qa['st_answer']; ?>
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
                        <?php echo $qa['st_answer']; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
