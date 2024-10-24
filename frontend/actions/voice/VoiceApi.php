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
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success($ret);
    }

    public function ws() {
        $file = $_FILES['fileUpload'];

        $_request = $_REQUEST;
        $sessionId = !empty($_request['session_id']) ? $_request['session_id'] : 0;
        $storyId = !empty($_request['story_id']) ? $_request['story_id'] : 0;
        $userId = !empty($_request['user_id']) ? $_request['user_id'] : 0;
        $sessionStageId = !empty($_request['session_stage_id']) ? $_request['session_stage_id'] : 0;

        try {
            $time1 = time();
            $word = Yii::$app->xunfei->sendByFile($file['tmp_name']);
//            $word = Yii::$app->xunfei->sendRealByFile($file['tmp_name']);
            var_dump($word);

            $lastContents = Yii::$app->doubao->getContentsFromDb($userId, $userId, strtotime('-5 minute'), 0, 1);

            $oldContents = [];
            if (!empty($lastContents)) {
                foreach ($lastContents as $lastContent) {
                    if (!empty($lastContent['prompt'])) {
                        $oldPrompts = json_decode($lastContent['prompt'], true);
                        if (!empty($oldPrompts)) {
                            foreach ($oldPrompts as $oldPrompt) {
                                if (!empty($oldPrompt['role']) && $oldPrompt['role'] != 'system') {
                                    $oldContents[] = $oldPrompt;
                                }
                            }
                        }
                        if (!empty($lastContents['content'])) {
                            if (Common::isJson($lastContents['content'])) {
                                $contentObj = json_decode($lastContents['content'], true);
                                $content = !empty($contentObj['content']) ? $contentObj['content'] : '';
                            } else {
                                $content = $lastContents['content'];
                            }
                            $oldContents[] = [
                                'role'  => 'assistant',
                                'content' => $content,
                            ];

                        }
                    }
                }
            }

            var_dump($oldContents);

            $params = [
                'userId' => $userId,
                'toUserId' => $userId,
                'storyId' => $storyId,
            ];

            $aiRet = Yii::$app->doubao->talk($word, $oldContents, $params);
            $time2 = time();
            var_dump($time2 - $time1);
            $aiContent = '';
            if (!empty($aiRet['content'])) {
                $aiContent = $aiRet['content'];
                var_dump($aiRet['content']);
            } else {
                $aiContent = $aiRet;
                var_dump($aiRet);

            }

            $strlength = 260;
            $dialogCt = intval(mb_strlen($aiContent, 'utf-8') / $strlength) + 1;

            $dialogArr = [];
            for ($i = 0; $i < $dialogCt; $i++) {
                $dialogArr[] = [
                    'name' => '小灵语',
                    'sentence' => mb_substr($aiContent, $i * $strlength, $strlength, 'utf-8'),
                    'to_user' => $userId,
                    'sender_id' => 0,
                ];
            }
            Yii::$app->act->addWithoutTag($sessionId, $sessionStageId, $storyId, $userId, $dialogArr, Actions::ACTION_TYPE_DIALOG);
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