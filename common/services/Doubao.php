<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\Subject;
use common\models\Qa;
use common\models\StoryMatch;
use common\models\UserExtends;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class Doubao extends Component
{

//    const CHATGPT_HOST = 'https://api.openai.com/v1';
    const CHATGPT_HOST = 'https://ark.cn-beijing.volces.com/api';

    public $apiKey;

    private $_token;

    const ROLE_GENERATE_SUBJECT = '你是一个小灵镜，负责出题和解答';

    public function generateSubject($userMessagePre = '', $level = 0, $matchClass = 0, $ct = 10, $extends = array()) {
        $ret = [];
        $gradeName = $this->_getGradeNameFromLevel($level);

        $matchClassName = !empty(StoryMatch::$matchClass2Name[$matchClass]) ? StoryMatch::$matchClass2Name[$matchClass] : '任意';

        if (!empty($userMessagePre)) {
            $jsonExample = json_encode([[
                'SUBJECT' => '小明有 5 个苹果，小红比小明多 3 个，那么小红有几个苹果？',
                'OPTIONS' => ['A' => '8个', 'B' => '7个', 'C' => '6个', 'D' => '5个'],
                'ANSWER' => 'A',
                'TYPE'  => '应用题',
                'POINT' => ['COMPUTE', 'READ'],
            ],[
                'SUBJECT' => 'SUBJECT2',
                'OPTIONS' => ['A' => 'AAA', 'B' => 'BBB', 'C' => 'CCC', 'D' => 'DDD'],
                'ANSWER' => 'A',
                'TYPE'  => '题型',
                'POINT' => ['LOGIC', 'REMEMBER'],
            ]], JSON_UNESCAPED_UNICODE);
            $msgTemplate = [
                '#角色' . "\n" . '你是一个教育方面的老师，你负责出题，解答和解析，根据下面规则出题',
                '#任务描述和要求',
                '针对' . $gradeName  . $matchClassName . '，生成不同的题目，' . $ct . '道，每次生成的题目都不能相同。',
                '给出题目考察的知识点，从计算能力(COMPUTE)，逻辑能力(LOGIC)，想象力(IMAGINATION)，创造力(CREATIVE)，记忆力(REMEMBER)，分析能力(ANALYSIS)，阅读能力(READ)，中选择1-3个最考察的点。',
                '题目类型为选择题。',
                '将生成的题目以 JSON 格式呈现，例如：' . $jsonExample,
                //[{"SUBJECT":"小明有 5 个苹果，小红比小明多 3 个，那么小红有几个苹果？", "OPTIONS": "A.8 个 B.7 个 C.6 个 D. 5 个","answer":"A"}]',
                '确保题目内容符合' . $gradeName . '学生的知识水平和理解能力。',
//                '适合' . $gradeName . '同学的题目，科目是：' . $matchClassName . '，请出'. $ct . '道，题目随机一些，尽可能规避历史已经出过的',
                '#输出格式#' . '输出题目、题型、标准答案和近似的三个选项答案，用ABCD表示。输出格式为JSON',
                '#输出样例#' . $jsonExample,
            ];
//$level = 8;
            if (!empty($extends['exampleTopics'])) {
                $tmpTopics = [];
                foreach ($extends['exampleTopics'] as $exampleTopic) {
                    $tmpTopics[] = $exampleTopic['topic'];
                }
                $tmpTopicStr = implode("\n#参考例题#", $tmpTopics);
                $msgTemplate[] = "#参考例题#" . $tmpTopicStr;
            } else {
                if (empty($extends['oldTopics'])) {
                    $qaClass = !empty(StoryMatch::$matchClass2QaClass[$matchClass]) ? StoryMatch::$matchClass2QaClass[$matchClass] : Subject::SUBJECT_CLASS_NORMAL;
                    $oldQa = Qa::find()
                        ->where([
                            'level' => $level,
                            'qa_class' => $qaClass,
                        ])
//                        ->orderBy('rand()')
                        ->orderBy('id desc')
                        ->limit(10)
//                        ->createCommand()
//                        ->getRawSql();
//                    var_dump($oldQa);exit;
                        ->all();
//var_dump($oldQa);exit;
                    if (!empty($oldQa)) {
                        $tmpTopics = [];
                        foreach ($oldQa as $oQa) {
                            $tmpTopics[] = $oQa->topic;
                        }
                        $tmpTopicStr = implode("\n#参考例题#", $tmpTopics);
                        $msgTemplate[] = "#参考例题#" . $tmpTopicStr;
                    }
                }
            }

            if (!empty($extends['oldTopics'])) {
                $tmpTopics = [];
                foreach ($extends['oldTopics'] as $oldTopic) {
                    $tmpTopics[] = $oldTopic['topic'];
                }
                $tmpTopicStr = implode("\n#参考例题#", $tmpTopics);
                $msgTemplate[] = "#参考例题#" . $tmpTopicStr;
            }

//            $userMessageTmp = $userMesssagePre . "\n" . implode("\n", $msgTemplate);
//            var_dump($msgTemplate);

            $response = $this->chatWithDoubao($userMessagePre, [], [], $msgTemplate);
            $messages = !empty($response['choices'][0]['message']['content']) ? $messages = $response['choices'][0]['message']['content'] : '';

            if (!empty($messages)) {
                $ret = json_decode($messages, true);
            }

        } else {

        }

        return $ret;
    }

    /**
     * 生成题目
     * @param int $level
     * @param int $matchClass
     * @param int $ct
     * @return array
     * 暂时作废
     */
    public function generateSubjectWithMode($level = 0, $matchClass = 0, $ct = 10, $templateContents = array()) {
        return true;
//        foreach (UserExtends::$userGradeLevelMap as $grade => $gradeLevel) {
//            if ($level >= $gradeLevel) {
//                $gradeName = UserExtends::$userGrade2Name[$grade];
//            } else {
//                break;
//            }
//        }
        $gradeName = $this->_getGradeNameFromLevel($level);
        $oldMessages = [];
        $roleTxt = self::ROLE_GENERATE_SUBJECT;
        switch ($matchClass) {
            case Subject::SUBJECT_CLASS_ENGLISH:
                $userMessage = '请出' . $ct . '道英语题目，题目是一个英文短句，答案是这个英文单词对应的中文。分为ABCD四个选项，其中随机一个选项是正确的，其他三个是近似但错误的。难度是适合' . $gradeName . '。';
                $userMessage .= '#输出格式#' . '输出题目、题型、选项和答案。输出格式为JSON';
                $userMessage .= '#输出样例#' . json_encode([[
                        'SUBJECT' => 'SUBJECT1',
                        'OPTIONS' => ['A' => 'AAA', 'B' => 'BBB', 'C' => 'CCC', 'D' => 'DDD'],
                        'ANSWER' => 'A',
                        'TYPE'  => '题型',
                    ],[
                        'SUBJECT' => 'SUBJECT2',
                        'OPTIONS' => ['A' => 'AAA', 'B' => 'BBB', 'C' => 'CCC', 'D' => 'DDD'],
                        'ANSWER' => 'A',
                        'TYPE'  => '题型',
                    ]], JSON_UNESCAPED_UNICODE);
                break;
            default:
                $userMessage = '请出' . $ct . '到题目，题目可以是数学，英语，语文，历史，生物，物理，或者冷门知识。分为ABCD四个选项，其中随机一个选项是正确的，其他三个是近似但错误的。难度是适合' . $gradeName . '。';
                break;
        }
        $response = $this->chatWithDoubao($userMessage, $oldMessages, $templateContents, $roleTxt);

        $messages = !empty($response['choices'][0]['message']['content']) ? $messages = $response['choices'][0]['message']['content'] : '';

        return $messages;
    }

    public function generateSuggestionFromSubject($topic, $level = 0, $matchClass, $ques, $oldMessages) {

        $gradeName = $this->_getGradeNameFromLevel($level);

        $matchCLassName = '';
        if (!empty(StoryMatch::$matchClass2Name[$matchClass])) {
            $matchCLassName = StoryMatch::$matchClass2Name[$matchClass];
        }

        $roleMessages = [];
        $simple = json_encode([
            'SUGGEST' => '问题的引导过程或者答案',
            'QUESTIONS' => [
                '可能还有的问题1',
                '可能还有的问题2',
                '可能还有的问题3',
                '可能还有的问题4',
            ],
        ]);
        $roleMessages[] = '#角色' . "\n" . '你是一个教育方面的老师';
        if (!empty($ques)) {
            $roleMessages[] = '#特点' . "\n" . '你直来直往，会直接给出问题答案';
        }
        $roleMessages[] = '#任务描述和要求';
        if (empty($ques)) {
            $roleMessages[] = '你根据题目内容，提供明确的引导信息，学生可以在思考下完成解题';
            $roleMessages[] = '利用引导式教学，引导学生思考，不要直接给出答案';
        } else {
            $roleMessages[] = '给出明确的答案或者提示';
        }
        $roleMessagesFormat = [
            '内容不超过200字',
            '并且给出，当前如果继续解题，可能还存在的4个方面问题，每个问题不超过18个字，并用指定格式返回',
            '用JSON的形式返回',
            '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
        ];
        $roleMessages = array_merge($roleMessages, $roleMessagesFormat);
//        var_dump($roleMessages);exit;
        switch ($matchClass) {
            case Subject::SUBJECT_CLASS_POEM:
                $userMessage = '这是一首诗的一句，题目内容是：' . $topic . '，问号(?)是原诗缺少的字。请说出这首诗的出处，作者和当时背景故事';
//                是语文老师，不说答案，通过这首诗的出处，作者，故事，引导学生作答
                break;
            case Subject::SUBJECT_CLASS_POEM_IDIOM:
                $userMessage = '这是一个成语，题目内容是：' . $topic . '，问号(?)是原成语缺少的字。请描述这个成语表达的含义，但是不要说出答案';
                break;
            case Subject::SUBJECT_CLASS_MATH:
            default:
                $userMessage = '这是一道' . $matchCLassName . '题目，题目内容是：' . $topic;
//                . '，问号(?)是要填写的答案。现在你扮演一名教育专家，不要给出答案，引导一名' . $gradeName . '的学生，请给出提示，200字之内';
                break;
        }
        if (!empty($ques)) {
            $userMessage .= "\n" . '，学生现在问题是：' . $ques;
        }
//        $userMessage .= '#输出格式#' . '输出提示方法';

        $response = $this->chatWithDoubao($userMessage, $oldMessages, [], $roleMessages);

//        var_dump($response);exit;
        $messages = $response['choices'][0]['message']['content'];

        $ret = json_decode($messages, true);

        return $ret;
    }

    public function chatWithDoubao($userMessage, $oldMessages = [], $templateContents = array(), $roleTxts = array()) {

        if (empty($roleTxts)) {
            $roleTxt = '#角色' . "\n" . '你是一个教育方面的老师，你负责出题，解答和解析';
            $templateMessages[] = array('role' => 'system', 'content' => $roleTxt);
        } else {
            foreach ($roleTxts as $roleTxt) {
                $templateMessages[] = array('role' => 'system', 'content' => $roleTxt);
            }
        }
        $messages = array(
//            array('role' => 'system', 'content' => $roleTxt),
            array('role' => 'user', 'content' => $userMessage)
        );

//        $templateMessages = array();
        if (!empty($templateContents)) {
            foreach ($templateContents as $templateContent) {
                $templateMessages[] = array('role' => 'assistant', 'content' => $templateContent);
            }
        }

        if (!empty($oldMessages)) {
            array_unshift($oldMessages, ['role' => 'system', 'content' => '#历史消息']);
            $messages = array_merge($messages, $oldMessages);
        }
        $messages = array_merge($templateMessages, $messages);
        Yii::info('doubao messages: ' . json_encode($messages, JSON_UNESCAPED_UNICODE));
//        if (!empty($oldMessages)) {
//            var_dump($messages);exit;
//        }
//        var_dump($messages);
//        print_r($messages);
//        exit;

        $data = array(
//            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
//            'model' => 'ep-20240628070258-6m88j',
            'model' => 'ep-20240729104951-snm9z',
            'messages' => $messages,
            'temperature' => 0.7,
//            'stream' => false,
        );

//        Yii::info('chatGPT data: ' . json_encode($data));

        $response = $this->_call('/v3/chat/completions', $data, 'POST');
//        if (!empty($oldMessages)) {
//            print_r($oldMessages);
//            print_r($messages);
//            print_r($response);
//        }
        Yii::info('doubao ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
//        file_put_contents('/tmp/doubao.tmp', json_encode($response, JSON_UNESCAPED_UNICODE));
//        var_dump($response);
//        exit;

        return json_decode($response, true);
    }

    public function callOpenAIChatGPT($userMessage, $templateContents = array()) {
//        $apiKey = $this->apiKey;
//        $url = self::CHATGPT_HOST . '/chat/completions';

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

    public function generateQAEmbeddingFromChatGPT($question, $answer) {
        $data = array(
            'model' => 'text-embedding-3-small',  // 或者使用其他模型
            'input' => $question . ' ' . $answer
        );

        $response = $this->_call('/embeddings', $data, 'POST');

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

    private function _formatResponse($response) {
        $messages = array();
        foreach ($response['choices'] as $choice) {
            $messages[] = array(
                'role' => 'assistant',
                'content' => $choice['message']['content']
            );
        }

        return $messages;
    }

    private function _call($interface, $params = array(), $method = 'POST') {
        $url = self::CHATGPT_HOST . $interface;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        );

        if ($method == 'POST') {
            $response = Curl::curlPost($url, $params, $headers, true);
        } else {
            $response = Curl::curlGet($url);
        }
        Yii::info('doubao ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
//        file_put_contents('/tmp/tmp.tmp', $response);
//        var_dump($response);exit;

//        return json_decode($response, true);
        return $response;
    }

    private function _getGradeNameFromLevel($level = 0) {
        $gradeName = '';
        foreach (UserExtends::$userGradeLevelMap as $grade => $gradeLevel) {
            if ($level >= $gradeLevel) {
                $gradeName = UserExtends::$userGrade2Name[$grade];
            } else {
                break;
            }
        }
        return $gradeName;
    }

//// 示例调用
//$userMessage = '提问问题1';
//$response = callOpenAIChatGPT($userMessage);
//
//// 输出ChatGPT生成的回答
//echo $response['choices'][0]['message']['content'];


}