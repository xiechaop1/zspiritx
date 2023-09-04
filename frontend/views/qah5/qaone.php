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
<audio autoplay loop>
  <source src="<?= $qa['voice'] ?>" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        角色NPC名
                    </div>
                     <?= $qa['topic'] ?>
                    <div>
                     <img src=" <?= $qa['attachment'] ?>" alt="" class="img-responsive d-block"/>
                    </div>
                    <!--<div class="hpa-ctr">
                        <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        播放语音
                    </div>-->
                </div>
            </div>
            <div class="row" id="answer-box">
                <?php
                $str = $qa['selected_json'];
                $str = str_replace("[div]", '<div>', $str);
                $str = str_replace("[/div]", '</div>', $str);
//                    echo $qa['selected_json'];
                ?>
                <?php
                $answers = ['A', 'B', 'C', 'D'];
                foreach ($answers as $an) {
                    $optstr = '<div class="m-t-30 col-sm-12 col-md-6">';

                    $optstr .= '<div class="answer-border"><input class="form-check-input"  type=radio name="answer" value="' . $an . '" id="answer-' . $an . '">';

                    $labelstr = '<label class="form-check-label fs-30 answer-btn" for="answer-' . $an . '">';

                    $findstr = '[opt  <span class="answer-tag">' . $an . '</span>]';
                    $str = str_replace($findstr, $optstr, $str);

                    $findstr = '[label ' . $an . ']';
                    $str = str_replace($findstr, $labelstr, $str);
                }
                $str = str_replace('[/label]', '</label></div>', $str);
                $str = str_replace('[/opt]', '</div>', $str);
                echo $str;
                ?>


                <!--<div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes">
                            <span class="answer-tag">A</span>
                            8跟
                        </label>
                    </div>
                </div>-->

            </div>
            <div class="row hide" id="answer-right-box">
                <div class="m-t-30 col-sm-12 col-md-12 p-40">
                    <img src="../../img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>
                    <div class="answer-title m-t-40">
                        <?php echo $qa['st_selected']; ?>
                    </div>
                    <div class="answer-detail m-t-40">
                         <?php echo $qa['st_answer']; ?>
                    </div>
                </div>

            </div>
            <div class="row hide" id="answer-error-box">
                <div class="m-t-36 col-sm-12 col-md-12">
                    <div class="answer-detail " >
                        <img src="../../img/qa/icon_错误提示@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                        <span  class=" d-inline-block vertical-mid">很遗憾，答错了…</span>

                    </div>
                </div>
            </div>





            <div class="w-100">

                <div class="text-66 text-center mt-2 mb-3 fs-20">



                </div>
            </div>
                    <div class="text-center m-t-30">
            <label id="answer-info" class="h5-btn-green-big answer-btn hide"  data-value="<?php echo $qa['st_selected']; ?>
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
