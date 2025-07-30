<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\Subject;
use common\models\GptContent;
use common\models\Qa;
use common\models\StoryMatch;
use common\models\UserEBook;
use common\models\UserEBookRes;
use common\models\UserExtends;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class EBook extends Component
{

//    const CHATGPT_HOST = 'https://api.openai.com/v1';

    // Todo: 替换掉Doubao接口
//    const CHATGPT_HOST = 'https://ark.cn-beijing.volces.com/api';
//    const CHATGPT_HOST = 'https://api.deepseek.com/';

    const CHATGPT_HOST = 'https://api.siliconflow.cn/';

    public $apiKey;

    private $_token;

    public $model = '';
    public $temperature = '';
    public $host = '';
    public $uri = '';

    private $_prompt = [];
    private $_msgId = '';

    public static $ebookParams = [
        1 => [
            'title' => 'Story 1',
            'story' => 'Story Content',
            'pois' => [
                1 => [
                    [
                        'page' => 1,
                        'poi_id' => 1,
                        'story' => 'Page Story 1',
                        'prompt' => '',
                        'duration' => 10,
                    ],
                ],
            ],
        ],
    ];


    public function generateVideo($userMessage, $image = '', $params = []) {
        $modelParams = $params;
        $prompt = $this->_genPrompt($userMessage, $image, 'file');
        $ret = $this->chatWithDoubao($prompt, $modelParams);

        return $ret;
    }

    public function generateVideoBase64($userMessage, $image = '', $params = []) {
        $modelParams = $params;
        if (!\common\helpers\Common::isBase64($image)) {
            // 通过$image（文件路径），获取图片类型
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $imageType = 'jpeg';
                    break;
                case 'png':
                    $imageType = 'png';
                    break;
                case 'gif':
                    $imageType = 'gif';
                    break;
                default:
                    $imageType = 'jpeg';
                    break;
            }
            $imageBase64 = base64_encode(file_get_contents($image));
        } else {
            $imageBase64 = $image;
            $imageType = 'jpeg';
        }
        $prompt = $this->_genPrompt($userMessage, $imageBase64, $imageType);
        $ret = $this->chatWithDoubao($prompt, $modelParams);

        return $ret;
    }

    private function _genPrompt($userMessage, $image, $imageType = 'jpg') {
        $text = [
            'type' => 'text',
            'text' => $userMessage
        ];
        if ($imageType == 'file') {
            $img = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $image
                ]
            ];
        } else {
            $img = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => 'data:image/' . $imageType . ';base64,' . $image,
                ]
            ];
        }
        $prompt = [
            $text,
            $img
        ];

        return $prompt;
    }

    public function generateVideoWithEbookStory($ebookStoryId , $userId, $poiId = 1, $image, $params = []) {
        $ebookParam = !empty(UserEBook::$poiList[$ebookStoryId])
            ? UserEBook::$poiList[$ebookStoryId] : [];

        if (empty($ebookParam)) {
            return false;
        }

        $storyId = 100;

        $pois = !empty($ebookParam['pois'][$poiId])
            ? $ebookParam['pois'][$poiId] : [];

        if (empty($poi)) {
            return false;
        }

        $userEbookId = $this->newEBookToDb($userId, $ebookStoryId, $ebookParam, $storyId);

        if (!empty($pois)) {
            $videoId = '';
            foreach ($pois as $poi) {
                $ret = $this->generateVideo($poi['prompt'], $image);
                if (!empty($ret['id'])) {
                    $videoId = $ret['id'];
                }
            }

            $this->newVideoToDb($userEbookId, $userId, $storyId, $ebookStoryId, $ebookParam, $poiId, $videoId);
        }
    }

    public function generateVideoBase64WithEbookStory($ebookStoryId , $userId, $poiId = 1, $image, $params = []) {
        $ebookParam = !empty(UserEBook::$poiList[$ebookStoryId])
            ? UserEBook::$poiList[$ebookStoryId] : [];

        if (empty($ebookParam)) {
            return false;
        }

        $storyId = 16;

        $pois = !empty($ebookParam['pois'][$poiId - 1])
            ? $ebookParam['pois'][$poiId - 1] : [];

        if (empty($pois)) {
            return false;
        }

        $userEbookId = $this->newEBookToDb($userId, $ebookStoryId, $ebookParam, $storyId);

        $isCreating = False;
        if (!empty($pois)) {
            foreach ($pois as $poi) {
                if (!empty($poi['resources'])) {
                    foreach ($poi['resources'] as $res) {
                        if (!empty($res['video_prompt'])) {
                            $ret = $this->generateVideoBase64($poi['video_prompt'], $image);
                            if (!empty($ret['id'])) {
                                $videoId = $ret['id'];
                                $this->newVideoToDb($userEbookId, $userId, $storyId, $ebookStoryId, $ebookParam, $poiId, $videoId);
                                $isCreating = True;
                            }
                        }
                    }
                }


            }


        }
        return $isCreating;
    }

    public function searchWithId($id) {
        $uri = '/v3/contents/generations/tasks/' . $id;

        $response = $this->_call($uri, [], 'GET', true);

        Yii::info('doubao search ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        file_put_contents('/tmp/doubao_search.log', json_encode($response, JSON_UNESCAPED_UNICODE));

        if (empty($response)) {
            return [];
        }

        $tmpRet =  json_decode($response, true);
        return $tmpRet;
    }

    public function chatWithDoubao($prompt, $modelParams =[], $cfg = [], $isJson = True) {

        $messages = $prompt;

//        $messages = $this->_genPrompt($userMessage, $image);
//        $this->_msgId = $msgId = md5(json_encode($messages, JSON_UNESCAPED_UNICODE) . microtime());
//        if (!empty($oldMessages)) {
//            var_dump($messages);exit;
//        }
//        var_dump($messages);
//        exit;
//        print_r($messages);
//        exit;

        if (!empty($cfg->apiKey)) {
            $this->apiKey = $cfg->apiKey;
        }

        if (!empty($cfg->host)) {
            $this->host = $cfg->host;
        }

        if (!empty($cfg->model)) {
            $this->model = $cfg->model;
        }

        if (!empty($cfg->temperature)) {
            $this->temperature = $cfg->temperature;
        }

        $model = !empty($modelParams['model']) ? $modelParams['model'] : $this->model;
        $temperature = !empty($modelParams['temperature']) ? $modelParams['temperature'] : $this->temperature;

        if (!empty($cfg->uri)) {
            $uri = $cfg->uri;
        } else {
            $uri = $this->uri;
        }
        $uri = empty($uri) ? '/v3/contents/generations/tasks' : $uri;

        $opts = [];

        // Todo:
        if (!empty($modelParams)) {

            $data = $modelParams;
            $data['model'] = $model;
            $data['messages'] = $messages;

            if (!empty($modelParams['duration'])) {
                $opts['duration'] = $modelParams['duration'];
            }

            if (!empty($modelParams['watermark'])) {
                $opts['watermark'] = $modelParams['watermark'];
            }

            if (!empty($modelParams['seed'])) {
                $opts['seed'] = $modelParams['seed'];
            }
            
            if (!empty($modelParams['camerafixed'])) {
                $opts['camerafixed'] = $modelParams['camerafixed'];
            }
            
        } else {
            $data = array(
//            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
//            'model' => 'deepseek-chat',
                'model' => $model,
//            'model' => 'ep-20240729104951-snm9z',
                'messages' => $messages,
                'radio' => 'adaptive',
//'prompt' => implode("\n", $templateContents),
//            'stream' => true,
//            "response_format" => [
//                'type' => 'json_object',
//            ],
            );
        }
//        print_r($data);exit;

        // Todo: 替换掉Doubao接口
//        $data = array(
////            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
//            'model' => 'ep-20240628070258-6m88j',
////            'model' => 'ep-20240729104951-snm9z',
//            'messages' => $messages,
//            'temperature' => 0.8,
////            'stream' => false,
//        );

        file_put_contents('/tmp/test_doubao_data.log', var_export($data, true));
//var_dump($data);exit;
//        Yii::info('chatGPT data: ' . json_encode($data));

        // Todo: 替换掉Doubao接口
//        $response = $this->_call('/v3/chat/completions', $data, 'POST');
        $response = $this->_call($uri, $data, 'POST', $isJson, $opts);
//        var_dump($data);exit;
//        $response = $this->_call('/beta/chat/completions', $data, 'POST');
//        var_dump($response);exit;
//        if (!empty($oldMessages)) {
//            print_r($oldMessages);
//            print_r($messages);
//            print_r($response);
//        }
        Yii::info('doubao ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
        file_put_contents('/tmp/doubao.log', json_encode($response, JSON_UNESCAPED_UNICODE));
//        var_dump($response);
//        exit;

        $ret = $tmpRet =  json_decode($response, true);

        return $ret;
    }

    public function callOpenAIChatGPT($userMessage, $templateContents = array()) {
//        $apiKey = $this->apiKey;
//        $url = $this->host . '/chat/completions';

        $messages = array(
            array('role' => 'system', 'content' => '你是一个灵镜新世界的小灵镜，专门解答各种问题'),
            array('role' => 'user', 'content' => $userMessage)
        );

//        // 添加一组预定义的问题和答案
//        $templateMessages = array(
//            array('role' => 'assistant', 'content' => '回答问题1的内容'),
//            array('role' => 'assistant', 'content' => '回答问题2的内容'),
//            // 添加更多的问题和答案
//        );

        $templateMessages = array();
        if (!empty($templateContents)) {
            foreach ($templateContents as $templateContent) {
                $templateMessages[] = array('role' => 'assistant', 'content' => $templateContent);
            }
        }

        $data = array(
            'model' => 'gpt-3.5-turbo',  // 或者使用其他模型
            'messages' => array_merge($messages, $templateMessages),
            'temperature' => 0.7
        );

        $response = $this->_call('/chat/completions', $data, 'POST');

//        if (curl_errno($ch)) {
//            echo 'Error:' . curl_error($ch);
//        }

//        curl_close($ch);

        return json_decode($response, true);
    }

    public function text2Speech($text, $voice = 'nova') {
        try {
            $data = array(
                'model' => 'tts-1',  // 或者使用其他模型
                'input' => $text,
                'voice' => $voice
            );

            $steam = $this->_call('/audio/speech', $data, 'POST');

            $tempFile = '/tmp/' . md5($text) . '.mp3';

            $saveFile = Yii::$app->basePath . '/web' . $tempFile;

            file_put_contents($saveFile, $steam);

            return $tempFile;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function newEBookToDb($userId, $ebookStoryId, $ebookParam, $storyId) {
        $userEbook = UserEBook::find()
            ->where([
                'user_id' => $userId,
                'ebook_story' => $ebookStoryId,
//                    'ebook_status' => UserEBook::USER_EBOOK_STATUS_DEFAULT
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (is_array($ebookParam)) {
            $ebookStoryParams = json_encode($ebookParam, JSON_UNESCAPED_UNICODE);
        }

        if (empty($userEbook) || $userEbook->ebook_status == UserEBook::USER_EBOOK_STATUS_COMPLETED) {
            $userEbook = new UserEBook();
            $userEbook->user_id = $userId;
            $userEbook->story_id = $storyId;
            $userEbook->ebook_story = $ebookStoryId;
            $userEbook->ebook_story_params = $ebookStoryParams;
            $userEbook->ebook_status = UserEBook::USER_EBOOK_STATUS_DEFAULT;
            $r = $userEbook->save();

//            $userEbookId = Yii::$app->db->getLastInsertID();
            $userEbookId = $userEbook->getPrimaryKey();
        } elseif ($userEbook->ebook_status == UserEBook::USER_EBOOK_STATUS_DEFAULT) {
            $userEbook->ebook_story = $ebookStoryId;
            $userEbook->ebook_story_params = $ebookStoryParams;
            $r = $userEbook->save();
            $userEbookId = $userEbook->id;
        } else {
            $userEbookId = $userEbook->id;
        }

        return $userEbookId;
    }

    public function newVideoToDb($userEbookId, $userId, $storyId, $ebookStory, $ebookStoryParams, $poiId, $videoId) {
        try {

            $model = UserEBookRes::find()
                ->where([
                    'user_ebook_id'   => $userEbookId,
                    'poi_id' => $poiId,
                ])
                ->one();

            if (empty($model)) {
                $model = new UserEBookRes();
                $model->user_id = $userId;
                $model->story_id = $storyId;
                $model->ebook_story = $ebookStory;
                $model->ebook_story_params = $ebookStoryParams;
                $model->poi_id = $poiId;
                $model->ebook_res_status = UserEBookRes::USER_EBOOK_RES_STATUS_DEFAULT;
                $model->ai_video_m_id = $videoId;
                $rr = $model->save();
            } else if (
                in_array(
                    $model->ebook_res_status,
                    [
                        UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL,
                        UserEBookRes::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_SUCCESS
                    ]
                )
            ) {
                $model->ai_video_m_id = $videoId;
                $rr = $model->save();
            }

            $model->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return True;
    }

    public function getVideoFromDb($userId, $ebookStory, $poiId) {
        $model = UserEBookRes::find();
        if (!empty($userId)) {
            $model->andFilterWhere(['user_id' => $userId]);
        }
        if (!empty($ebookStory)) {
            $model->andFilterWhere(['ebook_story' => $ebookStory]);
        }
        if (!empty($poiId)) {
            $model->andFilterWhere(['poi_id' => $poiId]);
        }
        $model = $model->all();

        if (empty($model)) {
            return [];
        }

        $ret = [];
        foreach ($model as $row) {
            $tmp = $row->toArray();
            $tmp['ebook_story_params'] = json_decode($tmp['ebook_story_params'], true);
        }

        return $ret;
    }


    private function _call($interface, $params = array(), $method = 'POST', $isJson = true, $opts = [], $isStream = false) {
        if (!empty($host)) {
            $url = $host . $interface;
        } else {
            $url = $this->host . $interface;
        }

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        );

//        var_dump($params);
//        exit;
//        var_dump($url);
        if ($method == 'POST') {
            $response = Curl::curlPost($url, $params, $headers, true, $opts, $isStream);
        } else {
            $response = Curl::curlGet($url);
        }
        Yii::info('doubao ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
//        file_put_contents('/tmp/tmp.tmp', $response);
//        var_dump($response);exit;

//        return json_decode($response, true);
        return $response;
    }




}