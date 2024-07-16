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

\frontend\assets\Matchh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = $storyMatch->match_name;

?>
<style>
    .answer-tag-word {
        position: relative;
        margin-left: 80px;
    }
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

    .keyboard_area .v_keyboard {
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

    .keyboard_area .v_div_keyboard {
        float: left;
        width: 120px;
        height: 120px;
        margin: 0 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .keyboard_label_big {
        clear: both;
        font-size: 40px;
        font-weight: bold;
        color: white;
        margin: 0px;
        padding: 0px;
    }

    .keyboard_area .keyboard_label_small {
        clear: both;
        font-size: 24px;
        color: white;
        margin: 0px;
        padding: 0px;
    }
    .keyboard_area .keyboard_label_delete {
        clear: both;
        font-size: 40px;
        color: red;
        margin: 0px;
        padding: 0px;
    }
</style>
<audio autoplay loop>
    <source src="<?= $qa['voice'] ?>" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="begin_ts" value="<?= time() ?>">
<input type="hidden" name="qa_type" id="qa_type" value="<?= $qa['qa_type'] ?>">
<input type="hidden" name="match_type" id="match_type" value="<?= $storyMatch->match_type ?>">
<input type="hidden" name="match_id" value="<?= $matchId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="match_class" value="<?= !empty($storyMatch->match_class) ? $storyMatch->match_class : 0 ?>">
<input type="hidden" name="rtn_answer_type" id="rtn_answer_type" value="<?= $rtnAnswerType ?>">
<input type="hidden" name="level" value="<?= $level ?>">

<input type="hidden" name="rival_speed_rate" id="rival_speed_rate" value="1">
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
        <div class="p-20 bg-black">
            <div class="m-t-20">
                <div class="match-qa-header-left3" style="width: 415px; text-align: left;">
                    <img src="<?= $user['avatar'] ?>" class="header-l">
                    <div class="progress-title">
                        <span class="text-1 text-FF"><?= $user->user_name ?></span>
                        <img src="../../static/img/match/coin.png" class="m-l-20 m-r-10">
                        <span id="gold">0</span>
                    </div>
                    <input type="hidden" class="show_max_hp" id="<?= $user->id ?>" value="<?= !empty($myProp['hp']) ? $myProp['hp'] : 300  ?>">
                    <div class="progress w-100">
                        <div id="my_hp" class="progress-bar" role="progressbar" aria-valuenow="<?= !empty($myProp['hp']) ? $myProp['hp'] : 300 ?>"
                             aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            <span class="sr-only">40% 完成</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="match-qa-box right">
                <!--文本问题-->
                <div class="match-qa-content-text" id="topic">
<!--                    ︎开并百花丛，独立疏篱趣未穷。-->
                </div>
                <!--图片问题-->
                <div class="match-qa-content-img" style="display: none;">
                    <img src="../../static/img/example.png" class="img-w-100">
                </div>
                <div class="match-qa-content-worry hide">
                    <img src="../../static/img/match/worry.png">
                    <span>17</span>
                </div>
                <div class="match-qa-content-right hide">
                    <img src="../../static/img/match/right.png">
                    <span>17</span>
                </div>
                <div class="d-block text-center m-t-50" style="margin-top: 10px;">
                    <div class="match-info" style="margin: 10px auto;" data-toggle="modal" data-target="#challenge-info">
                        <img src="../../static/img/match/Frame.png" class="img-coin">
                        提示
                    </div>
                </div>

                <div class="match-clock-bottom">
                    <div class="match-clock-bottom-left hide">
                        答题进度
                        <span class="text-1">1</span>/
                        <span class="text-2">30</span>
                    </div>
                    <div class="match-clock-bottom-right">
                        正确
                        <span class="text-1" id="right_ct">0</span>/错误
                        <span class="text-2" id="wrong_ct">0</span>
                    </div>

                </div>
            </div>

            <div class="m-t-20">
                <?php
                if (!empty($rivalPlayers)) {
                foreach ($rivalPlayers as $rivalPlayer) {
                    $prop = !empty($rivalPlayer['player']->m_user_model_prop) ? json_decode($rivalPlayer['player']->m_user_model_prop, true) : [];
                    $rivalHp = !empty($prop['prop']['max_hp']) ? $prop['prop']['max_hp'] : 100;
                ?>
                <div class="match-qa-header-right-choice-1">
                    <img src="<?= \common\helpers\Attachment::completeUrl($rivalPlayer['player']->storyModel->icon, true) ?>" class="header-choice-1">
                    <div class="progress-title">
                        <span class="text-1 text-FF"><?= $rivalPlayer['player']->storyModel->story_model_name ?></span>
                    </div>
                    <input type="hidden" class="show_speed" id="<?= $rivalPlayer['player']->id ?>" value="<?= $rivalPlayer['show_speed'] ?>">
                    <input type="hidden" class="show_attack" id="<?= $rivalPlayer['player']->id ?>" min="<?= $rivalPlayer['show_attack']['min'] ?>" max="<?= $rivalPlayer['show_attack']['max'] ?>" value="10">
                    <input type="hidden" class="show_max_hp" id="<?= $rivalPlayer['player']->id ?>" value="<?= $rivalHp ?>">
                    <div class="progress w-100" style="margin-bottom: 5px;">
                        <div class="progress-bar riv_hp" id="riv_hp_<?= $rivalPlayer['player']->id ?>" player_id="<?= $rivalPlayer['player']->id ?>" role="progressbar" aria-valuenow="<?= $rivalHp ?>"
                             aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            <span class="sr-only">40% 完成</span>
                        </div>
                    </div>
                    <div class="progress w-100" style="margin-bottom: 5px;">
                        <div id="riv_speed_<?= $rivalPlayer['player']->id ?>" class="progress-bar" role="progressbar" aria-valuenow="0"
                             aria-valuemin="0" aria-valuemax="100" style="background-color: cornflowerblue ;width: 0%;">
                            <span class="sr-only">40% 完成</span>
                        </div>
                    </div>


                </div>
                <?php
                }
                }
                ?>

            </div>

            <div class="m-t-100" style="margin-top: 150px;" id="answer-box">
<!--                <div class="answer-border2 worry">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_1">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_1">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="answer-border2 right">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_2">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_2">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="answer-border2">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_3">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_3">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="answer-border2">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_4">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_4">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
            </div>


        </div>
    </div>


    <!--原QA样式-->
    <div class="p-20 bg-black" style="display: none;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
<!--                    <div class="npc-name">-->
<!--                        问题-->
<!--                    </div>-->
                    <div class="npc-name" style="right: 60px;" id="qa_return_btn">
                        X
                    </div>
                    <?php
                    if ($storyMatch->match_type == \common\models\StoryMatch::MATCH_TYPE_CHALLENGE) {
                    ?>
                        <div class="row" style="font-size: 24px;">
                        <div style="float: left;">HP: </div>
                        <div class="my_hp1" id="my_hp1" style="float: left; height: 30px; margin-left: 20px; width:80%; border: 1px #eee solid;">
                            <div style="width: 100%; background-color: green; height: 30px;">&nbsp; </div>
                        </div>
                        </div>
                    <?php
                    } else if ($storyMatch->match_type == \common\models\StoryMatch::MATCH_TYPE_CONTEST) {
                        ?>
                        <div class="row" style="font-size: 32px; color:#FFB94F; width: 100%; text-align: right;">
                            <div style="float: left; width: 30%;">💰 <span id="gold">0</span></div>
                            <div style="float: left; width: 30%;">📝️ <span id="subjct">0</span></div>
                            <div style="float: left; width: 30%;">🕒 <span id="timer"><?= $initTimer?></span>秒</div>
                            <div style="display:none;float: left; width: 35%;">✅ <span id="right_ct">0</span>/<span id="wrong_ct">0</span></div>
                        </div>
                        <?php
                    }
                    ?>
                    <div id="number-floater" style="position: absolute; color: #FFB94F; font-size: 48px; top: 36px; left: 180px; text-align: center; z-index: 9999999"></div>
                    <input type="hidden" id="subj_idx" value="0">
                    <div id="topic" style="font-size: 60px; text-align: center;"></div>
                    <div id="image">
<!--                        <img src=" --><?php //= $qa['attachment'] ?><!--" alt="" class="img-responsive d-block"/>-->
                    </div>
                    <!--<div class="hpa-ctr">
                        <img src="../../static/img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        播放语音
                    </div>-->
                    <?php
                    if ($storyMatch->match_type == \common\models\StoryMatch::MATCH_TYPE_CHALLENGE) {
                        ?>
                    <div class="row" style="font-size: 32px; color:#FFB94F; width: 100%; bottom: 0px; margin-top: 30px; text-align: right;">
                        <div style="float: left; width: 20%;">💰 <span id="gold">0</span></div>
                        <div style="float: left; width: 20%;">📝️ <span id="subjct">0</span></div>
                        <div style="float: left; width: 30%;">✅ <span id="right_ct">0</span>/<span id="wrong_ct">0</span></div>
                        <div id="suggestion" class="fs-30" style="color: yellow; float: left; width: 20%;">❓提示
                        </div>
<!--                        <div style="float: left; width: 30%;">🕒 <span id="timer">--><?php //= $initTimer?><!--</span>秒</div>-->
                    </div>
                        <div id="suggestion_content" class="fs-24 text-FF" style="display:none; border-top: 1px solid #c0c0c0;"></div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <?php
            if (!empty($rivalPlayers)) {
                foreach ($rivalPlayers as $rivalPlayer) {
            ?>
            <div style="border-radius: 16px 16px 16px 16px;
    border: 2px solid #666;clear:both; width: 100%; font-size: 28px; color: #e0c46c; height: 120px; margin-top: 25px;">
                <input type="hidden" class="show_speed1" id="<?= $rivalPlayer['player']->id ?>" value="<?= $rivalPlayer['show_speed'] ?>">
                <input type="hidden" class="show_attack1" id="<?= $rivalPlayer['player']->id ?>" min="<?= $rivalPlayer['show_attack']['min'] ?>" max="<?= $rivalPlayer['show_attack']['max'] ?>" value="10">
                <?php
                if ($storyMatch->match_type == \common\models\StoryMatch::MATCH_TYPE_CHALLENGE) {
                ?>
                    <div class="riv">

                <div class="riv_avatar" id="riv_avatar" style="float: left; width: 200px; margin: 15px;">
                    <img src="<?= \common\helpers\Attachment::completeUrl($rivalPlayer['player']->storyModel->icon, true, 60) ?>">
                    &nbsp; <?= $rivalPlayer['player']->storyModel->story_model_name ?>
                </div>
<!--                <div style="float: left; width: 30%;">📝️ <span class="riv_subjct" id="riv_subjct_--><?php //= $rivalPlayer['player']->id ?><!--">0</span></div>-->
                <div style="float: left; width: 360px; margin: 15px;">
                    <div class="riv_hp1" id="riv_hp1_<?= $rivalPlayer['player']->id ?>" style="width:100%; border: 1px #eee solid;">
                        <div style="width: 100%; background-color: green; height: 30px;">&nbsp; </div>
                    </div>
                    <div class="riv_speed1" id="riv_speed1_<?= $rivalPlayer['player']->id ?>" style="width: 100%; background-color: cornflowerblue; height: 20px; margin-top: 20px;">
                        &nbsp; </div>
                </div>
                    </div>
                    <?php
                } else if ($storyMatch->match_type == \common\models\StoryMatch::MATCH_TYPE_CONTEST) {
                    ?>
                    <div style="float: left; width: 200px; margin: 15px;"><img src="<?= \common\helpers\Attachment::completeUrl($rivalPlayer['player']->storyModel->icon, true, 36) ?>">&nbsp; <?= $rivalPlayer['player']->storyModel->story_model_name ?></div>
                <div style="float: left; width: 30%;">⏰ <span id="timer"><?= $initTimer?></span>秒</div>
                <div style="float: left; width: 25%;">💰 <span id="gold">0</span></div>
                <div style="float: left; width: 30%;">📝️ <span class="riv_subjct" id="riv_subjct_<?= $rivalPlayer['player']->id ?>">0</span></div>
<!--                    <div style="float: left; width: 30%;">✅ <span id="right_ct">0</span>/<span id="wrong_ct">0</span></div>-->
                    <?php
                }
                    ?>
            </div>
            <?php
                }
            }
            ?>
            <div class="row">


                <label id="answer-info" class="h5-btn-green-big answer-btn hide"  data-value="<?php echo $qa['st_selected']; ?>
" data-qa="<?php echo $qa['id']; ?>" data-match="<?php echo $matchId ?>" data-type="<?php echo $qa['qa_type']; ?>" data-story="<?php echo $qa['story_id']; ?>" data-user="">
                    提交
                </label>
            </div>
            <div class="row" id="answer-box">
                <?php
                $str = !empty($qa['selected_json']) ? $qa['selected_json'] : '';
                //                $str = str_replace("[div]", '<div>', $str);
                //                $str = str_replace("[/div]", '</div>', $str);
                //                    echo $qa['selected_json'];
                ?>
                <?php
                switch ($qa['qa_type']) {
                    case \common\models\Qa::QA_TYPE_SELECTION:
                        $inputType = 'selection';
                        break;
                    case \common\models\Qa::QA_TYPE_MULTI:
                        $inputType = 'checkbox';
                        break;
                    case \common\models\Qa::QA_TYPE_WORD:
                    case \common\models\Qa::QA_TYPE_CHATGPT:
                        $inputType = 'text';
                        break;
                    case \common\models\Qa::QA_TYPE_VERIFYCODE:
                        $inputType = 'verifycode';
                        break;
                    case \common\models\Qa::QA_TYPE_SINGLE:
                    case \common\models\Qa::QA_TYPE_GPT_SUBJECT:
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
//                    foreach ($selected as $sel) {
//                        preg_match('/\[(\w+)\]/', $sel, $labelArr);
//                        $label = count($labelArr) > 1 ? $labelArr[1] : '';
//                        $txt = str_replace('[' . $label . ']', '', $sel);

//                    echo '<div id="selection_area"></div>';

//                        $optstr .= '
//                    <div class="m-t-30 col-sm-12 col-md-6">
//                    <div class="answer-border">
//                        <input class="form-check-input" type="radio" name="answer" value="' . $label . '" id="legal_person_yes_' . $label . '" >
//                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' . $label . '">
//                            <span class="answer-tag">' . $label . '</span>
//                    ' . $txt . '
//                    </label>
//                    </div>
//                </div>
//                    ';


//                    }
                } elseif ($inputType == 'text') {
                    $optstr = '';
                    if ($qa['qa_type'] == \common\models\Qa::QA_TYPE_CHATGPT) {
                        $optstr = '<div class="m-t-30 col-sm-12 col-md-6"><div id="answer-border-response" class="answer-border">
                    </div></div>';
                    }
                    $optstr .= '<div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                    <input class="form-check-label fs-30 text_input" type=text ' . (!empty($str['keyboard']) ? 'readonly' : '') . '  name="answer_txt" class="form-control" placeholder="请输入答案" style="width: 80%; color: yellow;">
                   <input type="button" name="answer" value="提交" class="fs-30" style="color: yellow;">
                    </div>
                    ';
                    $optstr .= \common\helpers\Qa::setKeyboard($str, $keyStoryModels);

//                    $optstr .= '<div class="m-t-30 col-sm-12 col-md-6">
//                    <div class="answer-border">
//                    <input class="form-check-label fs-30 text_input" type=text ' . (!empty($str['keyboard']) ? 'readonly' : '') . '  name="answer_txt" class="form-control" placeholder="请输入答案" style="width: 80%; color: yellow;">
//                   <input type="button" name="answer" value="提交" class="fs-30" style="color: yellow;">
//                    </div>
//                    ';
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
                    $optstr .= '
                    </div>
                    ';

                } elseif ($inputType == 'verifycode') {
//                    $maxLength = !empty($str['length']) ? $str['length'] : 5;
                    $maxLength = !empty($str['length']) ? $str['length'] : 5;
                    $width = intval(360 / $maxLength) . 'px';
                    $optstr = '<div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border code-input" maxlength="' . $maxLength . '">
                    ';
                    for ($i=0; $i<$maxLength; $i++) {
                        $autoFocus =  ($i==0) ? 'autofocus' : '';
                        $optstr .= '<input type="text" name="answer_txt"';
                        if (!empty($str['keyboard'])) {
                            $optstr .= ' readonly';
                        }
                        $optstr .= ' style="width: ' . $width . '" class="verifycode_input" maxlength="1" id="input-' . ($i+1) . '" ' . $autoFocus . '>';
                    }
//                    <input class="form-check-label fs-30" type="text" maxlength="1" id="input-1" autofocus>
//        <input class="form-check-label fs-30" type="text" maxlength="1" id="input-2">
//        <input class="form-check-label fs-30" type="text" maxlength="1" id="input-3">
//        <input class="form-check-label fs-30" type="text" maxlength="1" id="input-4">
//                    <input class="form-check-label fs-30" type=text name="answer_txt" class="hide form-control" placeholder="请输入答案" style="width: 80%; color: yellow;">
                    $optstr .= '<input type="button" name="answer" value="提交" class="fs-30">';
                    $optstr .= '</div>
                    </div>
                    ';
                    if (!empty($str['keyboard'])) {
                        $optstr .= \common\helpers\Qa::setKeyboard($str, $storyId, $userId, $sessionId);
//                        if ($str['keyboard'] != '9area') {
//                            $optstr .= '<div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                            $keyboard = $str['keyboard'];
////                        $keyboardArray = [];
////                        for ($i = 0; $i < mb_strlen($keyboard, 'UTF8'); $i++) {
////                            $key = mb_substr($keyboard, $i, 1, 'UTF8');
////                            $keyboardArray[$key] = $key;
////                        }
//                            $keyboardArrayTmp = explode('|', $keyboard);
//                            foreach ($keyboardArrayTmp as $keyVal) {
//                                $keyboardArray[$keyVal] = $keyVal;
//                            }
//                            $keyboardArray['←'] = 'DELETE';
//
//                            $i = 0;
//                            foreach ($keyboardArray as $key => $val) {
//                                $optstr .= '<input type="button" name="keyboard" class="v_keyboard ' . $val . '" id="keyboard-' . $key . '" value="' . $key . '" val="' . $val . '">';
//                                if (($i + 1) % 5 == 0) {
//                                    $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                                }
//                                $i++;
//                            }
//                            $optstr .= '</div>';
//                        } elseif ($str['keyboard'] == '9area') {
//                            $optstr .= '<div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                            $keyboard = $str['keyboard'];
//                            $keyboardArray = [];
//
//                            $labels = [
//                                0 => '+',
//                                1 => ' ',
//                                2 => 'ABC',
//                                3 => 'DEF',
//                                4 => 'GHI',
//                                5 => 'JKL',
//                                6 => 'MNO',
//                                7 => 'PQRS',
//                                8 => 'TUV',
//                                9 => 'WXYZ',
//                                '*' => '',
//                                '#' => '',
//                            ];
//
//                            $vals = [
//                                1,2,3,4,5,6,7,8,9,'*', 0, '#'
//                            ];
//
//                            for ($i = 0; $i < sizeof($vals); $i++) {
//                                $val = $vals[$i];
//                                $keyboardArray[$val] = '<div class="keyboard_label_big">' . $val . '</div><div class="keyboard_label_small">' . $labels[$val] . '</div>';
//                            }
//                            $keyboardAddationArray[2]['DELETE'] = '<div class="keyboard_label_delete">←</div>';
//
//                            $i = 0;
//                            foreach ($keyboardArray as $key => $val) {
//                                $optstr .= '<div name="keyboard" class="v_div_keyboard" id="keyboard-' . $key . '" val="' . $key . '">' . $val . '</div>';
//                                if (($i + 1) % 3 == 0) {
//                                    if (!empty($keyboardAddationArray[$i])) {
//                                        foreach ($keyboardAddationArray[$i] as $key => $val) {
//                                            $optstr .= '<div name="keyboard" class="v_div_keyboard" id="keyboard-' . $key . '" val="' . $key . '">' . $val . '</div>';
//                                        }
//                                    }
//                                    $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                                }
//                                $i++;
//                            }
//                            $optstr .= '</div>';
//                        }
                    }

                } elseif ($inputType == 'selection') {
                    $selected = $str;
                    $optstr = '';
                    foreach ($selected as $selection) {
                        $label = $selection['label'];
                        $val = $selection['value'];
                        $tag = !empty($selection['tag']) ? $selection['tag'] : $val;
                        $selectionType = !empty($selection['type']) ? $selection['type'] : 1;
                        $optstr .= '
                        <div class="m-t-30 col-sm-12 col-md-6">
                        <div class="answer-border">
                            <label class="form-check-label fs-30 selection-btn" style="text-align:left; padding-left: 80px;" answer_type="' . $val . '" selection_type="' . $selectionType . '" for="selection_' . $val . '">
                                <span class="answer-tag">' . $tag . '</span>
                        ' . $label . '
                        </label>
                        </div>
                    </div>
                        ';
                    }
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
            <div class="row modal" id="message-box" style="top: 150px;">
                <div class="m-t-30 col-sm-12 col-md-12 p-40">
<!--                    <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>-->
                    <div style="clear:both; margin: 10px; padding: 30px; border: 2px solid yellow; text-align: left; background-color: rgba(0,0,0, 0.5)">

                        <input type="hidden" name="message_id" id="message-id1">
                        <input type="hidden" name="message_topic" id="message-topic1">
                        <span style="float: left; width: 100px;">
                        <img src="../../static/img/match/sugg.png" style="width: 80px;" alt="" class="img-responsive  d-block m-auto"/>
                        </span>
                        <span class="fs-50 bold" style="color: #ffa227;">提&nbsp;示</span>
                        <div style="clear: both; border-top: 1px solid #ffa227; padding-top: 15px; ">
                        <span class="answer-detail" id="message-content1" style="line-height: 45px; color: yellow">

                        </span>
                        </div>
                    </div>
                    <br>
                    <!--                    <div class="answer-title m-t-40">-->
                    <!--                        恭喜您，挑战成功！-->
                    <!--                    </div>-->
                    <div class="btn-m-green m-t-30  m-l-30" data-dismiss="modal">
                        继续
                    </div>
                    <!--                    <div class="answer-detail m-t-40" style="line-height: 40px;">-->
                    <!--                        --><?php //echo ($qa['st_answer'] != 'True' && $qa['st_answer'] != $qa['st_selected']) ? $qa['st_answer'] : ''; ?>
                    <!--                    </div>-->
                </div>

            </div>

            <div class="row modal fade" id="answer-right-box1" style="top: 100px;">
                <div class="m-t-30 col-sm-12 col-md-12 p-40">
<!--                    <img src="../../static/img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>-->
                    <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>
                        <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

                            <span class="answer-detail" id="gold_score" style="color: yellow">

                        </span>
                        </div>
                    <br>
<!--                    <div class="answer-title m-t-40">-->
<!--                        恭喜您，挑战成功！-->
<!--                    </div>-->
                    <div class="btn-m-green m-t-30  m-l-30 confirm_btn">
                        继续
                    </div>

<!--                    <div class="btn-m-green m-t-30  m-l-30 msg-rtn-btn">-->
<!--                        继续-->
<!--                    </div>-->
<!--                    <div class="answer-detail m-t-40" style="line-height: 40px;">-->
<!--                        --><?php //echo ($qa['st_answer'] != 'True' && $qa['st_answer'] != $qa['st_selected']) ? $qa['st_answer'] : ''; ?>
<!--                    </div>-->
                </div>

            </div>
            <div class="row modal fade" id="answer-error-box1" style="top: 220px;">
                <div class="m-t-60 col-sm-12 col-md-12">
                    <div class="answer-detail " >
<!--                        <img src="../../static/img/qa/icon_错误提示@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
                        <img src="../../static/img/match/bc_lose.png" alt="" class="img-responsive  d-block m-auto"/>
                        <br>
<!--                        <span  class=" d-inline-block vertical-mid">很遗憾，挑战失败！</span>-->
                        <div class="btn-m-green m-t-30  m-l-30 retry_btn">
                            再试一次
                        </div>
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

<div class="row modal fade" id="answer-right-box" style="top: 100px;">
    <div class="m-t-30 col-sm-12 col-md-12 p-40">
        <!--                    <img src="../../static/img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>-->
        <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>
        <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

            <span class="answer-detail" id="gold_score" style="color: yellow">

                        </span>
        </div>
        <br>
        <!--                    <div class="answer-title m-t-40">-->
        <!--                        恭喜您，挑战成功！-->
        <!--                    </div>-->
        <div class="btn-m-green m-t-30  m-l-30 confirm_btn">
            继续
        </div>

        <!--                    <div class="btn-m-green m-t-30  m-l-30 msg-rtn-btn">-->
        <!--                        继续-->
        <!--                    </div>-->
        <!--                    <div class="answer-detail m-t-40" style="line-height: 40px;">-->
        <!--                        --><?php //echo ($qa['st_answer'] != 'True' && $qa['st_answer'] != $qa['st_selected']) ? $qa['st_answer'] : ''; ?>
        <!--                    </div>-->
    </div>

</div>
<div class="row modal fade" id="answer-error-box" style="top: 220px;">
    <div class="m-t-60 col-sm-12 col-md-12">
        <div class="answer-detail " >
            <!--                        <img src="../../static/img/qa/icon_错误提示@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
            <img src="../../static/img/match/bc_lose.png" alt="" class="img-responsive  d-block m-auto"/>
            <br>
            <!--                        <span  class=" d-inline-block vertical-mid">很遗憾，挑战失败！</span>-->
            <div class="btn-m-green m-t-30  m-l-30 retry_btn">
                再试一次
            </div>
        </div>
    </div>
</div>

<!-- 按钮：提示信息 -->
<div class="modal fade" id="challenge-info" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-lottery-bg">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 30px;right: 30px;">
                <img src="../../static/img/icon-close.png" class="img-40">
            </span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div class="m-t-50">
                    <div class="fs-36 text-F6 text-center bold hide lottery-success-title">
                        <img src="../../static/img/bg-lottery-text1.png" class="img-250">
                    </div>
                    <div class="fs-36  text-FF  text-center bold lottery-error-title">
                        提示
                    </div>

                    <input type="hidden" id="message-topic">
                    <input type="hidden" id="message-id">
                    <div id="message-content" class="fs-40 text-FF text-center bold m-t-50 lottery-content">

                    </div>
                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">
                        <label class="btn-green-m active ">知道了</label>
                    </div>
                </div>
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
                        恭喜您，挑战成功
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
                        很遗憾，挑战失败
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var obj;
    var max = 0;
    var myPropObj;
    var rivalTimerObj;
    var rivalTimerRunning;
    var topicSuggestion;
    window.onload = function () {
        var i = 0;
        var max = <?= $ct ?>;
        var match_type = $('#match_type').val();


        if (match_type == 3) {
            var matchTimer = setInterval(function() {
                // $('#msg_' + i).show();
                // if ($('#msg_' + i).length > 0) {
                //     $('#msg_' + i).get(0).scrollIntoView();
                // }
                compTimer(matchTimer);
                // console.log(i);
                i++;
            }, 1000);
        }

// showSubject(0, obj);
        var dataContent = <?= $subjectsJson ?>;
        var dataCon=$.toJSON(dataContent);
        obj = eval( "(" + dataCon + ")" );

        var myProp = <?= $myPropJson ?>;
        var myPropCon = $.toJSON(myProp);
        myPropObj = eval( "(" + myPropCon + ")" );

        max = <?= $ct ?>;

        // var rivals = $('.show_speed');
        // if (match_type == 3) {
        //     rivals.each(function () {
        //         console.log($(this).attr('id'));
        //         var rivalId = $(this).attr('id');
        //         var rivalSpeed = $(this).val();
        //         console.log(rivalSpeed);
        //
        //         rivalTimerObj = setInterval(function() {
        //             var chkTimer = $('#timer').html();
        //             var rivalSubjct = $('#riv_subjct_' + rivalId).html();
        //             rivalSubjct++;
        //             $('#riv_subjct_' + rivalId).html(rivalSubjct);
        //             if (chkTimer <= 0) {
        //                 clearInterval(rivalTimerObj);
        //             }
        //         }, rivalSpeed);
        //     });
        // } else if (match_type == 2) {
        //     rivals.each(function () {
        //         console.log($(this).attr('id'));
        //         var rivalId = $(this).attr('id');
        //         var rivalSpeed = $(this).val();
        //         var rivalHitObj = $(this).parent().find('.show_attack');
        //         // var rivalHit = rivalHitObj.val();
        //         var rivalHitMin = rivalHitObj.attr('min');
        //         var rivalHitMax = rivalHitObj.attr('max');
        //         // console.log(rivalHitObj.find('.show_attack'));
        //         console.log(rivalSpeed);
        //
        //         var rivalObj = $('#riv_speed_' + rivalId);
        //         rivalObj.css('width', '0px');
        //
        //         rivalTimerObj = setInterval(function() {
        //             var rivalWidth = rivalObj.width();
        //
        //             if (checkRivHp() == 0) {
        //                 clearRivSpeed(rivalObj);
        //                 clearInterval(rivalTimerObj);
        //             }
        //             if (rivalWidth >= 360) {
        //                 var myHp = $('#my_hp');
        //                 var rand = Math.random();
        //                 rivalHit = parseInt(rand * (rivalHitMax - rivalHitMin + 1)) + parseInt(rivalHitMin);
        //                 console.log(rivalHit);
        //                 computeHp(myHp, rivalHit);
        //                 clearRivSpeed(rivalObj);
        //
        //                 if (checkMyHp() == 0) {
        //                     $('#answer-error-box').modal('show');
        //                     clearInterval(rivalTimer);
        //                 }
        //
        //                 $('#add_gold').val('0');
        //                 $('#add_right').val('0');
        //                 $('#add_wrong').val('1');
        //
        //                 return;
        //                 // clearInterval(rivalTimer);
        //             }
        //
        //             // rivalWidth += rivalSpeed/200;
        //             var addRivalWidth = 13500/rivalSpeed;
        //             var showRivalWidth = rivalWidth + addRivalWidth;
        //             console.log(addRivalWidth);
        //             // console.log(showRivalWidth);
        //             rivalObj.css('width', showRivalWidth);
        //
        //         }, 50);
        //     });
        // }

        startRivalTimer(match_type);

        showSubject(0);

        $('.msg-rtn-btn').click(function() {
            $('#message-box').modal('hide');
            // startRivalTimer($('#match_type').val());
        });

        $('.match-info').click(function() {
            // $('#message-box').modal('show');
            // $('#message-box').modal('show');

            if ($('#message-topic').val() == $('#topic').html()) {
                return;
            }
            $('#message-content').html('正在思考……');

            setTimeout(function () {
                getSugg();
            }, 500);
             $("#message-box").modal('show');
            stopRivalTimer();

        });

    };

    
    function stopRivalTimer() {
        if (!rivalTimerRunning) {
            return;
        }
        clearInterval(rivalTimerObj);
        rivalTimerRunning = false;
    }

    function startRivalTimer(match_type) {
        // return;
        // console.log(rivalTimerRunning);
        if (rivalTimerRunning) {
            return;
        }
        var rivals = $('.show_speed');
        if (match_type == 3) {
            rivals.each(function () {
                console.log($(this).attr('id'));
                var rivalId = $(this).attr('id');
                var rivalSpeed = $(this).val();
                console.log(rivalSpeed);

                rivalTimerObj = setInterval(function() {
                    var chkTimer = $('#timer').html();
                    var rivalSubjct = $('#riv_subjct_' + rivalId).html();
                    rivalSubjct++;
                    $('#riv_subjct_' + rivalId).html(rivalSubjct);
                    if (chkTimer <= 0) {
                        clearInterval(rivalTimerObj);
                    }
                }, rivalSpeed);
            });
        } else if (match_type == 2) {
            rivals.each(function () {
                console.log($(this).attr('id'));
                var rivalId = $(this).attr('id');
                var rivalSpeed = $(this).val();
                var rivalHitObj = $(this).parent().find('.show_attack');
                // var rivalHit = rivalHitObj.val();
                var rivalHitMin = rivalHitObj.attr('min');
                var rivalHitMax = rivalHitObj.attr('max');
                // console.log(rivalHitObj.find('.show_attack'));
                console.log(rivalSpeed);

                var rivalObj = $('#riv_speed_' + rivalId);
                rivalObj.css('width', '0%');

                var time_ct = 0;
                rivalTimerObj = setInterval(function() {
                    time_ct++;
                    var rivalWidth = rivalObj.width();

                    var rivalSpeedRate = $('#rival_speed_rate').val();

                    if (checkRivHp() == 0) {
                        clearRivSpeed(rivalObj);
                        clearInterval(rivalTimerObj);
                    }
                    if (rivalWidth >= 300) {
                        console.log(time_ct);
                        time_ct = 0;
                        var myHp = $('#my_hp');
                        var rand = Math.random();
                        rivalHit = parseInt(rand * (rivalHitMax - rivalHitMin + 1)) + parseInt(rivalHitMin);
                        console.log(rivalHit);
                        computeHp(myHp, rivalHit);
                        clearRivSpeed(rivalObj);
                        // stopRivalTimer();

                        if (checkMyHp() == 0) {
                            $('#answer-error-box').modal('show');
                            clearInterval(rivalTimerObj);
                        }

                        $('#add_gold').val('0');
                        $('#add_right').val('0');
                        $('#add_wrong').val('1');

                        return;
                        // clearInterval(rivalTimer);
                    }

                    // rivalWidth += rivalSpeed/200;
                    var addRivalWidth = 135000/rivalSpeed * rivalSpeedRate;
                    var showRivalWidth = rivalWidth + addRivalWidth;
                    // console.log(showRivalWidth);
                    var showRivalWidthRate = parseInt(showRivalWidth/300*100);
                    rivalObj.css('width', showRivalWidth);
                    rivalObj.attr('aria-valuenow', showRivalWidthRate);

                }, 500);
            });
        }
        rivalTimerRunning = true;
    }

    function getSugg() {
        // $('#suggestion_content').toggle();
        var topic = $('#topic').html();
        var level = $('input[name=level]').val();
        var match_class = $('input[name=match_class]').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();


        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_suggestion_from_subject',
            data:{
                story_id:story_id,
                user_id:user_id,
                topic:topic,
                level:level,
                match_class:match_class,
            },
            onload: function (data) {
                $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())

                //新消息获取成功
                if(obj["code"]==200){
                    console.log(obj);
                    var suggestion = obj.data.suggestion;
                    $('#message-content').html(suggestion);
                    $('#message-topic').val(topic);
                    // $('#message-box').modal('show');
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }

    function showSubject(idx) {
        startRivalTimer($('#match_type').val());
        var topic = obj[idx].topic;
        var size = obj[idx].size;
        var speedrate = obj[idx].speed_rate;
        console.log(topic);
        if (topic == undefined) {
            idx = 0;
            var topic = obj[idx].topic;
        }
        if (topic == undefined) {
            return;
        }
        if (speedrate == undefined
        || speedrate == NaN
        ) {
            speedrate = 1;
        }
        $('#rival_speed_rate').val(speedrate);
        if (topic.indexOf('http') >= 0) {
            $('#image').html('<img src="' + topic + '" alt="" class="img-responsive d-block"/>');
            topic = '';
        } else {
            $('#image').html('');
        }
        $('#topic').html(topic);
        console.log(size);
        if (size != undefined) {
            $('#topic').css('font-size', size + 'px');
        }

        $('#subj_idx').val(idx);

        var qa_type = $('#qa_type').val();
        if (qa_type == 1 || qa_type == 30) {
            var ansrange = obj[idx].selected_json;
            console.log(ansrange);
            var optHtml = '';
            for (var j = 0; j < ansrange.length; j++) {
                label = String.fromCharCode(j + 65);
                optHtml += '<div class="answer-border2">';
                optHtml += '     <input class="form-check-input" type="radio" name="challenge_answer" value="' + ansrange[j] + '" id="legal_person_yes_' + label + '">';
                optHtml += '        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' + label + '">';
                optHtml += ansrange[j];
                optHtml += '        </label>';
                optHtml += '</div>';
                // label = String.fromCharCode(j + 65);
                // optHtml += '<div class="m-t-30 col-sm-6 col-md-6"><div class="answer-border">';
                // optHtml += '<input class="form-check-input" type="radio" name="answer_c" value="' + ansrange[j] + '" id="legal_person_yes_' + label + '" >';
                // optHtml += '<label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' + label + '">';
                // optHtml += '    <span class="answer-tag">' + label + '</span>' + '<span class="answer-tag-word">' + ansrange[j] + '</span>';
                // optHtml += '</label> </div></div>';
            }
            optHtml += '<input type="hidden" id="add_gold" value="10">';
            optHtml += '<input type="hidden" id="add_right" value="1">';
            optHtml += '<input type="hidden" id="add_wrong" value="0">';
        }
        // console.log(optHtml);
        $('#answer-box').html(optHtml);
        $('input[name=challenge_answer]').click(function() {
            // $('input[name=challenge_answer]').attr('disabled', true);
            submitSubject(idx, $(this));
            // $('input[name=answer_c]').attr('disabled', false);

        });
        $('#suggestion_content').fadeOut();
    }

    function sleep(ms) {
        setTimeout(function (){

        },ms);
    }


    function submitSubject(idx, chosenObj) {
        var answer = obj[idx].st_answer;
        var that=$("#answer-info");
        var match_type = $('#match_type').val();
        var qa_type=that.attr("data-type");
        if (qa_type == 1 || qa_type == 30 || qa_type == 2 || qa_type == 3 || qa_type == 4) {
            var v_select = $("input[name='challenge_answer']:checked").val();
        } else if (qa_type == 7) {
            var v_select = $("input[name='answer_txt']").val();
        } else if (qa_type == 9) {
            var v_select1 = $("input[name='answer_txt']").val();
            var v_select2 = '';
            // var v_select2 = $("#answer-border-response").html();
            var v_select = v_select2 + v_select1;
        } else if (qa_type == 8) {
            var v_selects = $("input[name='answer_txt']");
            var v_select = '';
            for (var i = 0; i < v_selects.length; i++) {
                v_select += v_selects[i].value;
            }
        }
        var chosen = v_select;
        // var chosen = $(chosenObj).val();

        console.log(chosen);
        console.log(answer);

        if (chosen == answer) {

            showRetAnimate(chosenObj, 1);
            setTimeout(function (){


                addGold();
                addRight();
                addWrong();
                addSubjCt();
                if (match_type == 2) {
                    var ret = computeRivHp();

                    if (ret == 0) {
                        // clearInterval(timer);
                        var answer = 1;
                        submitAnswer(answer);
                        // $('#answer-right-box').modal('show');

                    }
                }

                showSubject(idx+1);
                $('input[name=answer_c]').attr('disabled', false);
            },500);

        } else {
            $('#add_gold').val('0');
            $('#add_right').val('0');
            $('#add_wrong').val('1');
            showRetAnimate(chosenObj, 2);
            recordQa(obj[idx], chosen);
            setTimeout(function () {
                addRivSpeed($('.riv'), 10);
                $('input[name=answer_c]').attr('disabled', false);
            }, 1000);
        }
    }

    function recordQa(subjectObj, chosen) {
        console.log(subjectObj);
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();
        var session_id=$("input[name='session_id']").val();
        // var session_stage_id=$("input[name='session_stage_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();
        var qa_mode = 3;
        var qa_type = $('#qa_type').val();
        var match_class = $('input[name=match_class]').val();
        var st_answer = subjectObj.st_answer;
        var topic = subjectObj.topic;
        var selected = subjectObj.selected;
        var st_selected = selected;
        var score = subjectObj.gold;

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/qa/add_user_answer',
            data:{
                user_id:user_id,
                answer:chosen,
                story_id:story_id,
                session_id:session_id,
                // session_stage_id:session_stage_id,
                begin_ts:begin_ts,
                st_answer:st_answer,
                topic:topic,
                selected:selected,
                score:score,
                qa_mode:qa_mode,
                qa_type:qa_type,
                match_class:match_class,
                st_selected:st_selected,
            },
            onload: function (data) {
                $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())


                //新消息获取成功
                if(obj["code"]==200){

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                    console.log(obj.msg);
                }

            }
        });

    }

    function computeRivHp() {
        var rivals = $('.riv_hp');
        var result = 0;
        rivals.each(function() {
            // var rivHp = $(this).find('.riv_hp');
            var rivHp = $(this);
            console.log(rivHp.width());
            if (rivHp.width() <= 0) {
                return;
            }

            hitMin = myPropObj.hit.min;
            hitMax = myPropObj.hit.max;
            var hit = Math.floor(Math.random() * (hitMax - hitMin + 1) + hitMin);
            var rivId = rivHp.attr('player_id');
            computeHp(rivHp, hit);
            avatarAnimate($(this));
            clearRivSpeed($('#riv_speed_' + rivId));

            result = checkRivHp();

            return;
        });
        return result;
    }

    function clearRivSpeed(rivalObj) {
        rivalObj.css('width', '0%');
    }

    function addRivSpeed(rivalObj, speedBei = 10) {
        console.log(rivalObj);
        var rivalSpeedObj = $(rivalObj).find('.riv_speed');
        var speed = (750000 / $(rivalObj).parent().find('.show_speed').val()) ;
        var addSpeed = speed * speedBei;
        var speedRate = $('#rival_speed_rate').val();
        addSpeed = addSpeed * speedRate;
        rivalSpeedObj.css('width', rivalSpeedObj.width() + addSpeed);
    }

    function checkRivHp() {
        var rivals = $('.riv_hp');
        var result = 0;
        rivals.each(function() {
            var rivId = $(this).attr('player_id');
            var rivHp = $('#riv_hp_' + rivId);
            if (rivHp.width() <= 0) {
                return;
            }

            result = 1;
            return;
        });
        return result;
    }

    function checkMyHp() {
        var myHp = $('#my_hp');
        if (myHp.width() <= 0) {
            return 0;
        }
        return 1;
    }

    function computeHp(rivHp, hit) {
        // var hpWidth = rivHp.find('div').width();
        // var hpMaxWidth = rivHp.width();
        // // var hit=300;
        // console.log(hit);
        // hpWidth -= hit;
        // rivHp.find('div').css('width', hpWidth);
        // if (hpWidth/hpMaxWidth > 0.3 && hpWidth/hpMaxWidth <= 0.6) {
        //     rivHp.find('div').css('background-color', 'yellow');
        // } else if (hpWidth/hpMaxWidth <= 0.3) {
        //     rivHp.find('div').css('background-color', 'red');
        // }

        var hp = rivHp.attr('aria-valuenow');
        var hpMax = rivHp.parent().parent().find('.show_max_hp').val();
        var newHp = hp - hit;
        console.log(newHp);
        console.log(hpMax);
        var newHpWidth = Math.floor(newHp / hpMax * 100);
        var hpWidth = rivHp.width();
        rivHp.attr('aria-valuenow', newHp);
        rivHp.css('width', newHpWidth + '%');
        if (newHpWidth > 30 && newHpWidth <= 60) {
            rivHp.css('background-color', 'yellow');
        } else if (newHpWidth <= 30) {
            rivHp.css('background-color', 'red');
        }
        console.log(newHp);
        return;
    }

    function avatarAnimate(hitObj) {
        var rivAvatar = $(hitObj).parent().parent().find('img');
        var hitLeft = rivAvatar.position().left - 30;
        var hitTop = rivAvatar.position().top - 20;
        var hitDiv = '<div class="riv_hit" style="position: absolute; z-index: 999999; left: ' + hitLeft + 'px; top: ' + hitTop + 'px;"><img width="120" src="../../static/img/match/hit.gif"></div>';
        $(hitObj).append(hitDiv);
        $(hitObj).find('.riv_hit').animate({
            opacity: 100
        }, 500, function() {
            shake(rivAvatar);
            $(this).remove();
        });
    }

    function showRetAnimate(retObj, answer) {
        // if (answer == 1) {
        //     var imgUrl = '../../static/img/qa/icon_正确@2x.png';
        // } else {
        //     var imgUrl = '../../static/img/qa/icon_错误@2x.png';
        // }
        // var retDiv = '<div class="ret_hit" style="position: absolute; z-index: 999999; left: 0px; top: 0px;"><img width="75" src="' + imgUrl + '"></div>';
        // $(retObj).parent().append(retDiv);
        var retCss = 'right';
        console.log(answer);
        if (answer != 1) {
            retCss = 'worry';
        }
        $(retObj).parent().addClass(retCss);
        $(retObj).parent().find('.ret_hit').animate({
            opacity: 100
        }, 500, function() {
            $(this).remove();
        });
    }

    function shake(shakeObj) {
        $(shakeObj).animate({left: '+=20'}, 200) // 向右移动20px
            .animate({left: '-=20', rotate: -10 + "deg"}, 200) // 返回原位
            .animate({left: '+=10', rotate: 5 + "deg"}, 200) // 稍微右移
            .animate({left: '-=10', rotate: -5 + "deg"}, 200) // 返回原位
            .animate({left: '+=5', rotate: 10 + "deg"}, 200)  // 稍微右移
            .animate({left: '-=5', rotate: 0 + "deg"}, 200); // 返回原位
        // var shakeInterval = setInterval(function() {
        //     $(shakeObj).shake(4, 4, 20);
        // }, 2000);
        // clearInterval(shakeInterval);

    }

    function addGold() {
        var gold = $('#gold').html();
        var addGold = $('#add_gold').val();
        if (addGold > 0) {
            floNumber(addGold);
            gold = parseInt(gold) + parseInt(addGold);
            $('#gold').html(gold);
            $('#gold').css('opacity', 0).animate({
                opacity: 1
            }, 1000);
        }
    }

    function addRight() {
        var right = $('#right_ct').html();
        var addRight = $('#add_right').val();
        if (addRight > 0) {
            floNumber(addRight);
            right = parseInt(right) + parseInt(addRight);
            $('#right_ct').html(right);
            $('#right_ct').css('opacity', 0).animate({
                opacity: 1
            }, 1000);
        }
    }

    function addWrong() {
        var wrong = $('#wrong_ct').html();
        var addWrong = $('#add_wrong').val();
        if (addWrong > 0) {
            floNumber(addWrong);
            wrong = parseInt(wrong) + parseInt(addWrong);
            $('#wrong_ct').html(wrong);
            $('#wrong_ct').css('opacity', 0).animate({
                opacity: 1
            }, 1000);
        }
    }

    function submitAnswer(answer) {
        var that=$("#answer-info");
        var qa_id=that.attr("data-qa");
        var qa_type=that.attr("data-type");
        var story_id=that.attr("data-story");
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var session_stage_id=$("input[name='session_stage_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        var match_id=that.attr("data-match");

        var score=$('#gold').html();
        var subjct=$('#subjct').html();
        var right_ct=$('#right_ct').html();
        var wrong_ct=$('#wrong_ct').html();
        var max_riv_subjct = 0;
        var riv_subjct = $('.riv_subjct').each(function(){
            var riv_subjct = $(this).html();
            if (riv_subjct > max_riv_subjct) {
                max_riv_subjct = riv_subjct;
            }
        });

        // var answer;
        // console.log(subjct);
        // console.log(max_riv_subjct);
        // if (subjct > max_riv_subjct) {
        //     answer = 1;
        // } else {
        //     answer = 0;
        // }

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/update_match',
            data:{
                user_id:user_id,
                qa_id:qa_id,
                story_id:story_id,
                match_id:match_id,
                session_id:session_id,
                begin_ts:begin_ts,
                score:score,
                subjct:subjct,
                right_ct:right_ct,
                wrong_ct:wrong_ct,
                answer:answer,
                // riv_subjct:max_riv_subjct
            },
            onload: function (data) {
                $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())

                //audio 素材
                var audio_right=$("#audio_right")[0];
                var audio_wrong=$("#audio_wrong")[0];

                //新消息获取成功
                if(obj["code"]==200){

                    if(answer == 1){
                        $("#answer-box").hide();
                        $("#answer-right-box").modal('show');
                        audio_right.play();

                        if (obj.data.score.score != undefined) {
                            var score_text = "+" + obj.data.score.score + "枚";
                            if (obj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        $('#rtn_answer_type').val(1);   // 成功

                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&TrueAnswer');
                        //     var params = {
                        //         'WebViewOff':1,
                        //         'AnswerType':1
                        //     }
                        //     var data=$.toJSON(params);
                        //     Unity.call(data);
                        // },2000);
                    }
                    else{
                        $("#answer-box").hide();
                        // $("#answer-error-box").removeClass('hide');
                        $("#answer-error-box").modal('show');
                        $('#rtn_answer_type').val(2);   // 失败
                        // $("#h5-worry").modal('show');
                        audio_wrong.play();
                        // $(".retry_btn").show();
                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&FalseAnswer');
                        //     // var params = {
                        //     //     'WebViewOff':1,
                        //     //     'AnswerType':2
                        //     // }
                        //     // var data=$.toJSON(params);
                        //     // Unity.call(data);
                        //     location.reload();
                        // },2000);
                    }
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });

    };

    function addSubjCt() {
        var subjct = $('#subjct').html();
        subjct++;
        $('#subjct').html(subjct);
    }

    function compTimer(matchTimer) {
        var timer = $('#timer').html();
        timer--;
        $('#timer').html(timer);
        if (timer == 0) {
            $('#answer-box').hide();
            $('.confirm_btn').css('opacity', 0);
            $('.confirm_btn').removeClass('hide');
            $('.confirm_btn').animate({
                opacity: 1
            }, 1000);
            clearInterval(matchTimer);

            var answer;
            var subjct=$('#subjct').html();
            var max_riv_subjct = 0;
            var riv_subjct = $('.riv_subjct').each(function(){
                var riv_subjct = $(this).html();
                if (riv_subjct > max_riv_subjct) {
                    max_riv_subjct = riv_subjct;
                }
            });
            console.log(subjct);
            console.log(max_riv_subjct);
            if (parseInt(subjct) > parseInt(max_riv_subjct)) {
                answer = 1;
                console.log(answer);
            } else {
                answer = 0;
                console.log(answer);
            }
            submitAnswer(answer);
        }
    }

    function floNumber(num) {
        var duration = 5;
        var height = 0;
        $('#number-floater').html(num);
        $('#number-floater').css('opacity', 0) // 设置初始透明度为0
            .animate({
                top: '-=70',
                // 'font-size': 70,
                opacity: 1 // 渐显
            }, 200)
            .delay(duration) // 延迟随机时间
            .animate({
                top: '-=70',
                opacity: 0, // 渐隐
                'font-size': '-=15'
            }, 150, function() {
                $(this).css('top', height); // 动画完成后重置位置
                $(this).css('font-size', 40);
            });
    }

</script>
