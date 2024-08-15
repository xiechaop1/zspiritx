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
                'SUBJECT' => '题目（无选项）',
                'OPTIONS' => ['A' => '答案A', 'B' => '答案B', 'C' => '答案C', 'D' => '答案D'],
                'ANSWER' => 'A',
                'TYPE'  => '数学应用题',
                'POINT' => ['COMPUTE', 'READ'],
                'EXTEND' => '扩展信息',
            ],[
                'SUBJECT' => 'SUBJECT2(NO OPTIONS)',
                'OPTIONS' => ['A' => 'AAA', 'B' => 'BBB', 'C' => 'CCC', 'D' => 'DDD'],
                'ANSWER' => 'A',
                'TYPE'  => '题目分类',
                'POINT' => ['LOGIC', 'REMEMBER'],
                'EXTEND' => 'EXTEND',
            ]], JSON_UNESCAPED_UNICODE);
            $msgTemplate = [
                '#角色#' . "\n" . '你是一个教育专家',
                '#任务描述和要求#',
                '针对' . $gradeName  . $matchClassName . '生成不同的题目' . $ct . '道，每次生成的题目都不能相同。',
                '参考例题中的题型，但是更换内容',
                '给出题目考察的知识点，从计算能力(COMPUTE)，逻辑能力(LOGIC)，想象力(IMAGINATION)，创造力(CREATIVE)，记忆力(REMEMBER)，分析能力(ANALYSIS)，阅读能力(READ)，中选择1-3个最考察的点。',
                '题目类型为单选题，题干没有选项，选项放到OPTIONS中',
                '将生成的题目以 JSON 格式呈现',
//                ，例如：' . $jsonExample,
                //[{"SUBJECT":"小明有 5 个苹果，小红比小明多 3 个，那么小红有几个苹果？", "OPTIONS": "A.8 个 B.7 个 C.6 个 D. 5 个","answer":"A"}]',
//                '确保题目内容符合' . $gradeName . '学生的知识水平和理解能力。',
//                '适合' . $gradeName . '同学的题目，科目是：' . $matchClassName . '，请出'. $ct . '道，题目随机一些，尽可能规避历史已经出过的',
                '#输出格式#' . '输出题目、题型、标准答案和近似的三个选项答案，用ABCD表示。如果有扩展信息（如：图片描述，短文等）放入EXTEND字段。输出格式为JSON',
                '#输出样例#' . $jsonExample,
            ];
//            $userMessagePre = '你是一个教育方面的老师，负责出题、解答和解析，
//            针对三年级数学生成不同的题目5道，每次生成的题目要保持不同。
//            题目类型为选择题，返回的结果用JSON格式。请确保题目符合三年级的知识水平和理解能力。输出题目、题型、标准答案和近似的三个选项';

//$level = 8;
            if (1 == 1) {
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
                        ->orderBy('rand()')
//                            ->orderBy('id desc')
                            ->limit(50)
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
            }


//            $userMessageTmp = $userMesssagePre . "\n" . implode("\n", $msgTemplate);
//            var_dump($msgTemplate);

//            $userMessagePre = '你是一个教育方面的老师，负责出题、解答和解析，针对五年级数学生成不同的题目5道，符合五年级学习的知识体系，每次生成的题目要保持不同。题目类型为选择题，返回的结果用JSON格式。请确保题目符合五年级的知识水平和理解能力。输出题目、题型、标准答案和近似的三个选项';
//            $userMessagePre = '你是教育方面老师，针对5年级学生的历史知识体系，包含：中国古代史，夏商到战国时期为主。返回结果用JSON格式。题目随机。';
//            $userMessagePre .= "\n出5道题目，每次生成的题目都不能相同。";
//            $msgTemplate = [];
            $response = $this->chatWithDoubao($userMessagePre, [], [], $msgTemplate);
//            var_dump($response);
//            exit;
            $messages = !empty($response['choices'][0]['message']['content']) ? $messages = $response['choices'][0]['message']['content'] : '';

            if (!empty($messages)) {
                $ret = json_decode($messages, true);
            }

            file_put_contents('/tmp/test_doubao_sub.log', var_export($userMessagePre, true) . "\n\n\n" . var_export($ret, true));

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
                '当前引导的答案',
//                '可能还有的问题1',
                '可能还有的问题1',
                '可能还有的问题2',
                '可能还有的问题3',
//                '可能还有的问题4',
            ],
        ]);
        $roleMessages[] = '#角色' . "\n" . '你是一个教育方面的老师';
        if (!empty($ques)) {
            $roleMessages[] = '#特点' . "\n" . '你直来直往，会直接给出问题答案';
        }
        $roleMessages[] = '#任务描述和要求';
        if (empty($ques)) {
            $roleMessages[] = '你根据题目内容，提供解题的思路引导，在2个步骤下可以解出题目，并且提示出第1个步骤，并且在QUESTIONS中第一条给出这个步骤的答案，学生可以在思考下完成解题';
            $roleMessages[] = '利用引导式教学，引导学生思考，不要直接给出答案';
        } else {
            $roleMessages[] = '根据题目内容和之前的引导，给出接下来的解题步骤，并且在QUESTIONS中的第一条给出这个步骤的答案';
        }
        $roleMessagesFormat = [
            '内容不超过200字',
//            '然后再给出4条继续对答的建议，其中第1条是当下这个引导步骤的的答案，如：引导的是2个9相加等于几？返回：18，',
            '再给出3条这个引导下可能还存在的问题。均不超过18个字，并且按照指定的格式给出',
//            '给出4条回答建议，第1条是给出的引导的答案，后3条是，如果继续解题可能还存在的问题。每个问题不超过18个字，并用指定格式返回',
//            '给出4个解题引导的可能得答案。每个问题不超过18个字，并用指定格式返回',
//            '给出4条回答建议，前2条是根据当前给出的引导，可能得答案；后2条是，如果继续解题可能还存在的问题。每个问题不超过18个字，并用指定格式返回',
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
            $userMessage .= "\n" . '，学生现在问题是：' . $ques . '，接下来呢？请给出引导';
        }
//        $userMessage .= '#输出格式#' . '输出提示方法';

        $response = $this->chatWithDoubao($userMessage, $oldMessages, [], $roleMessages);

        file_put_contents('/tmp/test_doubao.log', var_export($roleMessages, true) . "\n\n\n" . var_export($response, true));
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

        $messages = [];
        if (!empty($userMessage)) {
            $messages = array(
//            array('role' => 'system', 'content' => $roleTxt),
                array('role' => 'user', 'content' => $userMessage)
            );
        }

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
        file_put_contents('/tmp/test_doubao_i.log', var_export($messages, true));
//        if (!empty($oldMessages)) {
//            var_dump($messages);exit;
//        }
//        var_dump($messages);
//        exit;
//        print_r($messages);
//        exit;

        $data = array(
//            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
            'model' => 'ep-20240628070258-6m88j',
//            'model' => 'ep-20240729104951-snm9z',
            'messages' => $messages,
            'temperature' => 0.8,
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