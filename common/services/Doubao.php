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
use common\models\UserExtends;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class Doubao extends Component
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

    private $_prompt = [];

    const ROLE_GENERATE_SUBJECT = '你是一个小灵镜，负责出题和解答';

    public function talk($userMessage, $oldMessages = [], $params = [], $model = '', $temperature = '') {

        $roleTxt = '#角色#' . "\n" . '你是一个温柔的知心姐姐，喜欢读书，学富五车，懂得很多知识，可以回答各种问题';
        $simple = [
            'content' => '回答问题',
        ];
        $extMessages = [
            '你说话很温柔，适合6-12岁小朋友',
            '你很贴心，也很会提供情绪价值',
            '内容不超过200字',
            '用JSON的形式返回',
            '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
        ];

        $userId = !empty($params['userId']) ? $params['userId'] : 0;
        $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;
        $toUserId = !empty($params['toUserId']) ? $params['toUserId'] : 0;
        $toUserId = !empty($toUserId) ? $toUserId : $userId;
        $msgClass = GptContent::MSG_CLASS_NORMAL;

        $oldMessages = $this->getOldContents($userId, $toUserId, $msgClass);

        $ret = $this->chatWithDoubao($userMessage, $oldMessages, $extMessages, [$roleTxt], false);


        $prompt = $this->_prompt;

        $this->saveContentToDb($userId, $toUserId, $ret, $prompt, $msgClass, 0, $storyId, $this->model);

        return $ret;
    }

    public function getOldContents($userId, $toUserId, $msgClass = GptContent::MSG_CLASS_NORMAL, $needFirst = false) {
        $beginTime = strtotime('-5 minute');
        $limit = 3;
        if ($needFirst) {
            $lastFirst = $this->getContentsFromDb($userId, $toUserId, $msgClass, 0, $beginTime, 0, $limit, 0);
        }
        $lastContents = $this->getContentsFromDb($userId, $toUserId, $msgClass, 0, $beginTime, 0, $limit, 0);

        $oldContents = [];
        if (!empty($lastContents)) {
            foreach ($lastContents as $lastContent) {
                if (empty($lastContent['content'])
                    || empty(trim($lastContent['content'])
                    || $lastContent['content'] == '[]'
                    )
                ) {
                    continue;
                }
                if (!empty($lastContent['prompt'])) {
                    $oldPrompts = json_decode($lastContent['prompt'], true);
                    if (!empty($oldPrompts)) {
                        foreach ($oldPrompts as $oldPrompt) {
                            if (!empty($oldPrompt['role']) && $oldPrompt['role'] != 'system') {
                                $oldContents[] = $oldPrompt;
                            }
                        }
                    }
                }
                if (!empty($lastContent['content'])) {
                    if (\common\helpers\Common::isJson($lastContent['content'])) {
                        $contentObj = json_decode($lastContent['content'], true);
                        $content = !empty($contentObj['content']) ? $contentObj['content'] : $lastContent['content'];
                    } else {
                        $content = $lastContent['content'];
                    }
                    $oldContents[] = [
                        'role'  => 'assistant',
                        'content' => $content,
                    ];

                }
                break;
            }
        }
        return $oldContents;
    }

    public function generateNswc($userMessage, $params, $aiRole = 'host', $isFirst = false) {
        if ($aiRole == 'host') {
            $roleTxt = '#角色#' . "\n" . '你是一个你说我猜游戏主持人，你负责出题，解答，引导游戏的进行';
            if ($isFirst) {
                $simple = [
                    'content' => '你打招呼的一句话',
                    'answer' => '随机生成的答案',
//                'final' => '最终的答案（人物、事物或者物品）',
                ];
                $example = [
                    'content' => '我已经想好啦，准备开始吧！',
                    'answer' => '足球',
                ];
                $extMessages = [
                    '你随机生成一个常见的物体、事物、人物、动物均可',
                    '内容不超过50字',
                    '用JSON的形式返回',
                    '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
                    '#示例#' . json_encode($example, JSON_UNESCAPED_UNICODE),
                ];
            } else {
                $simple = [
                    'content' => '回答',
//                'final' => '最终的答案（人物、事物或者物品）',
                ];
                $example = [
                    'content' => '不是，不是足球',
                ];
                $extMessages = [
                    '你很温柔，适合6-12岁小朋友',
                    '你随机生成一个常见的物体、事物、人物、动物均可，然后返回答案',
                    '玩家询问这个物体的特征，而你只"肯定回答"或者"否定回答"，如果你也无法判断，你就回答"我也不知道"',
                    '最后如果玩家猜对了，你就告诉玩家"猜对了"，并且结束游戏',
                    '内容不超过50字',
                    '用JSON的形式返回',
                    '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
                    '#示例#' . json_encode($example, JSON_UNESCAPED_UNICODE),
                ];
            }


            $msgClass = GptContent::MSG_CLASS_NISHUOWOCAI_HOST;
        } else {
            $roleTxt = '#角色#' . "\n" . '你是一个你说我猜游戏参与者，你负责出题，解答，引导游戏的进行';
            $simple = [
                'content' => '你的猜测',
            ];
            $example = [
                'content' => '这个物体是黄色的吗？',
            ];
            $extMessages = [
                '你很温柔，适合6-12岁小朋友',
                '玩家想了一个常见的物体、事物、人物或者动物，你来猜猜玩家想的是什么',
                '你需要用封闭的问题',
                '玩家只负责回答是或者不是',
                '如果玩家回答是，那么你继续缩小范围',
                '如果玩家回答不是，那么你将换一个特征继续猜测',
                '直到玩家最后明确回答出物体是什么？或者中止游戏',
                '内容不超过200字',
                '用JSON的形式返回',
                '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
                '#示例#' . json_encode($example, JSON_UNESCAPED_UNICODE),
            ];
            $msgClass = GptContent::MSG_CLASS_NISHUOWOCAI_PLAYER;
        }

        $userId = !empty($params['userId']) ? $params['userId'] : 0;
        $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;
        $toUserId = !empty($params['toUserId']) ? $params['toUserId'] : 0;
        $toUserId = !empty($toUserId) ? $toUserId : $userId;

        if (!$isFirst) {
            $oldContents = $this->getOldContents($userId, $toUserId, $msgClass);
        } else {
            $oldContents = [];
        }


        $gptRet = $this->chatWithDoubao($userMessage, $oldContents, $extMessages, [$roleTxt], false);

        $prompt = $this->_prompt;
        $this->saveContentToDb($userId, $toUserId, $gptRet, $prompt, $msgClass, 0, $storyId, $this->model);

        if (!is_array($gptRet) && !\common\helpers\Common::isJson($gptRet)) {
            $ret['content'] = $gptRet;
        } else {
            $ret = $gptRet;
        }
        
        if (!empty($ret['answer'])
            && mb_strpos($ret['answer'], '你想的答案是') !== false
        ) {
            preg_match('/你想的答案是(.*)[。]?/', $ret['answer'], $matches);
            if (!empty($matches[1])) {
                $ret['answer'] = $matches[1];
            }
        }

        return $ret;
    }

    public function generateGuessByDescGame($userMessage, $params = []
//        , $oldMessages = []
//        , $aiRole = 'host'
    ) {
//        if ($aiRole == 'host') {
            $roleTxt = '#角色#' . "\n" . '你是一个描述猜物体的游戏主持人，你负责出题，解答，引导游戏的进行';
            $simple = [
                'content' => '你对你想的物体、事物、人物或者活动的描述',
                'answer' => '你想的答案'
            ];
            $extMessages = [
                '你想一个常见的物体、事物、人物或者活动，并且你描述一下它的特征',
                '在你描述过程中不能出现这个目标物品的每一个字',
                '并且最后你把答案一并返回',
                '内容不超过50字',
                '用JSON的形式返回',
                '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
            ];
//        } else {
//            $roleTxt = '#角色#' . "\n" . '你是一个小灵镜，负责出题和解答';
//            $simple = [
//                'content' => '回答问题',
//            ];
//            $extMessages = [
//                '内容不超过200字',
//                '用JSON的形式返回',
//                '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
//            ];
//        }

        $userId = !empty($params['userId']) ? $params['userId'] : 0;
        $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;
        $toUserId = !empty($params['toUserId']) ? $params['toUserId'] : 0;
        $toUserId = !empty($toUserId) ? $toUserId : $userId;
        $msgClass = GptContent::MSG_CLASS_GUESS_BY_DESCRIPTION;

//        $lastContents = Yii::$app->doubao->getContentsFromDb($userId, $userId, GptContent::MSG_CLASS_NORMAL, strtotime('-5 minute'), 0, 1);

//        $oldContents = $this->getOldContents($userId, $toUserId, $msgClass);

        $ret = $this->chatWithDoubao($userMessage, [], $extMessages, [$roleTxt], false);

        if (!empty($ret['answer'])
            && mb_strpos($ret['answer'], '你想的答案是') !== false
        ) {
            preg_match('/你想的答案是(.*)[。]?/', $ret['answer'], $matches);
            if (!empty($matches[1])) {
                $ret['answer'] = $matches[1];
            }
        }

//        $prompt = $this->_prompt;
//        $this->saveContentToDb($userId, $toUserId, $ret, $prompt, $msgClass, 0, $storyId, $this->model);

        return $ret;

    }

    public function generateGuessByGuestGame($userMessage, $params = []
//        , $oldMessages = []
//        , $aiRole = 'host'
    ) {
//        if ($aiRole == 'host') {
        $roleTxt = '#角色#' . "\n" . '你是一个描述猜物体的游戏主持人，你有很强的逻辑思考，也很有耐心，说话方式很温柔';
        $simple = [
            'analyze' => '思考过程',
            'content' => '答案',
        ];
        $extMessages = [
            '你根据输入的内容的理解，猜一个答案，可能是常见的物品、人物、文字、事物、活动等等',
            '内容不超过50字',
            '用JSON的形式返回',
            '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
        ];
//        } else {
//            $roleTxt = '#角色#' . "\n" . '你是一个小灵镜，负责出题和解答';
//            $simple = [
//                'content' => '回答问题',
//            ];
//            $extMessages = [
//                '内容不超过200字',
//                '用JSON的形式返回',
//                '#输出格式#' . json_encode($simple, JSON_UNESCAPED_UNICODE),
//            ];
//        }

        $userId = !empty($params['userId']) ? $params['userId'] : 0;
        $storyId = !empty($params['storyId']) ? $params['storyId'] : 0;
        $toUserId = !empty($params['toUserId']) ? $params['toUserId'] : 0;
        $toUserId = !empty($toUserId) ? $toUserId : $userId;
        $msgClass = GptContent::MSG_CLASS_GUESS_BY_GUEST;

//        $lastContents = Yii::$app->doubao->getContentsFromDb($userId, $userId, GptContent::MSG_CLASS_NORMAL, strtotime('-5 minute'), 0, 1);

//        $oldContents = $this->getOldContents($userId, $toUserId, $msgClass);

        $ret = $this->chatWithDoubao($userMessage, [], $extMessages, [$roleTxt], false);

        if (!empty($ret['answer'])
            && mb_strpos($ret['answer'], '你想的答案是') !== false
        ) {
            preg_match('/你想的答案是(.*)[。]?/', $ret['answer'], $matches);
            if (!empty($matches[1])) {
                $ret['answer'] = $matches[1];
            }
        }

        $prompt = $this->_prompt;
        $this->saveContentToDb($userId, $toUserId, $ret, $prompt, $msgClass, 0, $storyId, $this->model);

        return $ret;

    }

    public function generateDocScore($userMessage, $level = 0, $docTitle = '', $docDesc = '', $oldMessages = []) {
        $gradeName = $this->_getGradeNameFromLevel($level);

        $roleTxt = '#角色#' . "\n" . '你是一个语文方面精英教师，可以出作文题目，续写作文，给作文判分，标准按照小学毕业要求';
        $extMessages = [];
        $extMessages[] = '#任务描述和要求';
        $userMsgs = [];
        if (!empty($docTitle)) {
//            $extMessages[] = ['role' => 'assistant', 'content' => '作文题目：' . $docTitle];
            $userMsgs[] = '作文题目：' . $docTitle;
        }
        if (!empty($docDesc)) {
            $userMsgs[] = '作文要求：' . $docDesc;
//            $extMessages[] = ['role' => 'assistant', 'content' => '作文要求：' . $docDesc];
        }
        $extMessages[] = '请参照' . $gradeName . '的学生的平均水平';
        $extMessages[] = '针对作文打分';
        $extMessages[] = '并且根据作文题目和要求，指正出作文的优点和不足，分别3个';
        $extMessages[] = '所有数据以JSON返回';
//        $extMessages[] = '作文题目和要求，作文内容，作文续写建议，作文评分结果，用JSON的形式返回';
        $extMessages[] = '#输出格式#' . json_encode([
                'TITLE' => '作文标题',
                'DESC' => '作文要求',
                'SCORE' => '当前作文评分',
                'GOOD' => [
                    '作文优点1',
                    '作文优点2',
                    '作文优点3',
                ],
                'BAD' => [
                    '作文不足1',
                    '作文不足2',
                    '作文不足3',
                ],
            ], JSON_UNESCAPED_UNICODE);

        $ret = $this->chatWithDoubao($userMessage, $oldMessages, $extMessages, [$roleTxt]);

        return $ret;
    }
    public function generateDocTitles($level = 0, $desc = '', $ct = 4) {
        $gradeName = $this->_getGradeNameFromLevel($level);

        $roleTxt = '#角色' . "\n" . '你是一个语文方面精英教师，可以出作文题目，续写作文，给作文判分，标准按照小学毕业要求';

        $extMessages = [];
        $extMessages[] = '#任务描述和要求';
        $extMessages[] = '你需要出' . $ct . '个作文题目，每个题目不超过20字，符合' . $gradeName . '的学生的认知水平';
        $extMessages[] = '同时每个作文有对应的要求，不超过50字';
        $extMessages[] = '用JSON的形式返回';
        $extMessages[] = '#输出格式#' . json_encode([
                'TITLE' => '作文标题',
                'DESC' => '作文要求',
            ], JSON_UNESCAPED_UNICODE);

        if (!empty($desc)) {
            $userMessage = '要求符合' . $desc . '的作文题目';
        } else {
            $userMessage = '';
        }

        $ret = $this->chatWithDoubao($userMessage, [], $extMessages, [$roleTxt]);
        return $ret;

    }

    public function generateDoc($userMessage, $level = 0, $docTitle = '', $docDesc = '', $oldMessages = [], $userMsgExtend = '') {
        $gradeName = $this->_getGradeNameFromLevel($level);

        $roleTxt = '#角色' . "\n" . '你是一个语文方面精英教师，可以出作文题目，续写作文，给作文判分，标准按照小学毕业要求';
        $extMessages = [];
        $extMessages[] = '#任务描述和要求';
        $userMsgs = [];
        if (!empty($docTitle)) {
//            $extMessages[] = ['role' => 'assistant', 'content' => '作文题目：' . $docTitle];
            $userMsgs[] = '作文题目：' . $docTitle;
        }
        if (!empty($docDesc)) {
            $userMsgs[] = '作文要求：' . $docDesc;
//            $extMessages[] = ['role' => 'assistant', 'content' => '作文要求：' . $docDesc];
        }
        $extMessages[] = '作文符合' . $gradeName . '的学生的认知水平';
        $extMessages[] = '你根据作文题目和要求，续写作文，留出关键问题等待学生继续完成';
        $extMessages[] = '然后你再根据现在的作文，给出4条可以续写的建议，每个不超过20字';
        $extMessages[] = '然后你针对当前这篇作文的现状，进行评分，评分标准参考小学生毕业要求';
        $extMessages[] = '所有数据以JSON返回';
//        $extMessages[] = '作文题目和要求，作文内容，作文续写建议，作文评分结果，用JSON的形式返回';
        $extMessages[] = '#输出格式#' . json_encode([
                'TITLE' => '作文标题',
                'DESC' => '作文要求',
                'CONTENT' => '继续续写的作文内容',
                'SCORE' => '当前作文评分',
                'QUES' => [
                    '作文续写建议1',
                    '作文续写建议2',
                    '作文续写建议3',
                    '作文续写建议4',
                ],
            ], JSON_UNESCAPED_UNICODE);

        $userMessage = implode("\n", $userMsgs) . "\n" . $userMessage . "\n" . $userMsgExtend;

        $ret = $this->chatWithDoubao($userMessage, $oldMessages, $extMessages, [$roleTxt]);

        return $ret;
    }

    public function generateStory($userMessage, $level = 0, $storyTitle = '', $storyContent = '', $oldMessages = []) {

        $gradeName = $this->_getGradeNameFromLevel($level);

        if (empty($storyTitle) || empty($storyContent)) {
            $roleTxt = '#角色' . "\n" . '你扮演一个童话故事，历史典故，四大名著的资深学者，擅长讲故事';
            $extMessages = [
                '#任务描述和要求',
//                '你需要取出一段故事，并且翻译成白话文，故事要足够随机',
//                '故事范围《三国演义》《红楼梦》《水浒传》《西游记》《安徒生童话》《格林童话》《一千零一夜》《罗宾汉》《神奇校车》《哈利波特》《小王子》《白雪公主》《灰姑娘》《青蛙王子》《小红帽》《绿野仙踪》《狮子王》《美女与野兽》《海底两万里》《阿拉丁神灯》《拇指姑娘》',
//                '故事范围《三国演义》，从120回中抽取一段',
//                '你需要编写一个故事，可以是四大名著，世界名著，中国名著中的某个故事或者某个段落，也可以是你自己编写的故事，一定要随机选题，不要重复',
                '符合' . $gradeName . '小朋友认知的',
                '把故事讲述成白话文，并且中间人物要有对话，描写人物内心活动',
                '给出故事大纲，故事的题目，故事的内容，内容跌宕起伏，精彩纷呈，有人物、有对话、有剧情',
//                '但不要给出结局，而是写到一个悬念处',
                '并且再给出1个针对后来的问题，和2个基于当前的故事发展可能未来发展的走向或者问题，以及1个续写故事的话可能的剧情，每个不超过20字',
                '用JSON的形式返回',
                '#输出格式#' . json_encode([
                    'TITLE' => '故事标题',
                    'CONTENT' => '故事内容',
                    'FRAME' => '故事大纲',
                    'QUES' => [
                        '后来呢？',
                        '故事可能得走向或者问题2',
                        '故事可能得走向或者问题3',
                        '如果续写故事可能的剧情',
                    ],
                ], JSON_UNESCAPED_UNICODE),
            ];
        } else {
            $roleTxt = '#角色' . "\n" . '你是一个故事编写者，负责编写故事';
            $extMessages = [
                '#任务描述和要求',
                '你需要续写一个故事，故事的题目是：' . $storyTitle,
                '故事大纲是：' . $storyContent,
                '符合' . $gradeName . '小朋友认知的',
                '续写的故事请满足原故事人物特性和整体剧情发展，不要给出结局，而是写到一个悬念处',
                '并且再给出1个针对后来的问题，和3个基于当前的故事发展可能未来发展的走向或者问题，每个不超过20字',
                '用JSON的形式返回',
                '#输出格式#' . json_encode([
                    'TITLE' => '故事标题',
                    'CONTENT' => '故事内容',
                    'FRAME' => '故事大纲',
                    'QUES' => [
                        '后来呢？',
                        '故事可能得走向或者问题1',
                        '故事可能得走向或者问题2',
                        '故事可能得走向或者问题3',
                    ],
                ], JSON_UNESCAPED_UNICODE),
            ];
        }
        $ret = $this->chatWithDoubao($userMessage, $oldMessages, $extMessages, [$roleTxt]);
        return $ret;
    }

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
            $ret = $response = $this->chatWithDoubao($userMessagePre, [], [], $msgTemplate);
//            $messages = !empty($response['choices'][0]['message']['content']) ? $messages = $response['choices'][0]['message']['content'] : '';
//
//            if (!empty($messages)) {
//                file_put_contents('/tmp/test_doubao_msg.log', $messages);
//                $messages = str_replace('```json', '', $messages);
//                $messages = str_replace('```', '', $messages);
//                $ret = json_decode($messages, true);
//            }
//            var_dump($ret);exit;

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
        $messages = $response = $this->chatWithDoubao($userMessage, $oldMessages, $templateContents, $roleTxt);

//        $messages = !empty($response['choices'][0]['message']['content']) ? $messages = $response['choices'][0]['message']['content'] : '';

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
                '当前这一条引导信息的答案',
//                '可能还有的问题1',
                '可能还有的问题1',
                '可能还有的问题2',
                '可能还有的问题3',
//                '可能还有的问题4',
            ],
        ]);
        $roleMessages[] = '#角色' . "\n" . '你是一个教育方面的老师';
//        if (!empty($ques)) {
//            $roleMessages[] = '#特点' . "\n" . '你直来直往，会直接给出问题答案';
//        }
        $extMessages = [];
        $extMessages[] = '#任务描述和要求';
        if (empty($ques)) {
            $extMessages[] = '你根据题目内容，提供解题的思路引导，在2个步骤下可以解出题目，并且提示出第1个步骤，并且在QUESTIONS中第一条给出这个步骤的答案，学生可以在思考下完成解题';
            $extMessages[] = '利用引导式教学，引导学生思考，不要直接给出答案';
        } else {
            $extMessages[] = '根据题目内容和之前的引导，给出接下来的解题步骤，并且在QUESTIONS中的第一条给出这个步骤的答案';
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
        $extMessages = array_merge($extMessages, $roleMessagesFormat);
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

        $ret = $response = $this->chatWithDoubao($userMessage, $oldMessages, $extMessages, $roleMessages);

        file_put_contents('/tmp/test_doubao.log', var_export($roleMessages, true) . "\n\n\n" . var_export($response, true));
//        var_dump($response);exit;
//        $messages = $response['choices'][0]['message']['content'];

//        $messages = str_replace('```json', '', $messages);
//        $messages = str_replace('```', '', $messages);
//        $ret = json_decode($messages, true);

        return $ret;
    }

    public function chatWithDoubao($userMessage, $oldMessages = [], $templateContents = array(), $roleTxts = array(), $isJson = true) {

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
            $userMsg = array(
//            array('role' => 'system', 'content' => $roleTxt),
                array('role' => 'user', 'content' => $userMessage),
//                array('role' => 'user', 'content' => '请继续续写作文'),
            );
        } else {
            $userMsg = [];
        }

//        $templateMessages = array();
        if (!empty($templateContents)) {
            foreach ($templateContents as $templateContent) {
//                $templateMessages[] = array('role' => 'assistant', 'content' => $templateContent);
                $templateMessages[] = array('role' => 'system', 'content' => $templateContent);
            }
        }

        if (!empty($oldMessages)) {
//            array_unshift($oldMessages, ['role' => 'assistant', 'content' => '#历史消息']);
//            array_unshift($oldMessages, ['role' => 'system', 'content' => '#历史消息']);
//            foreach ($oldMessages as $oldMessage) {
//                $oldMessageArray[] = [
//                    'role' => 'assistant',
//                    'content' => $oldMessage,
//                    'prefix' => True,
//                ];
//            }
            $messages = array_merge($messages, $oldMessages);
//            $messages += $oldMessageArray;
        }
        $messages = array_merge($templateMessages, $messages);
//        $messages = array_merge($messages, $templateMessages);
        $messages = array_merge($messages, $userMsg);
//        var_dump($messages);exit;
        Yii::info('doubao messages: ' . json_encode($messages, JSON_UNESCAPED_UNICODE));
        file_put_contents('/tmp/test_doubao_i.log', var_export($messages, true));
        $this->_prompt = $messages;
//        if (!empty($oldMessages)) {
//            var_dump($messages);exit;
//        }
//        var_dump($messages);
//        exit;
//        print_r($messages);
//        exit;

        // Todo: 替换掉Doubao接口
//        $data = array(
////            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
//            'model' => 'ep-20240628070258-6m88j',
////            'model' => 'ep-20240729104951-snm9z',
//            'messages' => $messages,
//            'temperature' => 0.8,
////            'stream' => false,
//        );
        $data = array(
//            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
//            'model' => 'deepseek-chat',
            'model' => $this->model,
//            'model' => 'ep-20240729104951-snm9z',
            'messages' => $messages,
            'temperature' => $this->temperature,
//'prompt' => implode("\n", $templateContents),
//            'stream' => false,
//            "response_format" => [
//                'type' => 'json_object',
//            ],
        );
        if ($isJson) {
            $data['response_format'] = [
                'type' => 'json_object',
            ];
        }
//var_dump($data);exit;
//        Yii::info('chatGPT data: ' . json_encode($data));

        // Todo: 替换掉Doubao接口
//        $response = $this->_call('/v3/chat/completions', $data, 'POST');
        $response = $this->_call('/v1/chat/completions', $data, 'POST');
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

        $tmpRet =  json_decode($response, true);
        $msg = !empty($tmpRet['choices'][0]['message']['content']) ? $tmpRet['choices'][0]['message']['content'] : '';
        if (empty($msg)) {
            $msg = !empty($tmpRet['choices'][0]['text']) ? $tmpRet['choices'][0]['text'] : '';
        }
        if ($msg) {
//            $msg = $tmpRet['choices'][0]['message']['content'];
            if (!empty($msg)) {
                $msg = str_replace('```json', '', $msg);
                $msg = str_replace('```', '', $msg);
                // 过滤掉msg开头的\n
                $msg = preg_replace('/^\\n/', '', $msg);
                if (\common\helpers\Common::isJson($msg)) {
                    $ret = json_decode($msg, true);
                } else {
                    $ret = $msg;
                }
//                $ret = json_decode($msg, true);


            }
        } else {
            $ret = [];
        }

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

    public function saveContentToDb($userId, $toUserId, $content, $prompt = [], $msgClass = GptContent::MSG_CLASS_NORMAL, $senderId = 0, $storyId = 0, $gptModel = '', $isFirst = GptContent::IS_FIRST_UNKNOWN, $msgType = GptContent::MSG_TYPE_TEXT) {
        try {
            $model = new GptContent();
            $model->user_id = $userId;
            $model->to_user_id = $toUserId;
            $model->content = json_encode($content, JSON_UNESCAPED_UNICODE);
            $model->msg_type = $msgType;
            $model->msg_class = $msgClass;
            $model->gpt_model = $gptModel;
            $model->prompt = json_encode($prompt, JSON_UNESCAPED_UNICODE);
            $model->sender_id = $senderId;
            $model->story_id = $storyId;
            $model->is_first = $isFirst;
            $model->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getContentsFromDb($userId, $toUserId, $msgClass = GptContent::MSG_CLASS_NORMAL, $beginId = 0, $beginTime = 0, $endTime = 0, $limit = 20, $isFirst = GptContent::IS_FIRST_UNKNOWN, $msgType = GptContent::MSG_TYPE_TEXT, $senderId = 0, $storyId = 0) {
        $contentModel = GptContent::find()
            ->where([
                'user_id' => $userId,
            ]);
        if (!empty($msgClass)) {
            $contentModel->andFilterWhere(['msg_class' => $msgClass]);
        }
        if (!empty($toUserId)) {
            $contentModel->andFilterWhere(['to_user_id' => $toUserId]);
        }
        if (!empty($senderId)) {
            $contentModel->andFilterWhere(['sender_id' => $senderId]);
        }
        if (!empty($storyId)) {
            $contentModel->andFilterWhere(['story_id' => $storyId]);
        }
        if (!empty($beginId)) {
            $contentModel->andFilterWhere(['>', 'id', $beginId]);
        }
        if (!empty($beginTime)) {
            $contentModel->andFilterWhere(['>=', 'created_at', $beginTime]);
        }
        if (!empty($endTime)) {
            $contentModel->andFilterWhere(['<=', 'created_at', $endTime]);
        }
        if (!empty($isFirst)) {
            $contentModel->andFilterWhere(['is_first' => $isFirst]);
        }
        $contentModel->orderBy('id desc');
        if ($limit > 0) {
            $contentModel->limit($limit);
        }
        $contents = $contentModel->all();

        return $contents;
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
        $url = $this->host . $interface;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        );

//        var_dump($params);
//        exit;
//        var_dump($url);
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