<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


use common\models\StoryModels;
use common\models\UserModels;

class Qa
{
    public static function formatQa($qa)
    {
        $qa['attachment'] = Attachment::completeUrl($qa['attachment'], true);
    }

    public static function formatSubjectFromGPT($gpt) {
//        $ret = [];

//        if (!empty($gptResponse)) {
//            foreach ($gptResponse as $gpt) {
                $answerIdx = $gpt['ANSWER'];
                if (in_array($answerIdx, ['A', 'B', 'C', 'D'])) {
                    $answer = !empty($gpt['OPTIONS'][$answerIdx]) ? $gpt['OPTIONS'][$answerIdx] : $answer;
                } else {
                    $answer = $answerIdx;
                }

                $options = $gpt['OPTIONS'];
//                unset($options[$answerIdx]);
                $opts = [];
                foreach ($options as $opt) {
                    $opts[] = $opt;
                }

                $ret = [
                    'formula' => $gpt['SUBJECT'],
                    'topic' => $gpt['SUBJECT'],
                    'standFormula' => $gpt['SUBJECT'],
                    'answerRange' => $opts,
                    'selected_json' => $opts,
                    'selected' => json_encode($opts, JSON_UNESCAPED_UNICODE),
                    'answer' => $answer,
                    'st_answer' => $answer,
                ];

//            }
//        }

        return $ret;
    }

    public static function generateChallengePropByLevel($level, $prop) {
        $hitRange = [
            5 * (1 + ($level - 1) / 5),
            10 * (1 + ($level - 1) / 5),
        ];
        $gold = 10 * (1 + ($level - 1) / 2);

        $prop['level'] = $level;
        $prop['hitRange'] = $hitRange;
        $prop['gold'] = $gold;

        return $prop;
    }

    public static function formatSelect($qa) {
        $str = $qa['selected_json'];
        $str = str_replace("[div]", '<div>', $str);
        $str = str_replace("[/div]", '</div>', $str);
        $answers = ['A', 'B', 'C', 'D'];
        foreach ($answers as $an) {
            $optstr = '<div class="form-check form-check-inline m-t-5">';
            $optstr .= '<input class="form-check-input"  type=radio name="answer" value="' . $an . '" id="answer-' . $an . '">';
            $labelstr = '<label class="form-check-label fs-30 text-66" for="answer-' . $an . '">';
            //. $an .'</label></div>';
            $findstr = '[opt ' . $an . ']';
            $str = str_replace($findstr, $optstr, $str);

            $findstr = '[label ' . $an . ']';
            $str = str_replace($findstr, $labelstr, $str);
        }
        $str = str_replace('[/label]', '</label>', $str);
        $str = str_replace('[/opt]', '</div>', $str);
        return $str;
    }

    public static function setKeyboard($keyboardConfig, $storyModels = [], $keyClass = 'v_div_keyboard') {
        $optstr = '';
        if (empty($keyboardConfig['keyboard'])) {
            return $optstr;
        }
        if ($keyboardConfig['keyboard'] == 'bagitems') {
            $optstr .= '<div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
            if (!empty($keyboardConfig['inc_story_model_ids'])) {
                $incIds = [];
                $keyStoryModelsConf = [];
                foreach ($keyboardConfig['inc_story_model_ids'] as $incStoryModelId => $incStoryModelConf) {
                    $incIds[] = $incStoryModelId;
                    $keyStoryModelsConf[$incStoryModelId] = $incStoryModelConf;
                }
//                $incBagItems = UserModels::find()
//                    ->where([
//                        'story_model_id' => $incIds,
//                        'user_id' => $userId,
//                        'session_id' => $sessionId,
//                        'is_delete' => \common\definitions\Common::STATUS_NORMAL,
//                    ]);
//                if (!empty($storyId)) {
//                    $incBagItems = $incBagItems->andFilterWhere(['story_id' => $storyId]);
//                }
//                $incBagItems = $incBagItems->all();

//                if (!empty($incBagItems)) {
//                    foreach ($incBagItems as $ibi) {
//                        if (!in_array($ibi->storyModel->id, $incIds)) {
//                            continue;
//                        }
//                        if (!empty($ibi->storyModel->story_model_image)) {
//                            $showItem = '<img src="' . Attachment::completeUrl($ibi->storyModel->story_model_image, true) . '" class="img-fluid">';
//                        } else {
//                            $showItem = $ibi->storyModel->story_model_name ;
//                        }
//                        $val = !empty($keyStoryModelsConf[$ibi->story_model_id]['val']) ? $keyStoryModelsConf[$ibi->story_model_id]['val'] : '';
//                        $optstr .= '<div name="keyboard" class="v_div_keyboard" id="keyboard-' . $ibi->story_model_id . '"'
////                        . ' val="' . !empty($keyStoryModelsConf[$ibi->story_model_id]['val']) ? $keyStoryModelsConf[$ibi->story_model_id]['val'] : '' . '"'
//                            . ' val="' . $val . '"'
//                        . '>' . $showItem . '</div>';
////                        $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
//                    }
//                }
                if (!empty($storyModels)) {
                    foreach ($storyModels as $storyModel) {
                        if (!in_array($storyModel->id, $incIds)) {
                            continue;
                        }
                        if (!empty($storyModel->icon)) {
                            $showItem = '<img src="' . Attachment::completeUrl($storyModel->icon, true) . '" class="puzzle_image_img">';
                        } else {
                            $showItem = $storyModel->story_model_name ;
                        }

                        $val = isset($keyStoryModelsConf[$storyModel->id]['val']) ? $keyStoryModelsConf[$storyModel->id]['val'] : '';
                        $rightVal = isset($keyStoryModelsConf[$storyModel->id]['right_val']) ? $keyStoryModelsConf[$storyModel->id]['right_val'] : '';
                        $optstr .= '<div name="keyboard" class="' . $keyClass . '" id="keyboard-' . $storyModel->id . '"'
//                        . ' val="' . !empty($keyStoryModelsConf[$story_model_id]['val']) ? $keyStoryModelsConf[$story_model_id]['val'] : '' . '"'
                            . ' val="' . $val . '"'
                            . ' right_val="' . $rightVal . '"'
                            . '>' . $showItem . '</div>';
//                        $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
                    }
                }
            }
            $optstr .= '</div>';
        } elseif ($keyboardConfig['keyboard'] == '9area') {
            $optstr .= '<div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
            $keyboard = $keyboardConfig['keyboard'];
            $keyboardArray = [];

            $labels = [
                0 => '+',
                1 => ' ',
                2 => 'ABC',
                3 => 'DEF',
                4 => 'GHI',
                5 => 'JKL',
                6 => 'MNO',
                7 => 'PQRS',
                8 => 'TUV',
                9 => 'WXYZ',
                '*' => '',
                '#' => '',
            ];

            $vals = [
                1,2,3,4,5,6,7,8,9,'*', 0, '#'
            ];

            for ($i = 0; $i < sizeof($vals); $i++) {
                $val = $vals[$i];
                $keyboardArray[$val] = '<div class="keyboard_label_big">' . $val . '</div><div class="keyboard_label_small">' . $labels[$val] . '</div>';
            }
            $keyboardAddationArray[2]['DELETE'] = '<div class="keyboard_label_delete">←</div>';

            $i = 0;
            foreach ($keyboardArray as $key => $val) {
                $optstr .= '<div name="keyboard" class="v_div_keyboard" id="keyboard-' . $key . '" val="' . $key . '">' . $val . '</div>';
                if (($i + 1) % 3 == 0) {
                    if (!empty($keyboardAddationArray[$i])) {
                        foreach ($keyboardAddationArray[$i] as $key => $val) {
                            $optstr .= '<div name="keyboard" class="v_div_keyboard" id="keyboard-' . $key . '" val="' . $key . '">' . $val . '</div>';
                        }
                    }
                    $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
                }
                $i++;
            }
            $optstr .= '</div>';
        } else {
            $optstr .= '<div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
            $keyboard = $keyboardConfig['keyboard'];
//                        $keyboardArray = [];
//                        for ($i = 0; $i < mb_strlen($keyboard, 'UTF8'); $i++) {
//                            $key = mb_substr($keyboard, $i, 1, 'UTF8');
//                            $keyboardArray[$key] = $key;
//                        }
            $keyboardArrayTmp = explode('|', $keyboard);
            foreach ($keyboardArrayTmp as $keyVal) {
                $keyboardArray[$keyVal] = $keyVal;
            }
            $keyboardArray['←'] = 'DELETE';

            $i = 0;
            foreach ($keyboardArray as $key => $val) {
                $optstr .= '<input type="button" name="keyboard" class="v_keyboard ' . $val . '" id="keyboard-' . $key . '" value="' . $key . '" val="' . $val . '">';
                if (($i + 1) % 5 == 0) {
                    $optstr .= '</div><div class="m-t-30 col-sm-12 col-md-6 keyboard_area">';
                }
                $i++;
            }
            $optstr .= '</div>';
        }
        return $optstr;
    }


}