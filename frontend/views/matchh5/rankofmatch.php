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

\frontend\assets\Qah5Asset::register($this);

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
    td {font-size: 18px;}
</style>
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex" style="color: white;">
                <hr>
                <table style="width: 100%; margin: 3px; color: white;">
                    <tr>
                        <td colspan="7"> </td>
                        <td colspan="3" style="text-align: center; font-weight: bold;">最佳</td>
                    </tr>
                    <tr style="border-bottom: 3px white solid;">
                        <td style="font-weight: bold;">排名</td>
                        <td style="font-weight: bold;">参赛者</td>
                        <td style="font-weight: bold;">赛车</td>
                        <td style="font-weight: bold;">级别</td>
                        <td style="font-weight: bold;">圈数</td>
                        <td style="font-weight: bold;">总时长</td>
                        <td style="font-weight: bold;">差距</td>
                        <td>圈数</td>
                        <td>时长</td>
                        <td>速度</td>
                    </tr>
                    <tr>
                        <td style="height: 4px;"> </td>
                    </tr>
                    <tr>
                        <td style="background-color: red; text-align: left; padding-left: 10px;" colspan="5">顶级组</td>
                        <td style="background-color: red" colspan="5"></td>
                    </tr>
                    <?php
                    if (!empty($rankList)) {
                        $firstScore = 0;
                        foreach ($rankList as $rank => $r) {

                            $scoreArr = \common\helpers\Common::formatTime($r->score2);
                            if ($firstScore == 0) {
                                $firstScore = $r->score;
                            }
                            $scoreGap = $r->score - $firstScore;
//                            var_dump($r);
                            $matchDetail = json_decode($r->match_detail, true);
//                            var_dump($matchDetail);exit;

                            $trStyle = '';
                            if ($r->user_id == $userId) {
                                $trStyle = 'color: #FFCC00;';
                            }
                            if ($r->session_id == $sessionId && $r->user_id == $userId) {
                                $trStyle = 'color: #FFFF10; font-weight: bold;';
                            }

?>
                            <tr<?= !empty($trStyle) ? ' style="' . $trStyle . '"' : '' ?>>
                                <td style="font-weight: bold;"><?= $rank ?></td>
                                <td style="font-weight: bold;"><?= $r->user->user_name ?></td>
                                <td>
                                    <?php
                                    $carLogo = '';
                                    if (strpos($r->storyModel->story_model_name, '奥迪') !== false) {
                                        $carLogo = 'audi.png';
                                    } elseif (strpos($r->storyModel->story_model_name, '奔驰') !== false) {
                                        $carLogo = 'mecerdes.png';
                                    } elseif (strpos($r->storyModel->story_model_name, '福特') !== false) {
                                        $carLogo = 'ford.png';
                                    }
                                    ?>
                                    <?php
                                    if (!empty($carLogo)) {
                                        $logoUrl = '/story_model/visual/11/carlogo/' . $carLogo;
                                    ?>
                                    <img src="<?= \common\helpers\Attachment::completeUrl($logoUrl) ?>" style="width: 36px; border-radius: 50%;">
                                        <?php
                                    }
                                        ?>
                                    <?= $r->storyModel->story_model_name ?></td>
                                <td>顶级组</td>
                                <td style="font-weight: bold;"><?= $r->score ?></td>
                                <td><?= $scoreArr['str'] ?></td>
                                <td><?= $scoreGap ?></td>
                                <td><?= !empty($matchDetail['best_time_lop']) ? $matchDetail['best_time_lop'] : '-' ?></td>
                                <td style="font-weight: bold;"><?= !empty($matchDetail['best_time']) ? $matchDetail['best_time'] : '-' ?></td>
                                <td><?= !empty($matchDetail['max_speed']) ? $matchDetail['max_speed'] : '-' ?>公里/小时</td>
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
