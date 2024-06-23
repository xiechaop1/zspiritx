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
                        设置(<?= !empty($user->user_name) ? $user->user_name : $userId ?>)
                    </div>

            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
<!--                        年级：<span class="fs-30">--><?php //= $userExtends->grade ?><!--</span>-->
<!--                        等级：<span class="fs-30">--><?php //= $userExtends->level ?><!--</span>-->
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label class="form-check-label fs-30 answer-btn">
                            <span class="answer-tag">
                              <img src="../../static/img/my/task.png" class="list-icon-s"/>
                            </span>
                            <div class="set">
                                年级：<?= !empty($userExtends->grade) ? \common\models\UserExtends::$userGrade2Name[$userExtends->grade] .
                                    ' <span id="change">[修改]</span>'
                                    : ' - ' ?>
                            </div>
                            <div class="grade_options"<?= !empty($userExtends->grade) ? ' style="display: none"' : '' ?>>
                                <?php
                                foreach (\common\models\UserExtends::$userSchoolGrade as $school => $grades) {
                                    ?>
                                    <div class="school"><?= \common\models\UserExtends::$userSchool2Name[$school] ?></div>
                                    <?php
                                    foreach ($grades as $grade) {
                                        ?>
                                        <div class="fs-30 grade"><a href="?user_id=<?= $userId ?>&grade=<?= $grade ?>"><?= \common\models\UserExtends::$userGrade2Name[$grade] ?></a></div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                }
                                ?>
                            </div>
                            <div class="set">
                                等级：<?= !empty($userExtends->level) ? $userExtends->level : ' - ' ?>
                            </div>

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
    window.onload = function () {
        $('#change').click(function () {
            $('.grade_options').toggle();
        });
    };
</script>

