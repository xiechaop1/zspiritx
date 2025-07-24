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

\frontend\assets\Marginh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '我的';

?>
<style>
    a {color: yellow}
</style>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<div class="w-100 m-auto">
    <div class="btn-m-green m-t-30  m-l-30" id="return_btn">
         退出
    </div>
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 100px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        我的(<?= !empty($user->user_name) ? $user->user_name : $userId ?>)
                    </div>

                    <div class="row" id="answer-box">
                        <div class="m-t-30 col-sm-12 col-md-12">
                            <div class="answer-border">
                                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                                <label class="form-check-label fs-30 answer-btn">
                            <span class="answer-tag">
                              <img src="../../static/img/my/task.png" class="list-icon-s"/>
                            </span>
                                    <span style="padding-left: 90px; "><a href="/myh5/settings?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>">设置</a></span>
                                </label>
                            </div>
                        </div>
                    </div>

            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label class="form-check-label fs-30 answer-btn">
                            <span class="answer-tag">
                              <img src="../../static/img/my/task.png" class="list-icon-s"/>
                            </span>
                            <span style="padding-left: 90px; "><a href="/knowledgeh5/all?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&knowledge_class_id=<?= \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION ?>">任务</a></span>
                            <?= $userKnowledge[\common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION] == 1 ? '<img src="../../static/img/qa/unread.png" style="margin-left: 15px;"></img>' : '' ?>
                    </label>
                     </div>
                </div>

            </div>
                    <div class="row" id="answer-box">
                        <div class="m-t-30 col-sm-12 col-md-12">
                            <div class="answer-border">
                                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                                <label class="form-check-label fs-30 answer-btn">
                                    <span class="answer-tag">
                                        <img src="../../static/img/my/know.png" class="list-icon-s"/>
                                    </span>
                                    <span style="padding-left: 90px; "><a href="/knowledgeh5/all?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&knowledge_class_id=<?= \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL ?>">知识</a></span>
                                    <?= $userKnowledge[\common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL] == 1 ? '<img src="../../static/img/qa/unread.png" style="margin-left: 15px;"></img>' : '' ?>
                                </label>

                            </div>
                        </div>

                    </div>
                    <div class="row" id="answer-box">
                        <div class="m-t-30 col-sm-12 col-md-12">
                            <div class="answer-border">
                                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                                <label class="form-check-label fs-30 answer-btn">
                                    <span class="answer-tag">
                                        <img src="../../static/img/my/task.png" class="list-icon-s"/>
                                    </span>
                                    <span style="padding-left: 90px; "><a href="/rankh5/rank?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>">排行榜</a></span>
                                </label>

                            </div>
                        </div>

                    </div>
                    <div class="row" id="answer-box">
                        <div class="m-t-30 col-sm-12 col-md-12">
                            <div class="answer-border">
                                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                                <label class="form-check-label fs-30 answer-btn">
                                    <span class="answer-tag">
                                        <img src="../../static/img/my/task.png" class="list-icon-s"/>
                                    </span>
                                    <span style="padding-left: 90px; "><a href="javascript:void(0);" onclick="goHome()">返回主厅</a></span>
                                </label>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        </div>

    </div>

</div>
<script>
    function goHome() {
        var userId = <?= $userId ?>;
        var params = {
            'WebViewOff': 1,
            'UserId': userId,
            'StoryId': <?= $defStoryId ?>
        }
        var data = $.toJSON(params);
        console.log(data);
        Unity.call(data);


    }
</script>

