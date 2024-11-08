<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\voice;


use common\extensions\Uploader;
use common\helpers\Common;
use common\models\Actions;
use common\models\GptContent;
use frontend\actions\ApiAction;
use yii;

class VoiceApi extends ApiAction
{
    public $action;

//    public $userId;

    private $_storyId;

    private $_story;

    private $_get;

    public $dirPrefix;

    public function run()
    {
        $this->_get = Yii::$app->request->get();


        try {
            $this->_storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

            switch ($this->action) {
                case 'input':
//                    $ret = $this->input();
                    // Todo: 临时处理，测试用
                    $ret = $this->ws();

                    break;
                case 'ws':
                    $ret = $this->ws();
                    break;
                case 'image':
                    $ret = $this->image();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success($ret);
    }

    public function image() {
        $ret = true;


        $_request = $_REQUEST;
        $sessionId = !empty($_request['session_id']) ? $_request['session_id'] : 0;
        $storyId = !empty($_request['story_id']) ? $_request['story_id'] : 0;
        $userId = !empty($_request['user_id']) ? $_request['user_id'] : 0;
        $sessionStageId = !empty($_request['session_stage_id']) ? $_request['session_stage_id'] : 0;
        $senderId = !empty($_request['sender_id']) ? $_request['sender_id'] : 0;

        $source = !empty($_request['source']) ? $_request['source'] : 0;
        $type = !empty($_request['type']) ? $_request['type'] : '';

        $dialogId = !empty($_request['dialog_id']) ? $_request['dialog_id'] : 0;

        $needVoice = false;

        $dataBase64 = !empty($_POST['data']) ? $_POST['data'] : '';


//        $word = '分析一下照片，提取关键物品，描述一下，并推测一下玩家状态，为玩家提供帮助';
        $word = '';
        $word .= '根据照片的场景，提取关键物品或者人物' . "\n"
//            . '像日常对话一样，描述一下关键物品和人物' . "\n"
            . '详细分析一下关键物品或者人物的背景、知识或者历史' . "\n"
//            . '询问一下是否需要帮助' . "\n"
            . '判断一下场景类型（户外，作业，游戏，聚会，郊游，吃饭等）' . "\n"
//            . '如果是作业场景，询问一下是否需要讲解题目' . "\n"
//            . '如果是游戏场景，询问一下是否可以一起参与' . "\n"
//            . '如果是聚会场景，询问一下是否需要给一些建议' . "\n"
//            . '如果有人物，判断一下人物情绪，并且根据情绪给出情绪价值反馈' . "\n"
//            . '如果是其他的，可以描述一下场景，并且询问一下是否需要帮助' . "\n"
            . '根据场景询问玩家是否需要更多帮助，比如：更详细介绍一下' . "\n"
            . '#格式#描述关键物品和背景知识，给出的判断和建议。【场景：场景类型】' . "\n"
//            . '并且根据可能存在的问题，给出一些建议' . "\n"
            . '总体不超过200字' . "\n"
            . '#样例#这是一篇数学作业，其中包括1+1=，2+2=，是数学的基础逻辑计算。需要帮助解析吗？【场景：作业】' . "\n"
            . '#样例#这像是在遛狗，是柯基狗，这种狗狗的腿比较短，但是很聪明，长相很可爱。需要更详细介绍一下吗？【场景：户外】' . "\n";

//        $img = base64_decode($dataBase64);
        $img = $dataBase64;
//        file_put_contents('/tmp/camshot_1.log', $img);

        $params = [
            'userId' => $userId,
            'toUserId' => $userId,
            'storyId' => $storyId,
            'senderId' => $senderId,
            'sessionId' => $sessionId,
            'sessionStageId' => $sessionStageId,
            'needVoice' => $needVoice,
            'dialogId' => $dialogId,
        ];

        // Todo:
        // 调用阿里云接口，把图片加prompt传给他
        $ret = Yii::$app->doubao->talkWithImage($word, $img, $params);

        return $ret;
    }

    public function ws() {
        $file = $_FILES['fileUpload'];

        $_request = $_REQUEST;
        $sessionId = !empty($_request['session_id']) ? $_request['session_id'] : 0;
        $storyId = !empty($_request['story_id']) ? $_request['story_id'] : 0;
        $userId = !empty($_request['user_id']) ? $_request['user_id'] : 0;
        $sessionStageId = !empty($_request['session_stage_id']) ? $_request['session_stage_id'] : 0;
        $senderId = !empty($_request['sender_id']) ? $_request['sender_id'] : 0;

        $source = !empty($_request['source']) ? $_request['source'] : 0;
        $type = !empty($_request['type']) ? $_request['type'] : '';

        $dialogId = !empty($_request['dialog_id']) ? $_request['dialog_id'] : 0;

        try {
            $needVoice = false;

            $time1 = time();
            $word = Yii::$app->xunfei->sendByFile($file['tmp_name']);

            if ($type == 'asr') {
                return [
                    'text' => $word,
                ];
            }

//            $word = Yii::$app->xunfei->sendRealByFile($file['tmp_name']);
//            var_dump($word);

            $oldContents = Yii::$app->doubao->getOldContents($userId, $userId, $senderId, GptContent::MSG_CLASS_NORMAL);

//            var_dump($oldContents);

            $params = [
                'userId' => $userId,
                'toUserId' => $userId,
                'storyId' => $storyId,
                'senderId' => $senderId,
                'sessionId' => $sessionId,
                'sessionStageId' => $sessionStageId,
                'needVoice' => $needVoice,
                'dialogId' => $dialogId,
            ];

            $aiRet = Yii::$app->doubao->talk($word, $oldContents, $params);
            return $aiRet;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function input()
    {
//        $wavData = $_POST['data'];
//
//        $tmpFile = '/tmp/' . md5(time()) . '.wav';
//        $tmpFileHanlder = fopen($tmpFile, 'w');
//        fwrite($tmpFileHanlder, $wavData);
//        fclose($tmpFileHanlder);

//        return $this->_upload($this->dirPrefix);

//        $this->_upload($this->dirPrefix);
        $this->analysisVoice();
    }

    public function analysisVoice() {

        $file = $_FILES['fileUpload'];
        try {
            $time1 = time();
            $r = Yii::$app->baiduASR->asrByFile($file['tmp_name']);
            $time2 = time();
            if (!empty($r['result'][0])) {
                var_dump($r['result'][0]);
                var_dump($time2 - $time1);
            } else {
                var_dump($r);
            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function _upload($dirPrefix){


        $config['accessKeyId'] = Yii::$app->params['oss.accesskeyid'];
        $config['accessKeySecret'] = Yii::$app->params['oss.accesskeysecret'];
        $config['endpoint'] = Yii::$app->params['oss.endpoint'];
        $config['bucket'] = Yii::$app->params['oss.bucket'];
        $config['pathFormat'] = $dirPrefix . '/{yyyy}{mm}{dd}/{time}{rand:6}';
        $config['pathRoot'] = 'v/';
        $config['maxSize'] = 1024 * 1024 * 10;
        $config['allowFiles'] = ['.wav', '.mp3', '.amr', '.m4a', '.aac', '.flac', '.ogg', '.wma', '.ape', '.aiff', '.au', '.m4r', '.m4b', '.m4p', '.m4v'];
        $config['fieldName'] = 'fileUpload';

        try {
            $up = new Uploader('fileUpload', $config);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }

        var_dump($up->getFileInfo());
        exit;
        var_dump($_REQUEST);

//        file_put_contents('/tmp/uploadreq.log', var_export($_REQUEST, true), FILE_APPEND);
//
//        if (Yii::$app->request->isPost) {
//            var_dump($_REQUEST);
//            $needDate = true;
//            $fileArray = Yii::$app->upload->upload($dirPrefix);
//            if (empty($fileArray['file_model']) || $fileArray['file_model']->hasError == 0 ) {
//                $fileTruePath = $fileArray['file_path'];
////                $uploadShowDir = 'http://file.hewa.cn/';
////                $uploadShowDir = Yii::$app->upload->uploadShowDir . $dirPrefix . '/';
//                $subFilePath = $dirPrefix . '/';
//                if ($needDate) {
////                    $uploadShowDir .= date('Ymd') . '/';
//                    $subFilePath .= date('Ymd') . '/';
//                }
//                $uploadShowDir = Yii::$app->upload->uploadShowDir . $subFilePath;
//                $fileName = Yii::getAlias($uploadShowDir . $fileArray['file_name']);
//
//                $fileArray['file_path'] = $fileName;
//
//                $data = [
//                    'file_array' => $fileArray,
//                    'file_name' => $fileName,
//                    'sub_file_name' => $subFilePath . $fileArray['file_name'],
//                    'file_true_path' => $fileTruePath,
//                ];
//
//                return $this->success($data);
//            } else {
//                return $this->fail(Yii::t('web', 'upload failed'));
//            }
//        } else {
//            return $this->controller->render('upload', [
//            ]);
//        }

    }




}