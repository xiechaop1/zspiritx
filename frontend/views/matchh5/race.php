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
    .bg-black {
        background-image: url("../../static/img/match/raceback.jpg");
        background-size: 130%;
        /*background-size: cover;*/
        background-repeat: no-repeat;
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
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="match_id" value="<?= $matchId ?>">
<input type="hidden" name="begin_ts" value="<?= time() ?>">
<input type="hidden" name="qa_type" id="qa_type" value="<?= $qa['qa_type'] ?>">
<input type="hidden" name="match_type" id="match_type" value="<?= $storyMatch->match_type ?>">
<input type="hidden" name="rtn_answer_type" id="rtn_answer_type" value="<?= $rtnAnswerType ?>">
<input type="hidden" name="init_timer" id="init_timer" value="<?= $initTimer ?>">
<input type="hidden" name="init_add_gold" id="init_add_gold" value="<?= $addGold ?>">
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
        <div class="p-20 bg-black1">
            <div class="m-t-20 m-b-60">
                <div class="match-qa-header-left2">
                    <img src="<?= $user['avatar'] ?>" class="header-m">
                    <img src="../../static/img/match/coin.png">
                    <span id="gold"><?= !empty($userScore->score) ? \common\helpers\Common::formatNumberToStr($userScore->score, true, 0, 0) : 0 ?></span>
                </div>
                <div class="match-qa-header-right">
                    本场选手
                    <span class="text-1" id="players_ct">0</span> / <span class="text-2"><?= $storyMatch->max_players_ct ?></span>
                </div>
            </div>
            <div class="match-qa-box m-t-50">
                <div class="match-clock">
                    <img src="../../static/img/match/clock.png">
                    <span class="text-1" id="timer"><?= $initTimer ?></span>秒
                </div>
                <!--文本问题-->
                <div class="match-qa-content-text" style="font-size: 36px; line-height: 125%;" id="topic">
                    <!--                    ︎开并百花丛，独立疏篱趣未穷。-->
                </div>
                <!--图片问题-->
                <div class="match-qa-content-img" style="display: none;" id="image">
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

                <div class="match-clock-bottom">
                    答题
                    <span class="text-1" id="subjct"><?= !empty($myPlayer['prop']['subj_ct']) ? $myPlayer['prop']['subj_ct'] : 0 ?></span>/
                    <span class="text-2" id="subjtotal"><?= $ct ?></span>
                </div>
            </div>
            <div class="m-t-20">
                <?php
                if (!empty($rivalPlayers)) {
                    foreach ($rivalPlayers as $rivalPlayer) {
                        $prop = !empty($rivalPlayer['m_user_model_prop']) ? json_decode($rivalPlayer['m_user_model_prop'], true) : [];
                        $subjCt = !empty($prop['subj_ct']) ? $prop['subj_ct'] : 1;
                        $subjCtRate = intval(($subjCt)/$ct * 100);
//                        $rivalHp = !empty($prop['prop']['max_hp']) ? $prop['prop']['max_hp'] : 100;
                        ?>
                        <div class="match-qa-header-right-choice-1">
                            <img src="<?= \common\helpers\Attachment::completeUrl($rivalPlayer['user']['avatar'], true) ?>" class="header-choice-1">
                            <div class="progress-title" style="font-size: 26px;">
                                <span class="text-1 text-FF"><?= $rivalPlayer['user']['user_name'] ?></span>&nbsp; (<span class="riv_subjct" id="riv_subjct_<?= $rivalPlayer['id'] ?>"><?= $subjCt ?></span> / <span class="riv_subjmax" id="riv_subjmax_<?= $rivalPlayer['id'] ?>"><?= $ct ?></span>)

                            </div>
                            <div class="progress w-100" style="margin-top: 15px; margin-bottom: 5px;">
                                <div class="progress-bar riv_hp" id="riv_hp_<?= $rivalPlayer['id'] ?>" player_id="<?= $rivalPlayer['id'] ?>" role="progressbar" aria-valuenow="<?= $subjCtRate ?>"
                                     aria-valuemin="0" aria-valuemax="100" style="width: <?= $subjCtRate ?>%;">
                                    <span class="sr-only">40% 完成</span>
                                </div>
                            </div>
<!--                            <div class="progress w-100" style="margin-bottom: 5px;">-->
<!--                                <div id="riv_speed_--><?php //= $rivalPlayer['id'] ?><!--" class="progress-bar" role="progressbar" aria-valuenow="0"-->
<!--                                     aria-valuemin="0" aria-valuemax="100" style="background-color: cornflowerblue ;width: 0%;">-->
<!--                                    <span class="sr-only">40% 完成</span>-->
<!--                                </div>-->
<!--                            </div>-->


                        </div>
                        <?php
                    }
                }
                ?>

            </div>
            <div class="m-t-100" id="answer-box" style="margin-top: 200px;">
                <!--                <div class="answer-border2 worry">-->
                <!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_1">-->
                <!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_1">-->
                <!--                        15-->
                <!--                    </label>-->
                <!--                </div>-->
                <!--                <div class="answer-border2 right">-->
                <!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_1">-->
                <!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_1">-->
                <!--                        15-->
                <!--                    </label>-->
                <!--                </div>-->
                <!--                <div class="answer-border2">-->
                <!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_1">-->
                <!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_1">-->
                <!--                        15-->
                <!--                    </label>-->
                <!--                </div>-->
                <!--                <div class="answer-border2">-->
                <!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_1">-->
                <!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_1">-->
                <!--                        15-->
                <!--                    </label>-->
                <!--                </div>-->
            </div>

        </div>
    </div>

    <!--原QA样式-->
    <div class="p-20 bg-black hide">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <!--                    <div class="npc-name">-->
                    <!--                        问题-->
                    <!--                    </div>-->
                    <div class="npc-name" style="right: 60px;" id="qa_return_btn">
                        X
                    </div>
                    <div class="row" style="font-size: 32px; color:#FFB94F; width: 100%; text-align: right;">
                        <div style="float: left; width: 30%;">💰 <span id="gold1">0</span></div>
                        <div style="float: left; width: 30%;">📝️ <span id="subjct1">0</span> / <span id="subjtotal1"><?= $ct ?></span></div>
                        <div style="float: left; width: 30%;">🕒 <span id="timer1"><?= $initTimer?></span>秒</div>
                    </div>

                    <div id="number-floater" style="position: absolute; color: #FFB94F; font-size: 48px; top: 36px; left: 180px; text-align: center; z-index: 9999999"></div>
                    <input type="hidden" id="subj_idx" value="0">
                    <div id="topic" style="font-size: 60px; text-align: center;"></div>

                </div>
            </div>
            <div class="row">


                <label id="answer-info" class="h5-btn-green-big answer-btn hide"  data-value="<?php echo $qa['st_selected']; ?>
" data-qa="<?php echo $qa['id']; ?>" data-match="<?php echo $matchId ?>" data-type="<?php echo $qa['qa_type']; ?>" data-story="<?php echo $storyId; ?>" data-user="">
                    提交
                </label>
            </div>
            <div class="row" id="answer-box2">
                <?php
                $str = $qa['selected_json'];
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
            <div class="fs-30 bold text-FF" style="background: rgba(125, 225, 225, 0.3); position: absolute; margin: 0px; padding: 20px; bottom: 20px; width: 85%;">
                <?php
                if (!empty($matchPlayers) && 1 != 1) {
                    foreach ($matchPlayers as $matchPlayer) {
                        ?>
                        <div style="float: left; padding: 10px; width: 80px; font-size: 28px; color: #e0c46c; height: 80px;">

                            <div class="riv_avatar" id="riv_avatar" user_id="<?= !empty($matchPlayer['user']['id']) ? $matchPlayer['user']['id'] : 0 ?>" data-id="<?= $matchPlayer['id'] ?>">
                                <img width="60" src="<?= $matchPlayer['user']['avatar'] ?>"><br>
                                <!--                    &nbsp; --><?php //= !empty($matchPlayers['user']['user_name']) ? $matchPlayers['user']['user_name'] : 'AI-' . rand(1000,9999) ?>
                            </div>
                        </div>

                        <?php
                    }
                }
                ?>
                在场选手：<span id="players_ct">0</span> / <span><?= $storyMatch->max_players_ct ?></span>
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
                        <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

                            <span class="answer-detail" id="gold_score1" style="color: yellow">

                        </span>
                        </div>
                        <br>
                        <!--                        <span  class=" d-inline-block vertical-mid">很遗憾，挑战失败！</span>-->
                        <div class="btn-m-green m-t-30  m-l-30 confirm_btn">
                            返回
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

<div class="modal fade" id="extend-info" tabindex="-1" style="display: none;" aria-hidden="true">
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
                        详情
                    </div>

                    <!--                    <div id="extend-content" class="fs-32 text-FF bold m-t-50 lottery-content" style="height: 600px; overflow: auto;">-->
                    <!--                    </div>-->
                    <div id="extend-content-container" class="fs-32 text-FF bold m-t-50 lottery-content" style="height: 600px; overflow: auto;">
                        <div id="extend-content"></div>
                        <div style="float: left; line-height: 200%;">
                            <img class="play_voice_extend" src="../../static/img/match/play.png" width="50">
                        </div>
                    </div>
                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">
                        <label class="btn-green-m active ">知道了</label>
                    </div>
                    <!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
                    <!--                        <label class="btn-green-m active ">知道了</label>-->
                    <!--                    </div>-->
                    <!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
                    <!--                        <label class="btn-green-m active ">知道了</label>-->
                    <!--                    </div>-->
                </div>
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
            <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

                <span class="answer-detail" id="gold_score1" style="color: yellow">

                        </span>
            </div>
            <br>
            <!--                        <span  class=" d-inline-block vertical-mid">很遗憾，挑战失败！</span>-->
            <div class="btn-m-green m-t-30  m-l-30 confirm_btn">
                返回
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
    var maxSubjectsCt = <?= $ct ?>;
    var myPropObj;
    var intervalObjs = new Array();
    window.onload = function () {
        var i = 0;
        //maxSubjectsCt = <?php //= $ct ?>//;
        var match_type = $('#match_type').val();

        if (match_type == 4 || match_type == 5) {
            var matchTimer = setInterval(function() {
                // $('#msg_' + i).show();
                // if ($('#msg_' + i).length > 0) {
                //     $('#msg_' + i).get(0).scrollIntoView();
                // }
                compTimer(matchTimer);
                // console.log(i);
                i++;
            }, 1000);
            intervalObjs.push(matchTimer);

            var matching = setInterval(function(){
                getKnockoutPlayer();
                // clearInterval(matching);
            }, 3000);
            intervalObjs.push(matching);
        }
        var raceTimer = setInterval(function() {
            getStoryMatchPlayersProp(raceTimer);
        }, 1000);
        intervalObjs.push(raceTimer);
        var storyMatchTimer = setInterval(function() {
            getStoryMatch(storyMatchTimer);
        }, 5000);
        intervalObjs.push(storyMatchTimer);
// showSubject(0, obj);
        var dataContent = <?= $subjectsJson ?>;
        var dataCon=$.toJSON(dataContent);
        obj = eval( "(" + dataCon + ")" );

        var maxSubjectsCt = <?= $ct ?>;

        var initCt = <?= !empty($myPlayer['prop']['subj_ct']) ? $myPlayer['prop']['subj_ct'] : 0 ?>;

        showSubject(initCt);
    };

    function getKnockoutPlayer() {
        var story_id=$("input[name='story_id']").val();
        var match_id=$("input[name='match_id']").val();
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_knockout_players_in_match',
            data:{
                // user_id:user_id,
                story_id:story_id,
                match_id:match_id,
                // session_id:session_id,
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
                if(obj["code"] == 200){

                    // $('#players').html('');
                    // var playerIds = obj.data.playerIds;

                    // $('.riv_avatar').each(function() {
                    //     var rivId = $(this).attr('data-id');
                    //     if (playerIds.indexOf(rivId) >= 0) {
                    //
                    //     } else {
                    //         $(this).fadeOut();
                    //     }
                    // });
                    if (obj.data.players_ct != $('#players_ct').html()) {
                        $('#players_ct').hide();
                        $('#players_ct').html(obj.data.players_ct);
                        $('#players_ct').fadeIn();
                    }
                    $('#players_ct').html(obj.data.players_ct);
                    $('#players_ct').fadeIn();
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }

    function showSubject(idx) {
        // console.log(obj.subjects[idx]);
        var tobj = obj.subjects[idx].topic;
        var topic = tobj.formula;
        var add_gold = $('#init_add_gold').val();
        console.log(topic);
        if (topic == undefined) {
            idx = 0;
            var topic = tobj.formula;
        }
        console.log(topic);
        var mobj = obj.subjects[idx];
        // if (topic.indexOf('http') >= 0) {
        //     $('#image').html('<img src="' + topic + '" alt="" class="img-responsive d-block"/>');
        //     topic = '';
        // } else {
        //     $('#image').html('');
        // }

        if (mobj.hasOwnProperty('image') && mobj.image != undefined) {
            $('#image').html('<img src="' + mobj.image + '" alt="" class=" img-w-100"/>');
            $('#image').show();
        } else {
            $('#image').html('');
            $('#image').hide();
        }
        if (mobj.hasOwnProperty('extend') && mobj.extend != undefined && mobj.extend != '') {
            $('#extend-content').html(mobj.extend);
            $('.play_voice_extend').show();
        } else {
            $('#extend-content').html('');
            $('.play_voice_extend').hide();
        }

        $('#topic').html(topic);

        $('#subj_idx').val(idx);

        var qa_type = $('#qa_type').val();
        console.log(qa_type);
        console.log(tobj);
        if (qa_type == 1) {
            var ansrange = tobj.answerRange;
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
                // optHtml += '    <span class="answer-tag">' + label + '</span>' + ansrange[j];
                // optHtml += '</label> </div></div>';
            }
            optHtml += '<input type="hidden" id="add_gold" value="' + add_gold + '">';
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
    }

    function sleep(ms) {
        setTimeout(function (){

        },ms);
    }


    function submitSubject(idx, chosenObj) {
        var tobj = obj.subjects[idx].topic;
        var answer = tobj.answer;
        var that=$("#answer-info");
        var match_type = $('#match_type').val();
        var qa_type=that.attr("data-type");
        if (qa_type == 1 || qa_type == 2 || qa_type == 3 || qa_type == 4) {
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
        if (chosen == answer || answer == "" || answer == undefined) {

            addGold();
            // addRight();
            // addWrong();
            addSubjCt();
            idx = idx + 1;
            var retCss = 'right';
            $(chosenObj).parent().addClass(retCss);
            var initTimer = $('#init_timer').val();
            $('#timer').html(initTimer);
            setTimeout(function() {

                var subAnswer = 1;
                submitAnswer(subAnswer);
                showSubject(idx);

                // if (idx >= maxSubjectsCt) {
                //
                //     // clearInterval(timer);
                //     var subAnswer = 1;
                //     submitAnswer(subAnswer);
                //     // $('#answer-right-box').modal('show');
                //
                // } else {
                //
                //     showSubject(idx);
                // }
            }, 300);

        } else {
            // 失败提交
            var retCss = 'worry';
            chosenObj.parent().addClass(retCss);
            console.log(retCss);
            console.log(chosenObj);
            setTimeout(function() {
                submitAnswer(0);
            }, 300);
        }
    }

    function getStoryMatchPlayersProp() {
        
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();
        var story_match_id = $('input[name=match_id]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_story_match_players_prop',
            data:{
                user_id:user_id,
                story_id:story_id,
                story_match_id:story_match_id,
            },
            onload: function (data) {
                // $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var ajaxObj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())


                //新消息获取成功
                if(ajaxObj["code"]==200){
                    console.log(ajaxObj);
                    var data = ajaxObj.data;
                    var propObj = data.story_match_players_prop;

                    $.each(propObj, function(uid, prop) {
                        if (prop != null && prop != undefined) {
                            var subjct = prop.subj_ct;
                            if (subjct != $('#riv_subjct_' + uid).html() && subjct != undefined) {
                                $('#riv_subjct_' + uid).html(subjct);
                                $('#riv_subjct_' + uid).css('opacity', 0).animate({
                                    opacity: 1
                                }, 1000);
                            }
                        }
                    });

                    $('#players_ct').html(data.players_ct);
                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                    console.log(ajaxObj.msg);
                }

            }
        });
    }


    function getStoryMatch() {

        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();
        var story_match_id = $('input[name=match_id]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_story_match',
            data:{
                user_id:user_id,
                story_id:story_id,
                story_match_id:story_match_id,
            },
            onload: function (data) {
                // $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var ajaxObj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())


                //新消息获取成功
                if(ajaxObj["code"]==200){
                    console.log(ajaxObj);
                    var data = ajaxObj.data;
                    var storyMatchObj = data.story_match;

                    if (storyMatchObj.story_match_status == 4) {
                        // 结束了
                        if (storyMatchObj.story_match_prop_array.winner == user_id) {
                            // Winner
                            $("#answer-box").hide();
                            $("#answer-right-box").modal('show');
                            audio_right.play();
                        } else {
                            // Loser
                            $("#answer-box").hide();
                            //         // $("#answer-error-box").removeClass('hide');
                            $("#answer-error-box").modal('show');
                        }
                        for (var i = 0; i < intervalObjs.length; i++) {
                            clearInterval(intervalObjs[i]);
                        }
                    } else {
                        return false;
                    }
                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                    console.log(ajaxObj.msg);
                }

            }
        });
    }



    function addGold() {
        var gold = $('#gold').html();
        var addGold = $('#add_gold').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/add_user_score',
            data:{
                user_id:user_id,
                story_id:story_id,
                // session_id:session_id,
                // session_stage_id:session_stage_id,
                score:addGold,
            },
            onload: function (data) {
                // $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var ajaxObj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())


                //新消息获取成功
                if(ajaxObj["code"]==200){

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                    console.log(ajaxObj.msg);
                }

            }
        });

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

        for (var i = 0; i < intervalObjs.length; i++) {
            clearInterval(intervalObjs[i]);
        }

        var that=$("#answer-info");
        var qa_id=that.attr("data-qa");
        var qa_type=that.attr("data-type");
        var story_id=$("input[name='story_id']").val();
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
        var wrong_ct=0;

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

                //     if(answer == 1){
                //         $("#answer-box").hide();
                //         $("#answer-right-box").modal('show');
                //         audio_right.play();
                //
                //         if (obj.data.score.score != undefined) {
                //             var score_text = "+" + obj.data.score.score + "枚";
                //             if (obj.data.score.addition > 0) {
                //                 score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                //             }
                //             $("#gold_score").html(score_text);
                //         }
                //         $('#rtn_answer_type').val(1);   // 成功
                //
                //         // setTimeout(function (){
                //         //     // Unity.call('WebViewOff&TrueAnswer');
                //         //     var params = {
                //         //         'WebViewOff':1,
                //         //         'AnswerType':1
                //         //     }
                //         //     var data=$.toJSON(params);
                //         //     Unity.call(data);
                //         // },2000);
                //     }
                //     else{
                //         $("#answer-box").hide();
                //         // $("#answer-error-box").removeClass('hide');
                //         if (obj.data.score.score != undefined) {
                //             var score_text = "+" + obj.data.score.score + "枚";
                //             if (obj.data.score.addition > 0) {
                //                 score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                //             }
                //             $("#gold_score1").html(score_text);
                //         }
                //         $("#answer-error-box").modal('show');
                //         $('#rtn_answer_type').val(2);   // 失败
                //         // $("#h5-worry").modal('show');
                //         audio_wrong.play();
                //         // $(".retry_btn").show();
                //         // setTimeout(function (){
                //         //     // Unity.call('WebViewOff&FalseAnswer');
                //         //     // var params = {
                //         //     //     'WebViewOff':1,
                //         //     //     'AnswerType':2
                //         //     // }
                //         //     // var data=$.toJSON(params);
                //         //     // Unity.call(data);
                //         //     location.reload();
                //         // },2000);
                //     }
                }
                // //新消息获取失败
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

            answer = 0;
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
