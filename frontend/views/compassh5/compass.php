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

\frontend\assets\Compassh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = $qa['topic'];

?>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="team_id" value="<?= $teamId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="story_stage_id" value="<?= $storyStageId ?>">
<input type="hidden" name="user_lng" value="<?= $userLng ?>">
<input type="hidden" name="user_lat" value="<?= $userLat ?>">
<input type="hidden" name="dis_range" value="<?= $disRange ?>">

<div id="compass"></div>




