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

$this->title = $qa['topic'];

?>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<div class="w-100 m-auto">

    <div class="p-20 bg-F5">
        <div class="w-100 p-30 bg-FF m-b-10">
            <div class="w-100">
                <div class="fs-30 bold w-100">
                    <?= $qa['topic'] ?>
                </div>
                <div class="text-66 text-center mt-2 mb-3 fs-20">
                <?php
                $str = $qa['selected_json'];
                $str = str_replace("[div]", '<div>', $str);
                $str = str_replace("[/div]", '</div>', $str);
//                    echo $qa['selected_json'];
                ?>
                <?php
                $answers = ['A', 'B', 'C', 'D'];
                foreach ($answers as $an) {
                    $optstr = '<div class="form-check form-check-inline m-t-5">';
                    $optstr .= '<input class="form-check-input"  type=radio name="answer" value="' . $an . '" id="answer-' . $an . '">';
                    $labelstr = '<label class="form-check-label fs-30 text-66" for="answer-' . $an . '">';
                    //. $an .'</label></div>';
                    $findstr = '[opt ' . $an . ']';
                    $str = str_replace($findstr, $optstr, $str);

                    $findstr = '[label ' . $an . ']';
                    $str = str_replace($findstr, $labelstr, $str);
                }
                $str = str_replace('[/label]', '</label>', $str);
                $str = str_replace('[/opt]', '</div>', $str);
                echo $str;
                ?>

                </div>
            </div>
                    <div class="text-center m-t-30">
            <label class="h5-btn-green-big answer-btn"  data-value="<?php echo $qa['st_selected']; ?>
" data-qa="<?php echo $qa['id']; ?>" data-story="<?php echo $qa['story_id']; ?>" data-user="">
                提交
            </label>
        </div>
        </div>
       
    </div>

</div>
<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-null" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                       请选择答案
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-right" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        恭喜您答对了
                    </div>
                    <div class="text-center m-t-30">
                        <?php echo $qa['st_answer']; ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-worry" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        很稀罕，打错了
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5">
                        <?php echo $qa['st_answer']; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
