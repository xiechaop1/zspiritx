<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\processh5;


use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\models\SessionModels;
use common\models\StoryModels;
use common\models\UserModels;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Guide extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $page = !empty($this->_get['page']) ? $this->_get['page'] : 1;

        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $txt[3] = [
            [
                'title' => '游戏介绍',
                'content' => '欢迎您来到"大峡谷"体验"侏罗纪之时间裂缝"，在这里您将体验到一场穿越时空的冒险之旅。我来简单介绍一下游戏规则吧！',
                'image' => '',
            ],
            [
                'title' => '游戏介绍',
                'content' => '首先建议您<span style="color:red; font-weight: bold;">打开手机声音</span>，有背景音乐，还有对白，可以辅助您游戏',
                'image' => '',
            ],
            [
                'title' => '游戏介绍',
                'content' => '进入游戏，左侧是工具栏，其中"地图"是您的位置；"背包"有您从地上拾取的物品；"我的"里有"任务"和"知识"，是您要进行的额任务，和获取的知识（有些剧本可能没有知识）',
                'image' => 'img/guide/3/3-1.png',
            ],
            [
                'title' => '游戏介绍',
                'content' => '右侧是游戏画面，下面有辅助栏，包括：当前的任务和物品/NPC距您的距离',
                'image' => 'img/guide/3/4-1.png',
            ],
            [
                'title' => '游戏介绍-发现',
                'content' => '当您所处一个场景中，请您按照线索仔细寻找，当出现提示<font color="red"><b>"正在扫描构建环境，请使用手机缓慢扫描地面"</b></font>的时候，请您将手机对准地面或者平面，让物品/NPC有个落脚点',
                'image' => 'img/guide/3/5-1.png',
            ],
            [
                'title' => '游戏介绍-发现',
                'content' => '当您看到<font color="red"><b>"周围发生了变化，在四周找找看吧"</b></font>，说明已经有NPC或者物品出现了，请您用手机环顾四周，可以找到物品/NPC，并继续游戏',
                'image' => 'img/guide/3/lookforaround.png',
            ],
            [
                'title' => '游戏介绍-对话',
                'content' => '出现的物品可以点击拾取，出现的NPC可以对话，并且指引您继续进行游戏',
                'image' => 'img/guide/3/7-1.png',
            ],
            [
                'title' => '游戏介绍-扫描',
                'content' => '游戏中会给您线索进行任务，需要您仔细分析和寻找，当您已经在对应位置但是没有物品/NPC出现的时候，建议您对准<font color="red"><b>"说明牌"，并离得近一些，停留一点时间</b></font>，或者从"任务"中查看扫描的位置，尝试让物品/NPC出现',
                'image' => 'img/guide/3/8-1.png',
            ],
            [
                'title' => '游戏介绍-投喂',
                'content' => '当您需要投喂恐龙的时候，您先和恐龙对话，看到<span style="font-weight: bold; color: red;">屏幕顶部有恐龙的名字</span>，然后打开<span style="font-weight: bold; color: red;">"背包"</span>找到对应的食物，并点击食物可以投喂，如果是恐龙爱吃的，那么他将会给您新的指引。',
                'image' => 'img/guide/3/9-1.png',
            ],
            [
                'title' => '游戏介绍-答题',
                'content' => '请您仔细看题，回答题目，如果有些题目较难，建议您尝试仔细观察恐龙和您的<span style="color:red; font-weight: bold;">对话</span>，或者恐龙旁边的<span style="color:red; font-weight: bold;">"说明牌"</span>，或者也可以<span style="color:red; font-weight: bold;">百度</span>，去寻找答案',
                'image' => 'img/guide/3/10-1.png',
            ],
            [
                'title' => '游戏介绍-答题',
                'content' => '当您题目做对以后，别忘记<span style="color:red; font-weight: bold;">收集宝石</span>，来补上时间裂缝',
                'image' => 'img/guide/3/11-1.png',
            ],
            [
                'title' => '游戏介绍-QA',
                'content' => '如果您的物品/NPC无法识别出现，或者物品拾取不到等任何问题，您都可以<span style="color:red; font-weight: bold;">杀掉进程，重新进入</span>，我们会在1小时内<span style="color:red; font-weight: bold;">保留您之前场景的结果</span>，您从当前场景重新游戏即可',
                'image' => '',
            ],
            [
                'title' => '游戏介绍-QA',
                'content' => '如果您有任何问题，欢迎<span style="color:red; font-weight: bold;">您和管理员联系：18500041193（可以加微信）</span>。最后祝您玩儿的开心！灵镜新世界，感谢您的到来！',
                'image' => 'img/guide/3/13-1.png',
            ],
        ];

        if (empty($storyId) || empty($txt[$storyId])) {
            return $this->controller->render('guide', [
                'params'      => $_GET,
                'page'        => $page,
                'storyId'       => $storyId,
                'pageCount'     => 0,
                'content'           => '',
            ]);
        }

        if (empty($page) || $page < 1) {
            $page = 1;
        }
        if ($page > sizeof($txt[$storyId])) {
            $page = sizeof($txt[$storyId]);
        }

        foreach ($txt as $storyId => &$contents) {
            foreach ($contents as &$content) {
                if (!empty($content['image'])) {
                    $content['image'] = Attachment::completeUrl($content['image'], true);
                }
            }
        }


        return $this->controller->render('guide', [
            'params'      => $_GET,
            'page'        => $page,
            'pageCount'   => sizeof($txt[$storyId]),
            'storyId'       => $storyId,
            'content'           => $txt[$storyId][$page],
        ]);

    }

}