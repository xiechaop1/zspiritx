<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\matchh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\GptContent;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Poem;
use common\models\ShopWares;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryRank;
use common\models\User;
use common\models\UserExtends;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use common\models\UserScore;
use common\models\UserWare;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Puzzle extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;
        $type = !empty($_GET['type']) ? $_GET['type'] : 0;

//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;

        $qa = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        if (!empty($qa)) {
            $qa = $qa->toArray();
            $qa['selected_json'] = \common\helpers\Common::isJson($qa['selected']) ? json_decode($qa['selected'], true) : $qa['selected'];
        }

        $user = User::find()
            ->where([
                'id'    => $userId,
            ])
            ->one();

        if (!empty($user['avatar'])) {
            $user['avatar'] = Attachment::completeUrl($user['avatar']);
        } else {
            $user['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
        }

        if (empty($user)) {
            return $this->renderErr('用户不存在！');
        }

        $userExtends = UserExtends::find()
            ->where([
                'user_id'   => $userId,
            ])
            ->one();

        $level = !empty($userExtends['level']) ? $userExtends['level'] : 11;

        $userScore = Yii::$app->score->get($userId, $storyId, 0);

//        $genStory = Yii::$app->doubao->generateStory('故事是榜文行到涿县，引出涿县中一个英雄。那人不甚好读书；性宽和，寡言语，喜怒不形于色；素有大志，专好结交天下豪杰；生得身长七尺五寸，两耳垂肩，双手过膝，目能自顾其耳，面如冠玉，唇若涂脂；中山靖王刘胜之后，汉景帝阁下玄孙，姓刘名备，字玄德。昔刘胜之子刘贞，汉武时封涿鹿亭侯，后坐酎金失侯，因此遗这一枝在涿县。玄德祖刘雄，父刘弘。弘曾举孝廉，亦尝作吏，早丧。玄德幼孤，事母至孝；家贫，贩屦织席为业。家住本县楼桑村。其家之东南，有一大桑树，高五丈余，遥望之，童童如车盖。相者云：“此家必出贵人。”玄德幼时，与乡中小儿戏于树下，曰：“我为天子，当乘此车盖。”叔父刘元起奇其言，曰：“此儿非常人也！”因见玄德家贫，常资给之。年十五岁，母使游学，尝师事郑玄、卢植，与公孙瓒等为友。
//
//及刘焉发榜招军时，玄德年已二十八岁矣。当日见了榜文，慨然长叹。随后一人厉声言曰：“大丈夫不与国家出力，何故长叹？”玄德回视其人，身长八尺，豹头环眼，燕颔虎须，声若巨雷，势如奔马。玄德见他形貌异常，问其姓名。其人曰：“某姓张名飞，字翼德。世居涿郡，颇有庄田，卖酒屠猪，专好结交天下豪杰。恰才见公看榜而叹，故此相问。”玄德曰：“我本汉室宗亲，姓刘，名备。今闻黄巾倡乱，有志欲破贼安民，恨力不能，故长叹耳。”飞曰：“吾颇有资财，当招募乡勇，与公同举大事，如何。”玄德甚喜，遂与同入村店中饮酒。
//
//正饮间，见一大汉，推着一辆车子，到店门首歇了，入店坐下，便唤酒保：“快斟酒来吃，我待赶入城去投军。”玄德看其人：身长九尺，髯长二尺；面如重枣，唇若涂脂；丹凤眼，卧蚕眉，相貌堂堂，威风凛凛。玄德就邀他同坐，叩其姓名。其人曰：“吾姓关名羽，字长生，后改云长，河东解良人也。因本处势豪倚势凌人，被吾杀了，逃难江湖，五六年矣。今闻此处招军破贼，特来应募。”玄德遂以己志告之，云长大喜。同到张飞庄上，共议大事。飞曰：“吾庄后有一桃园，花开正盛；明日当于园中祭告天地，我三人结为兄弟，协力同心，然后可图大事。”玄德、云长齐声应曰：“如此甚好。”
//
//次日，于桃园中，备下乌牛白马祭礼等项，三人焚香再拜而说誓曰：“念刘备、关羽、张飞，虽然异姓，既结为兄弟，则同心协力，救困扶危；上报国家，下安黎庶。不求同年同月同日生，只愿同年同月同日死。皇天后土，实鉴此心，背义忘恩，天人共戮！”誓毕，拜玄德为兄，关羽次之，张飞为弟。祭罢天地，复宰牛设酒，聚乡中勇士，得三百余人，就桃园中痛饮一醉。来日收拾军器，但恨无马匹可乘。正思虑间，人报有两个客人，引一伙伴当，赶一群马，投庄上来。玄德曰：“此天佑我也！”三人出庄迎接。原来二客乃中山大商：一名张世平，一名苏双，每年往北贩马，近因寇发而回。玄德请二人到庄，置酒管待，诉说欲讨贼安民之意。二客大喜，愿将良马五十匹相送；又赠金银五百两，镔铁一千斤，以资器用。
//
//玄德谢别二客，便命良匠打造双股剑。云长造青龙偃月刀，又名“冷艳锯”，重八十二斤。张飞造丈八点钢矛。各置全身铠甲。共聚乡勇五百余人，来见邹靖。邹靖引见太守刘焉。三人参见毕，各通姓名。玄德说起宗派，刘焉大喜，遂认玄德为侄。不数日，人报黄巾贼将程远志统兵五万来犯涿郡。刘焉令邹靖引玄德等三人，统兵五百，前去破敌。玄德等欣然领军前进，直至大兴山下，与贼相见。贼众皆披发，以黄巾抹额。当下两军相对，玄德出马，左有云长，右有翼德，扬鞭大骂：“反国逆贼，何不早降！”程远志大怒，遣副将邓茂出战。张飞挺丈八蛇矛直出，手起处，刺中邓茂心窝，翻身落马。程远志见折了邓茂，拍马舞刀，直取张飞。云长舞动大刀，纵马飞迎。程远志见了，早吃一惊，措手不及，被云长刀起处，挥为两段。后人有诗赞二人曰：英雄露颖在今朝，一试矛兮一试刀。初出便将威力展，三分好把姓名标。', $level);
//        $genStory = Yii::$app->doubao->generateStory('适合6-8岁小朋友阅读的故事，随机出5个', $level);
        $old = [];
        $old[] = [
            'role' => 'assistant',
            'content' => '春天来了，空气中弥漫着淡淡的花香，仿佛大地在一夜之间换上了新装。树枝上嫩绿的芽儿探出头来，好奇地打量着这个世界。小河边的柳树垂下了细长的枝条，像少女的长发在微风中轻轻摇曳。鸟儿在枝头欢快地歌唱，仿佛在庆祝春天的到来。田野里，农民伯伯已经开始忙碌起来，播种、施肥，期待着秋天的丰收。 ',
//            'prefix' => True,
        ];
        $old[] = [
            'role' => 'user',
            'content' => '然而，春天不仅仅是大自然的复苏，它也象征着新的开始。每当春天来临，我总会感到一股莫名的力量在心中涌动，激励我向前迈进。春天，是希望的季节，是梦想的起点。',
//            'prefix' => True,
        ];
        $old[] = [
            'role' => 'assistant',
            'content' => '然而，春天也有它独特的美中不足。虽然万物复苏，但有时也会带来一些不便。例如，春天的雨水虽然滋润了大地，但也可能导致道路泥泞，出行不便。',
//            'prefix' => True,
        ];
        $old = [];

        $userTxt = '但是我依然很喜欢春天！因为春天我就可以出门和小朋友一起玩耍了，我们跑上跑下，玩的不亦乐乎。';
//        $genStory = Yii::$app->doubao->generateDoc($userTxt, $level, '春来到', '春天来了，万物复苏，你眼中的春天是什么样的？请写一篇记叙文',
//            $old);
//        var_dump($genStory);exit;
//var_dump(date('Y-m-d H:i:s', time()));
//var_dump($old);
//echo "<br>";
//echo !empty($genStory['CONTENT']) ? $genStory['CONTENT'] : '';
//if (!is_array($genStory)) {
//    echo $genStory;
//} else {
//    var_dump($genStory);
//}
//        exit;
        $genStory = '';
        switch ($type) {
            case GptContent::MSG_CLASS_GUESS_BY_DESCRIPTION:
                $params = [
                    'userId' => $userId,
                    'storyId' => $storyId,
                    'toUserId' => $userId,
                ];
                $genStory = Yii::$app->doubao->generateGuessByDescGame('描述猜物体', $params);
                break;
            case GptContent::MSG_CLASS_GUESS_BY_GUEST:
//                $params = [
//                    'userId' => $userId,
//                    'storyId' => $storyId,
//                    'toUserId' => $userId,
//                ];
//                $genStory = Yii::$app->doubao->generateGuessByGuestGame('', $params);
                $genStory = [
                    'content' => '你来描述一个，我来猜！',
                    'answer' => '',
                ];
                break;
            default:
                break;
        }
//        var_dump($genStory);exit;

        return $this->controller->render('puzzle', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'qa'            => $qa,
            'rtnAnswerType' => 2,
            'type' => $type,
            'initTimer' => 60,
            'user' => $user,
            'userScore' => $userScore,
            'level' => $level,
            'genStory' => $genStory,
        ]);
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}