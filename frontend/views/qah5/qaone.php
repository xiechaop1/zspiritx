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
$this->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width; initial-scale=1.0',
]);

$this->title = $qa['topic'];

?>

<div class="w-1200 mt-5 ">
<!--    <div class="relative w-245 d-inline-block align-top rounded border overflow-hidden text-FF mr-4 mt-5 pb-3 bg-FF tree" id="ListGroup">-->
<!--        <ul class="nav flex-column">-->
<!--            <li class="title bg-F6 py-3 px-4 fs-18 bg-en">-->
<!--                推荐文章-->
<!--            </li>-->
<!--            -->
<!---->
<!---->
<!--        </ul>-->
<!--        <div><img src="/static/img/lxs.png" class="absolute pb-2 bottom-0 z-index-99 right-0"></div>-->
<!--    </div>-->
    <div class="d-inline-block align-top">
        <div class="content rounded mt-5 p-0 bg-FF border-EA px-5 py-4 bg-FF ">
            <div class="relative">
            </div>
            <div class="d-flex align-items-center justify-content-center mt-5">
                <span class="fs-22 text-F6"><?= $qa['topic'] ?></span>
            </div>
            <div class="text-66 text-center mt-2 mb-3 fs-15">
                <?php
                    echo $qa['selected_json'];
                ?>

            </div>
            <!--            <img class="w-100" src="" alt="">-->
            <div class="pb-3" id="article-content-box">
                <?php
                $answers = ['A', 'B', 'C', 'D'];
                foreach ($answers as $an) {
                    echo '<input type=radio name="answer" value="' . $an . '" id="answer-' . $an . '"> ' . $an . '<br>';
                }
                ?>
                <input type="button" value="提交" id="submit">

            </div>
        </div>
    </div>
    <!-- <div class="toTop rounded-circle bg-F6">
        返回
    </div> -->
</div>