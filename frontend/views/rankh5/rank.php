<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 3:14 PM
 */

/**
 * @var \yii\web\View $this ;
 */

/**
 * @var \common\models\QA $qa
 */

use common\models\StoryRank;

\frontend\assets\Marginh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '消息';

?>
<style>
    td {font-size: 24px;}

</style>
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30" style="position: absolute; right: 5px; top: -60px;" id="return_btn">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
                       排行榜
                    </div>
                </div>
            </div>
            <div style="clear:both; color: white; font-size: 24px; font-weight: bold; margin-top: 30px; padding-bottom: 15px; border-bottom: 1px #fff solid;">
                <?php
                if (!empty($rankCfgList)) {
                    foreach ($rankCfgList as $rClass => $rankCfg) {
                        if ($rClass == $rankClass) {
                            $style = 'border-bottom: 4px #DAFC70 solid; color: #DAFC70';
                        } else {
                            $style = 'color: #fff';
                        }
                        ?>
                        <a href="/rankh5/rank?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&rank_class=<?= $rClass?>">
                        <span style="<?= $style ?>; padding: 15px; ">
                            <?= !empty(StoryRank::$storyRankClass2Name[$rClass])
                                ? StoryRank::$storyRankClass2Name[$rClass] : '未知' ?>
                        </span>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="w-1-0 d-flex" style="color: white; margin-top: 30px; ">

                <table style="width: 100%; margin: 3px; color: white;">
                    <tr style="border-bottom: 3px white solid;">
                        <td style="font-weight: bold;">排名</td>
                        <td style="font-weight: bold;">参赛者</td>
                        <?php
                        if (!empty($rankConfig['storyModel']['name'])) {
                            ?>
                            <td style="font-weight: bold;"><?= !empty($rankConfig['storyModel']['name']) ? $rankConfig['storyModel']['name'] : '物品' ?></td>
                            <?php
                        }
                        ?>
                        <td style="font-weight: bold;"><?= !empty($rankConfig['score']['name']) ? $rankConfig['score']['name'] : '成绩' ?></td>
                        <?php
                        if (!empty($rankConfig['score2']['name'])) {
                        ?>
                        <td style="font-weight: bold;"><?= !empty($rankConfig['score2']['name']) ? $rankConfig['score2']['name'] : '副成绩' ?></td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td style="height: 4px;"> </td>
                    </tr>
                    <?php
                    if (!empty($rankList)) {
                        $firstScore = 0;
                        foreach ($rankList as $rank => $r) {

                            if ($firstScore == 0) {
                                $firstScore = $r->score;
                            }
                            $scoreGap = $r->score - $firstScore;

                            $trStyle = '';
                            if ($r->user_id == $userId) {
                                $trStyle = 'color: #FFD700;';
                            }
?>
                            <tr<?= !empty($trStyle) ? ' style="'. $trStyle . '"' : '' ?>>
                                <td style="font-weight: bold;"><?= $rank ?></td>
                                <td style="font-weight: bold;"><?= $r->user->user_name ?></td>
                                <?php
                                if (!empty($rankConfig['storyModel']['name'])) {
                                    ?>
                                    <td><?php
                                        if (!empty($rankConfig['storyModel']['format'])) {
                                            eval('$tmp = ' . sprintf($rankConfig['score2']['format'], $r->storyModel->story_model_name) . '; ');
                                            echo $tmp;
                                        } else {
                                            echo $r->storyModel->story_model_name;
                                        } ?></td>
                                    <?php
                                }
                                ?>
                                <td style="font-weight: bold;"><?php
                                    if (!empty($rankConfig['score']['format'])) {
                                        eval('$tmp = ' . sprintf($rankConfig['score']['format'], $r->score) . ';');
                                        echo $tmp;
                                    } else {
                                        echo $r->score;
                                    } ?></td>
                            <?php
                            if (!empty($rankConfig['score2']['name'])) {
                                ?>
                                <td><?php
                                    if (!empty($rankConfig['score2']['format'])) {
                                        eval('$tmp = ' . sprintf($rankConfig['score2']['format'], $r->score2) . ';');
                                        echo $tmp;
                                    } else {
                                        echo $r->score2;
                                    } ?></td>
                                <?php
                            }
                                ?>
                            </tr>
                            <?php

                        }
                    }
                    ?>
                </table>
<!--                <div class="fs-30 bold w-100 text-FF title-box-border">-->
<!--                    <div class="npc-name">-->
<!--                        消息-->
<!--                    </div>-->
<!--                    c-->
<!--                </div>-->
            </div>
<!--            <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn">-->
<!--                返回-->
<!--            </div>-->
        </div>
        </div>

    </div>

</div>
