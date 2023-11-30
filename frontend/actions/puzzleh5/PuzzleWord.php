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

class PuzzleWord extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $rows = !empty($_GET['rows']) ? $_GET['rows'] : 0;
        $cols = !empty($_GET['cols']) ? $_GET['cols'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        $qaOne = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        $qaOne = $qaOne->toArray();
        $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
        $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);

        $str = $qaOne['selected_json'];
        $str = str_replace(chr(13), '', $str);
        $str = str_replace(chr(10), '', $str);
        $str = str_replace($qaOne['st_selected'], '', $str);
        $strLen = mb_strlen($str, 'utf-8');
        $iList = [];
        for ($i=0; $i<$strLen; $i++) {
            $iList[] = ['word' => mb_substr($str, $i, 1, 'utf-8'), 'val' => 0];
        }

        $ansStrLen = mb_strlen($qaOne['st_selected'], 'utf-8');
        for ($i=0; $i<$ansStrLen; $i++) {
            $iList[] = ['word' => mb_substr($qaOne['st_selected'], $i, 1, 'utf-8'), 'val' => 1];
        }
        shuffle($iList);

        $totalWidth = 550;
        $totalHeight = 400;


        return $this->controller->render('puzzle_word', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'rows'          => $rows,
            'cols'          => $cols,
            'width'         => intval($totalWidth/$cols),
            'height'        => intval($totalHeight/$rows),
            'iList'         => $iList,
            'rightAnswer'   => $qaOne['st_answer'],
            'qa'         => $qaOne,
        ]);
    }
}