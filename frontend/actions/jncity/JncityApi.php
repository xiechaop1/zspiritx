<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\jncity;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Knowledge;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryMatch;
use common\models\StoryStages;
use common\models\UserBook;
use common\models\UserData;
use common\models\UserEBook;
use common\models\UserEBookRes;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use frontend\actions\ApiAction;
use OSS\Core\OssException;
use OSS\OssClient;
use yii;

class JncityApi extends ApiAction
{
    public $action;

//    public $userId;

    private $_storyId;

    private $_story;

    private $_get;

    public function run()
    {
        $this->_get = Yii::$app->request->get();

        try {
            $this->_storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

            switch ($this->action) {
                case 'poi_list':
                    $ret = $this->poiList();
                    break;
                case 'story_list':
                    $ret = $this->storyList();
                    break;
                case 'change_story':
                    $ret = $this->changeStory();
                    break;
                case 'get_story':
                    $ret = $this->getStory();
                    break;
                case 'get_user_ebook':
                    $ret = $this->getUserEbook();
                    break;
                case 'get_user_one_ebook':
                    $ret = $this->getUserOneEbook();
                    break;
                case 'get_user_last_ebook':
                    $ret = $this->getUserLastEbook();
                    break;
                case 'upload':
                    $ret = $this->upload();
                    break;
                case 'generate_video':
                    $ret = $this->generateVideo();
                    break;
                case 'check_hailuo_video':
                    $ret = $this->checkHailuoVideo();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }

        return $this->success($ret);
    }

    // 获取问答列表
    public function poiList() {
//        $poiList = UserEBook::$poiList;

        $ebookStory = !empty($this->_get['ebook_story']) ? $this->_get['ebook_story'] : '';

        if (!empty($ebookStory)) {
            $poiList = Yii::$app->ebook->getStoryParams($ebookStory);
            $poiList = !empty($poiList['pois']) ? $poiList['pois'] : [];
        } else {
            $poiList = Yii::$app->ebook->getStoryParams();
        }

        return $poiList;

    }

    public function storyList() {
//        $storyList = UserEBook::$poiList;
        $storyList = Yii::$app->ebook->getStoryParams(0);

        if (empty($storyList)) {
            return [];
        }

//        foreach ($storyList as &$story) {
//            $story['poi'] = !empty(UserEBook::$poiList[$story['poi']])
//                ? UserEBook::$poiList[$story['poi']] : [];
//        }

        return $storyList;
    }

    public function changeStory() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookStory = !empty($this->_get['ebook_story']) ? $this->_get['ebook_story'] : '';

        $userEbook = UserEBook::find()
            ->where([
                'user_id' => $userId,
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->one();

        if (!empty($userEbook)) {
            if ($userEbook->ebook_status == UserEBook::USER_EBOOK_STATUS_PLAYING) {
                throw new \Exception('当前的电子书已经进行中，不能修改故事', ErrorCode::EBOOK_USER_EBOOK_STATUS_PLAYING);
            } else if ($userEbook->ebook_status == UserEBook::USER_EBOOK_STATUS_COMPLETED) {
                throw new \Exception('当前电子书已完成，请重新开始新的电子书', ErrorCode::EBOOK_USER_EBOOK_STATUS_COMPLETED);
            }

//            $ebookParam = !empty(UserEBook::$poiList[$ebookStory])
//                ? UserEBook::$poiList[$ebookStory] : [];

            $ebookParam = Yii::$app->ebook->getStoryParams($ebookStory);

            if (empty($ebookParam)) {
                return false;
            }

            $storyId = 100;

            $userEbook->ebook_story = $ebookStory;
            $userEbook->ebook_story_params = $ebookParam;
            $r = $userEbook->save();

            return $userEbook;

        } else {
            throw new \Exception('电子书尚未开启', ErrorCode::EBOOK_USER_EBOOK_STATUS_NONE);
        }

        return true;
    }

    public function getUserEbook() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

//        if (empty($userId)) {
//            throw new \Exception('用户ID不能为空', ErrorCode::EBOOK_USER_ID_EMPTY);
//        }

        $userEbook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $ret = [];

        if (!empty($userEbook)) {
            foreach ($userEbook as $ue) {
                $tmp = $ue->toArray();
                $tmp['created_at_str'] = Date('Y-m-d H::s', $ue->created_at);
                $tmp['ebook_story_params'] = json_decode($ue->ebook_story_params, true);
                $tmp['user_ebook_res'] = $ue->ebookRes;
                $tmp['mission_ct'] = !empty($ue->ebookRes) ? sizeof($ue->ebookRes) : 0;
                $tmp['ebook_status_str'] = !empty(UserEBook::$userEbookStatus2Name[$ue->ebook_status]) ? UserEBook::$userEbookStatus2Name[$ue->ebook_status] : '未知状态';
                $ret[] = $tmp;
            }
        }

        return $ret;
    }

    public function getUserOneEbook() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookId = !empty($this->_get['user_ebook_id']) ? $this->_get['user_ebook_id'] : 0;

//        if (empty($userId)) {
//            throw new \Exception('用户ID不能为空', ErrorCode::EBOOK_USER_ID_EMPTY);
//        }

        $userEbook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->andFilterWhere(['id' => $ebookId])
//            ->orderBy(['id' => SORT_DESC])
            ->one();

        $ret = [];

        if (!empty($userEbook)) {
            $tmp = $userEbook->toArray();
            $tmp['created_at_str'] = Date('Y-m-d H::s', $userEbook->created_at);
            $tmp['ebook_story_params'] = json_decode($userEbook->ebook_story_params, true);
            $tmpRes = $userEbook->ebookRes;
            $tmpData = [];
            if (!empty($tmpRes)) {
//                $tmpRes = $tmpRes->toArray();
                foreach ($tmpRes as $tmpRow) {
                    $tmpOne = $tmpRow->toArray();
                    unset($tmpOne['ebook_story_params']);
//                    if (!empty($tmpOne['ebook_story_params'])) {
//                        $tmpOne['ebook_story_params'] = json_decode($tmpRow['ebook_story_params'], true);
//                    }
                    $tmpData[$tmpOne['poi_id']] = $tmpOne;
                }
            }
            if (!empty($tmp['ebook_story_params']['pois'])) {
                foreach ($tmp['ebook_story_params']['pois'] as &$poiRow) {
                    if (!empty($tmpData[$poiRow['poi_id']])) {
                        $poiRow['user_ebook_res'] = $tmpData[$poiRow['poi_id']];
                    }
                }
            }
//            $tmp['user_ebook_res'] = $tmpData;
            $ret = $tmp;
        }

        return $ret;
    }

    public function getUserLastEbook() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

//        if (empty($userId)) {
//            throw new \Exception('用户ID不能为空', ErrorCode::EBOOK_USER_ID_EMPTY);
//        }

        $userEbook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $ret = [];

        if (!empty($userEbook)) {
//            foreach ($userEbook as $ue) {
            $tmp = $userEbook->toArray();
            $tmp['created_at_str'] = Date('Y-m-d H::s', $userEbook->created_at);
            $tmp['user_ebook_res'] = $userEbook->ebookRes;
            $ret = $tmp;
//            }
        }

        return $ret;
    }

    public function upload() {
        if (!empty($_FILES['fileUpload'])) {
            $file = $_FILES['fileUpload'];
        } else {
            $file = $_FILES['file'];
        }

        if (empty($file)) {
            Yii::error('[JNCITY] 上传文件不能为空');
            throw new \Exception('[JNCITY]上传文件不能为空', ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        // 检查文件上传错误
        if (!empty($file['error'])) {
            $errorMsg = $this->getUploadErrorMessage($file['error']);
            Yii::error('[JNCITY] 文件上传错误: ' . $errorMsg . ', 文件信息: ' . json_encode($file, JSON_UNESCAPED_UNICODE));
            throw new \Exception('文件上传失败: ' . $errorMsg, ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        // 检查文件大小
        $maxFileSize = $this->getMaxUploadSize();
        if ($file['size'] > $maxFileSize) {
            $maxSizeMB = round($maxFileSize / 1024 / 1024, 2);
            Yii::error('[JNCITY] 文件大小超限: ' . $file['size'] . ' > ' . $maxFileSize);
            throw new \Exception("文件大小超过限制，最大允许 {$maxSizeMB}MB", ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        $request = $_REQUEST;
        $userId = !empty($request['user_id']) ? $request['user_id'] : 0;
        $ebookStoryId = !empty($request['ebook_story_id']) ? $request['ebook_story_id'] : 0;
        $poiId = !empty($request['poi_id']) ? $request['poi_id'] : 0;

        $filePath = !empty($file['tmp_name']) ? $file['tmp_name'] : '';
        if (empty($filePath)) {
            Yii::error('[JNCITY] 上传文件失败' . json_encode($file, JSON_UNESCAPED_UNICODE));
            throw new \Exception('上传文件失败', ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        $accessKeyId = Yii::$app->params['oss.accesskeyid'];
        $accessKeySecret = Yii::$app->params['oss.accesskeysecret'];
        $endpoint = Yii::$app->params['oss.endpoint'];
        $host = Yii::$app->params['oss.host'];
        $bucket = Yii::$app->params['oss.bucket'];

        $ossFileName = 'jncity/images/' . basename($filePath);

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $ossClient->uploadFile($bucket, $ossFileName, $filePath);

            $newImageUrl = 'https://' . $bucket . '.' . $endpoint . '/' . $ossFileName;

        } catch (OssException $e) {
            throw new \Exception('OSS上传文件失败', ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        try {
            $ret = Yii::$app->ebook->generateVideoBase64WithEbookStory($ebookStoryId, $userId, $poiId, $newImageUrl);
//            sleep(3);
//            $ret = false;
            if ($ret === false) {
                Yii::error('[JNCITY]生成视频失败，返回结果：' . json_encode($ret, JSON_UNESCAPED_UNICODE));
                throw new \Exception('AI错误', ErrorCode::EBOOK_GEN_VIDEO_FAILED);
            }
//            $videoId = '';
//            if (!empty($ret['id'])) {
//                $videoId = $ret['id'];
//            }
        } catch (\Exception $e) {
            Yii::error('[JNCITY]生成视频失败: ' . $e->getMessage());
//            throw new \Exception('生成视频失败: ' . $e->getMessage(), ErrorCode::EBOOK_GEN_VIDEO_FAILED);
            return [
                'code' => -100,
                'msg' => '生成视频失败: ' . $e->getMessage(),
            ];
        }

        return [
            'code' => 0,
            'msg' => '上传成功，视频生成中，请您稍后关注……',
        ];

    }

    public function generateVideo() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookStoryId = !empty($this->_get['ebook_story_id']) ? $this->_get['ebook_story_id'] : 0;
        $poiId = !empty($this->_get['poi_id']) ? $this->_get['poi_id'] : 0;
        $imageUrl = !empty($this->_get['image_url']) ? $this->_get['image_url'] : '';

        try {
            $ret = Yii::$app->ebook->generateVideoWithEbookStory($ebookStoryId, $userId, $poiId, $imageUrl);
            $videoId = '';
            if (!empty($ret['id'])) {
                $videoId = $ret['id'];
            }
        } catch (\Exception $e) {
            throw new \Exception('生成视频失败: ' . $e->getMessage(), ErrorCode::EBOOK_GEN_VIDEO_FAILED);
        }

        return [
            'msg' => '视频生成中，ID：' . $videoId,
        ];
    }

    public function checkHailuoVideo() {
        $challenge = !empty($this->_get['challenge']) ? $this->_get['challenge'] : '';

        if (!empty($challenge)) {
            return ['challenge' => $challenge];
        } else {
            return ['status' => 'success'];
        }
    }



    public function getStory() {

        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

//        $story = !empty(UserEBook::$storyList[$storyId]) ? UserEBook::$storyList[$storyId] : [];
//        $story['poi'] = !empty(UserEBook::$poiList[$story['poi']])
//            ? UserEBook::$poiList[$story['poi']] : [];

//        $story = !empty(UserEBook::$poiList[$storyId]) ? UserEBook::$poiList[$storyId] : [];
        $story = Yii::$app->ebook->getStoryParams($storyId);

        return $story;

    }

    /**
     * 获取文件上传错误信息
     * @param int $errorCode
     * @return string
     */
    private function getUploadErrorMessage($errorCode)
    {
        $errorMessages = [
            UPLOAD_ERR_OK => '没有错误',
            UPLOAD_ERR_INI_SIZE => '文件大小超过了 php.ini 中 upload_max_filesize 的值',
            UPLOAD_ERR_FORM_SIZE => '文件大小超过了表单中 MAX_FILE_SIZE 的值',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => '文件上传被扩展程序中断',
        ];

        return isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : '未知错误';
    }

    /**
     * 获取最大上传文件大小
     * @return int
     */
    private function getMaxUploadSize()
    {
        $maxUploadSize = ini_get('upload_max_filesize');
        $maxPostSize = ini_get('post_max_size');
        $memoryLimit = ini_get('memory_limit');

        // 转换为字节
        $maxUploadSize = $this->convertToBytes($maxUploadSize);
        $maxPostSize = $this->convertToBytes($maxPostSize);
        $memoryLimit = $this->convertToBytes($memoryLimit);

        // 取最小值
        return min($maxUploadSize, $maxPostSize, $memoryLimit);
    }

    /**
     * 将 PHP 大小字符串转换为字节
     * @param string $size
     * @return int
     */
    private function convertToBytes($size)
    {
        $size = strtolower(trim($size));
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;

        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }

        return $size;
    }

}