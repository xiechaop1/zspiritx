<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/09/27
 * Time: 下午4:33
 */

namespace common\helpers;

use common\models\Actions;
use yii;

class Stream
{

    public static $dialogTxtMaxLength = 85;

    public static $dialogTxt = '';

    public static function streamCallbackToText($data) {
        $dataJson = str_replace('data: ', '', $data);
        $dataArray = json_decode($dataJson, true);

        if (isset($dataArray['choices'][0]['delta']['content'])) {
            echo $dataArray['choices'][0]['delta']['content'] . str_repeat('        ', 128);
//            echo $dataArray['choices'][0]['delta']['content'] . ' 111';
        }
//        else {
//            print_r($dataArray);
//        }
        ob_flush();
        flush();
    }

    public static function streamCallbackToDialogAction($data, $params = []) {
        $dataJson = str_replace('data: ', '', $data);
        file_put_contents('/tmp/streamCallbackToDialogAction.log', $dataJson . PHP_EOL, FILE_APPEND);
        $dataArray = json_decode($dataJson, true);

        $userId = !empty($params['userId']) ? $params['userId'] : 0;
        $senderId = !empty($params['senderId']) ? $params['senderId'] : 0;
        $needVoice = !empty($params['needVoice']) ? $params['needVoice'] : false;
        $sessionId = !empty($params['sessionId']) ? $params['sessionId'] : 0;
        $sessionStageId = !empty($params['sessionStageId']) ? $params['sessionStageId'] : 0;
        $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;

        file_put_contents('/tmp/streamCallbackToDialogAction.log', var_export($dataArray, true), FILE_APPEND);
        if (isset($dataArray['choices'][0]['delta']['content'])
            || !empty($dataArray['choices'][0]['finish_reason'])
        ) {
            $aiContent = $dataArray['choices'][0]['delta']['content'];
            $aiContent = str_replace('\n', '', $aiContent);
            self::$dialogTxt .= $aiContent;
            $dialogArr = [];
            file_put_contents('/tmp/stream.log', mb_strlen(self::$dialogTxt, 'UTF8') . ' ' . self::$dialogTxtMaxLength . PHP_EOL, FILE_APPEND);
            if (mb_strlen(self::$dialogTxt, 'UTF8') >= self::$dialogTxtMaxLength
                || !empty($dataArray['choices'][0]['finish_reason'])
            ) {
                $sentenceClip = mb_substr(self::$dialogTxt, 0, self::$dialogTxtMaxLength, 'UTF8');

                $dialogTmp = [
                    'name' => '小灵语',
                    'sentence' => $sentenceClip,
                    'to_user' => $userId,
                    'sender_id' => $senderId,
                    'viewMode' => 'rec',
                ];

                if ($needVoice === true) {
                    $ttsRet = Yii::$app->doubaoTTS->ttsWithDoubao($sentenceClip, $userId);

                    if (!empty($ttsRet['file']['saveFile'])) {
                        $ttsFile = 'https://h5.zspiritx.com.cn/' . $ttsRet['file']['file'];
                        $dialogTmp['sentenceClipURL'] = $ttsFile;
                    }
                }
                $dialogArr[] = $dialogTmp;
                file_put_contents('/tmp/stream.log', var_export($dialogArr, true), FILE_APPEND);
                Yii::$app->act->addWithoutTag($sessionId, $sessionStageId, $storyId, $userId, $dialogArr, Actions::ACTION_TYPE_DIALOG);
                self::$dialogTxt = mb_substr(self::$dialogTxt, self::$dialogTxtMaxLength, null, 'UTF8');

            }
//            echo $dataArray['choices'][0]['delta']['content'] . ' 111';
        }
//        else {
//            print_r($dataArray);
//        }
        ob_flush();
        flush();
    }
}
