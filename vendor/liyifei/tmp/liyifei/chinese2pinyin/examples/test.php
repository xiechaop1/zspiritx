<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 17-8-15
 * Time: 上午10:26
 */
require_once(dirname(__FILE__) . '/../vendor/yiisoft/yii2/Yii.php');
require_once(dirname(__FILE__) . '/../vendor/autoload.php');
@(Yii::$app->charset = 'UTF-8');


$converter = new \liyifei\chinese2pinyin\Chinese2pinyin();


//var_dump( $converter->transformUcWords('李奕飞', '||') );
var_dump($converter->transformUcWords('☀你好世界', '', false));
var_dump($converter->transformWithTone('☀你好世界', ' ', false));