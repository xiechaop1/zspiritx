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
    select {
        width: 300px;
        height: 60px;
        font-size: 32px;
        color: #000;
        border: 1px solid #000;
        border-radius: 15px;
        background-color: #fff;

    }
    .set {
        clear: both;
    }
    .school {
        clear: both;
        border-bottom: 2px solid #80ff00;
        margin: 25px;
        text-align: center;
        color: #80ff00;
    }

    .grade {
        float: left;
        width: 200px;
        margin: 10px;
    }
    .grade_options {
        border: 1px solid #80ff00;
        border-radius: 10px;
        background-color: rgba(64,128,0,0.2);
    }
    .curr {
        font-weight: bold;
        color: #3a8104;
    }
    .change {
        font-weight: normal;
        color: yellow;
        font-size: 24px;
    }
</style>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<div class="w-100 m-auto">
    <div class="btn-m-green m-t-30  m-l-30" id="return_btn">
         退出
    </div>
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 100px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        设置电子书故事
                    </div>
                    <?php
                    if (!empty($message)) {
                        ?>
                    <div style="margin: 20px; padding: 10px; font-height: 150%;">
                    <?= $message ?>
                    </div>

                        <div class="btn-m-green m-t-30 float-right m-r-20" id="set_return_ebook">
                            返回
                        </div>
                    <?php
                    } else {
                    ?>
                    <form method="post">
            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border" style="border: 0px; ">
                        <label class="form-check-label fs-30 answer-btn">
                            <div class="set">
                            </div>
                            <div class="grade_options">
                                <select name="user_ebook_story_id" style="border: 0px; background-color: black; color: #fff; padding: 10px; height: 80px;">
                                <?php
                                foreach (\common\models\UserEBook::$poiList as $userEbookStoryId => $story) {
                                    ?>
                                    <option value="<?= $story['id'] ?>"
                                        <?php
                                        if ($story['id'] == $ebookStoryId) {
                                            echo ' selected';
                                        }
                                        ?>
                                    ><?= $story['story'] ?></option>
                                    <?php
                                }
                                ?>
                                </select>
                            </div>
                            <div class="set" style="margin-top: 50px;">
                                <input type="submit" value="变更" class="btn-m-green m-t-30" style="position: relative; width: 200px; height: 80px;">
                            </div>


                    </label>
                     </div>
                </div>

            </div>
                    </form>
                    <?php
                    }
                    ?>

                </div>
            </div>

        </div>
        </div>

    </div>

</div>
<script>
    window.onload = function () {
        $('#set_return_ebook').click(function () {
            location.href= '/myh5/set_ebook_story?user_id=<?=$userId?>';
        });
    };
</script>

