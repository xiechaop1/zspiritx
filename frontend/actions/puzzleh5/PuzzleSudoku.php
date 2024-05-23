<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\puzzleh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\Order;
use common\models\Story;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class PuzzleSudoku extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $size = !empty($_GET['size']) ? $_GET['size'] : 0;
        $hole = !empty($_GET['hole']) ? $_GET['hole'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        if (empty($qaId)) {
            $qaId = Net::get('id');
        }

        $qaOne = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        if (empty($qaOne)) {
            throw new NotFoundHttpException('QA not found');
        }
        $qaOne = $qaOne->toArray();
        $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
        $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);

        if (empty($hole) && !empty($qaOne['selected_json']['hole'])) {
            $hole = $qaOne['selected_json']['hole'];
        }
        if (empty($size) && !empty($qaOne['selected_json']['size'])) {
            $size = $qaOne['selected_json']['size'];
        }

        $keyboardArray = [];
        if (!empty($qaOne['selected_json']['keyboard'])) {
            $keyboard = $qaOne['selected_json']['keyboard'];
            if (strpos($keyboard, '|') !== false) {
                $keyboardArrayTmp = explode('|', $keyboard);
            } else {
                $keyboardArrayTmp = str_split($keyboard);
            }
            foreach ($keyboardArrayTmp as $keyVal) {
                $keyboardArray[$keyVal] = $keyVal;
            }
            $keyboardArray['←'] = 'DELETE';
        }

        $size = !empty($qaOne['selected_json']['size']) ? $qaOne['selected_json']['size'] : $size;

        $retry = 1;
        $ct = 0;
        while ($retry == 1) {
            $retry = 0;
            for ($i = 0; $i < $size; $i++) {
                for ($j = 0; $j < $size; $j++) {
                    $whiteList['area'][$i][] = $j + 1;
                    $whiteList['row'][$i][] = $j + 1;
                    $whiteList['col'][$i][] = $j + 1;
                }
            }


            for ($i = 0; $i < $size; $i++) {
                for ($j = 0; $j < $size; $j++) {
                    $currWhiteList = array_intersect($whiteList['row'][$i], $whiteList['col'][$j]);
                    if ($size == 9) {
                        $currWhiteList = array_intersect($currWhiteList, $whiteList['area'][floor($i / 3) * 3 + floor($j / 3)]);
                    }
                    if (empty($currWhiteList)) {
                        $retry = 1;
                        break;
                    }
                    $numPos = array_rand($currWhiteList);

                    $num = $currWhiteList[$numPos];
//                exit;
                    $numList[$i][$j] = $num;
                    $whiteList['row'][$i] = array_diff($whiteList['row'][$i], [$num]);
                    $whiteList['col'][$j] = array_diff($whiteList['col'][$j], [$num]);
                    if ($size == 9) {
                        $whiteList['area'][floor($i / 3) * 3 + floor($j / 3)] = array_diff($whiteList['area'][floor($i / 3) * 3 + floor($j / 3)], [$num]);
                    }
                }
            }
//            echo $ct++;
        }

        // $numList 输出成9*9的数组
//        for ($i=0; $i<$size; $i++) {
//            for ($j=0; $j<$size; $j++) {
//                echo $numList[$i][$j] . ' ';
//            }
//            echo '<br>';
//        }
//        exit;

        // 挖坑
        // 根据难度不同，挖去的个数不同
        for ($i = 0; $i < $hole; $i++) {
            $x = rand(0, $size - 1);
            $y = rand(0, $size - 1);
            if ($numList[$x][$y] == 0) {
                $i--;
                continue;
            } else {
                $numList[$x][$y] = 0;
            }
        }
//var_dump($numList);
//        exit;

        return $this->controller->render('puzzle_sudoku', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'iList'      => $numList,
            'size'          => $size,
            'qaId'          => $qaId,
            'storyId'       => $qaOne['story_id'],
            'rightAnswer'   => $qaOne['st_answer'],
            'keyboardArray'    => $keyboardArray,
            'qa'         => $qaOne,
        ]);
    }

}