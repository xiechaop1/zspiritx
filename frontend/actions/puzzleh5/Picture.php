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

class Picture extends Action
{

    
    public function run()
    {

        $image = 'img/home/index_image.jpg';
        $image = Attachment::completeUrl($image, false);

        $blockSize = 200;
        $rows = 4;
        $cols = 4;
        $pictures = \common\helpers\Puzzle::cutImage($image, $blockSize, $rows, $cols);
//
        $picId = !empty($_GET['pic_id']) ? $_GET['pic_id'] : 0;
//
//        $gameImage = imagecreatetruecolor($blockSize * $cols, $blockSize * $rows);
//        $gameImage = imagecreatetruecolor($blockSize, $blockSize);
//
//        $block = 0;
//        shuffle($pictures);
//        for ($x = 0; $x < $rows; $x++) {
//            for ($y = 0; $y < $cols; $y++) {
//                imagecopy($gameImage, $pictures[$block]['image'], $x * $blockSize, $y * $blockSize, 0, 0, $blockSize, $blockSize);
//                $block++;
//            }
//        }
//        imagecopy($gameImage, $pictures[$picId]['image'], 0, 0, 0, 0, $blockSize, $blockSize);

        header('Content-Type: image/jpeg');
        imagejpeg($pictures[$picId]);

//        \common\helpers\Common::createPuzzle($image, 200, 4, 4,100);
    }
}