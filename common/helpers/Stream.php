<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/09/27
 * Time: 下午4:33
 */

namespace common\helpers;

use common\models\Actions;
use common\models\GptContent;
use yii;

class Stream
{

    public static $dialogTxtMaxLength = 85;

    public static $dialogTxt = '';

    public static $dialogTmpTxt = '';

    public static function streamCallbackToText($data, $params = []) {
        $dataJson = str_replace('data: ', '', $data);
        $dataArray = json_decode($dataJson, true);

        if (isset($dataArray['choices'][0]['delta']['content'])) {
            self::$dialogTxt .= $dataArray['choices'][0]['delta']['content'];
            echo $dataArray['choices'][0]['delta']['content'] . str_repeat('        ', 128);
//            echo $dataArray['choices'][0]['delta']['content'] . ' 111';
        }
//        if (!empty($dataArray['choices'][0]['finish_reason'])) {
            if (!empty($params) && $params['need_save'] == true) {
                $userId = !empty($params['userId']) ? $params['userId'] : 0;
                $toUserId = !empty($params['toUserId']) ? $params['toUserId'] : 0;
                $content = self::$dialogTxt;
                $prompt = !empty($params['prompt']) ? $params['prompt'] : '';
                $msgClass = !empty($params['msgClass']) ? $params['msgClass'] : 0;
                $senderId = !empty($params['senderId']) ? $params['senderId'] : 0;
                $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;
                $gptModel = !empty($params['gptModel']) ? $params['gptModel'] : '';
                $isFirst = !empty($params['isFirst']) ? $params['isFirst'] : false;
                $msgId = !empty($params['msgId']) ? $params['msgId'] : '';

                Yii::$app->doubao->saveContentToDb($msgId, $userId, $toUserId, $content, $prompt, $msgClass, $senderId, $storyId, $gptModel, $isFirst);
            }
//        }
//        else {
//            print_r($dataArray);
//        }
        ob_flush();
        flush();
    }

    public static function streamCallbackToDialogAction($data, $params = []) {
        file_put_contents('/tmp/streamCallbackToDialogAction.log', Date('Y-m-d H:i:s') . PHP_EOL . 'params: ' . var_export($params, true) . PHP_EOL, FILE_APPEND);
        file_put_contents('/tmp/streamCallbackToDialogAction.log', 'old: ' . $data . PHP_EOL, FILE_APPEND);
        $dataJsons = str_replace('data: ', '', $data);
        $dataJsons = str_replace('[DONE]', '', $dataJsons);

        $dataJsons = explode("\n", $dataJsons);

        if (!empty($dataJsons)) {
            foreach ($dataJsons as $dataJson) {
                if (!empty($dataJson)) {
                    $dataJson = str_replace("\n", '', $dataJson);
//        file_put_contents('/tmp/streamCallbackToDialogAction.log', $dataJson . PHP_EOL, FILE_APPEND);
                    $dataArray = json_decode($dataJson, true);

                    $userId = !empty($params['userId']) ? $params['userId'] : 0;
                    $senderId = !empty($params['senderId']) ? $params['senderId'] : 0;
                    $needVoice = !empty($params['needVoice']) ? $params['needVoice'] : false;
                    $sessionId = !empty($params['sessionId']) ? $params['sessionId'] : 0;
                    $sessionStageId = !empty($params['sessionStageId']) ? $params['sessionStageId'] : 0;
                    $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;
                    $dialogId = !empty($params['dialogId']) ? $params['dialogId'] : 0;

                    file_put_contents('/tmp/streamCallbackToDialogAction.log', 'array: ' . var_export($dataArray, true) . PHP_EOL, FILE_APPEND);
                    if (isset($dataArray['choices'][0]['delta']['content'])
                        || !empty($dataArray['choices'][0]['finish_reason'])
                    ) {
                        $aiContent = $dataArray['choices'][0]['delta']['content'];
                        $aiContent = str_replace("\n", '', $aiContent);
                        $aiContent = str_replace("\r", '', $aiContent);
                        $aiContent = str_replace("\t", '', $aiContent);
                        self::$dialogTmpTxt .= $aiContent;
                        self::$dialogTxt .= $aiContent;
                        $dialogArr = [];
//            file_put_contents('/tmp/stream.log', mb_strlen(self::$dialogTmpTxt, 'UTF8') . ' ' . self::$dialogTxtMaxLength . PHP_EOL, FILE_APPEND);
                        if (mb_strlen(self::$dialogTmpTxt, 'UTF8') >= self::$dialogTxtMaxLength
                            || !empty($dataArray['choices'][0]['finish_reason'])
                        ) {
                            $sentenceClip = mb_substr(self::$dialogTmpTxt, 0, self::$dialogTxtMaxLength, 'UTF8');
//                $sentenceClip = str_replace("\n", '', $sentenceClip);
//                $sentenceClip = str_replace("\r", '', $sentenceClip);
//                $sentenceClip = str_replace("\t", '', $sentenceClip);

                            $senderName = '小灵语';
                            if (!empty($params['senderName'])) {
                                $senderName = $params['senderName'];
                            }

                            $dialogTmp = [
                                'name' => $senderName,
                                'sentence' => $sentenceClip,
                                'to_user' => $userId,
                                'sender_id' => $senderId,
                                'viewMode' => 'rec',
                                'dialog_id' => $dialogId,
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
                            self::$dialogTmpTxt = mb_substr(self::$dialogTmpTxt, self::$dialogTxtMaxLength, null, 'UTF8');

//                if (!empty($dataArray['choices'][0]['finish_reason'])) {
                            $toUserId = $userId;
                            $content = self::$dialogTxt;
                            $prompt = !empty($params['prompt']) ? $params['prompt'] : '';
                            $msgClass = !empty($params['msgClass']) ? $params['msgClass'] : 0;
                            $gptModel = !empty($params['gptModel']) ? $params['gptModel'] : '';
                            $isFirst = !empty($params['isFirst']) ? $params['isFirst'] : GptContent::IS_FIRST_UNKNOWN;
                            $msgId = !empty($params['msgId']) ? $params['msgId'] : '';

//                    if (!empty($prompt)) {
//                        foreach ($prompt as $idx => $onePrompt) {
//                            if (!empty($onePrompt['content'][0]['image_url']['url']) && strpos(substr($onePrompt['content'][0]['image_url']['url'], 0, 50), 'base64') !== false) {
//                                unset($prompt[$idx]['content'][0]['image_url']['url']);
//                            }
//                        }
//                    }

                            Yii::$app->doubao->saveContentToDb($msgId, $userId, $toUserId, $content, $prompt, $msgClass, $senderId, $storyId, $gptModel, $isFirst);

//                }

                        }
//            echo $dataArray['choices'][0]['delta']['content'] . ' 111';
                    }
                    ob_flush();
                    flush();
                }
            }
        }

//        else {
//            print_r($dataArray);
//        }

    }
}
