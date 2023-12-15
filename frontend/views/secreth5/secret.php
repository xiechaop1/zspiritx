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
 * @var \common\models\Secret $qa
 */

\frontend\assets\Secreth5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',q
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '密码锁';

?>
<style>
    .hide {display: none;}
</style>
<input type="hidden" name="pin_code" value="<?= $stSelected ?>">
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="session_stage_id" value="<?= $sessionStageId ?>">
<input type="hidden" name="qa_id" id="qa_id" value="<?= $qaId ?>">
<input type="hidden" name="story_id" id="story_id" value="<?= $storyId ?>">


<div class="w-100 m-auto">
    <audio controls id="audio_right" class="hide">
        <source src="../../static/audio/qa_right.mp3" type="audio/mpeg">
        您的浏览器不支持 audio 元素。
    </audio>
    <audio controls id="audio_wrong" class="hide">
        <source src="../../static/audio/qa_wrong.mp3" type="audio/mpeg">
        您的浏览器不支持 audio 元素。
    </audio>

<div class="btn-m-green m-t-30 float-right m-r-20" id="qa_return_btn">
  返回
</div>

<div class="loader"></div>

<div class="lock">
  <div class="screen">
    <div class="code"><?= str_repeat('0', strlen($stSelected)) ?></div>
    <div class="status">LOCKED</div>
    <div class="scanlines"></div>
  </div>
  <div class="rows">
    <?php
    for ($i=0; $i<strlen($stSelected); $i++) {
      ?>
      <div class="row">
          <?php
          for ($j=0; $j<mb_strlen($qa['selected_json'], 'UTF8'); $j++) {
          ?>
      <div class="cell">
        <div class="text"><?= mb_substr($qa['selected_json'], $j, 1, 'UTF8') ?></div>
      </div>
          <?php
          }
          ?>
<!--      <div class="cell">-->
<!--        <div class="text">1</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">2</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">3</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">4</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">5</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">6</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">7</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">8</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">9</div>-->
<!--      </div>-->
    </div>
    <?php
    }
    ?>
<!--    <div class="row">-->
<!--      <div class="cell">-->
<!--        <div class="text">0</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">1</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">2</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">3</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">4</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">5</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">6</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">7</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">8</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">9</div>-->
<!--      </div>-->
<!--    </div>-->
<!--    <div class="row">-->
<!--      <div class="cell">-->
<!--        <div class="text">0</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">1</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">2</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">3</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">4</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">5</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">6</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">7</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">8</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">9</div>-->
<!--      </div>-->
<!--    </div>-->
<!--    <div class="row">-->
<!--      <div class="cell">-->
<!--        <div class="text">0</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">1</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">2</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">3</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">4</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">5</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">6</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">7</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">8</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">9</div>-->
<!--      </div>-->
<!--    </div>-->
<!--    <div class="row">-->
<!--      <div class="cell">-->
<!--        <div class="text">0</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">1</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">2</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">3</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">4</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">5</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">6</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">7</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">8</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">9</div>-->
<!--      </div>-->
<!--    </div>-->
<!--    <div class="row">-->
<!--      <div class="cell">-->
<!--        <div class="text">0</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">1</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">2</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">3</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">4</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">5</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">6</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">7</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">8</div>-->
<!--      </div>-->
<!--      <div class="cell">-->
<!--        <div class="text">9</div>-->
<!--      </div>-->
<!--    </div>-->
  </div>
</div>

    <div class="hide answer-right" id="answer-right-box" style="position: absolute; background-color: #1F2628 !important; z-index: 9999999999; width: 90%;font-size: 14px; top: 30px; left: 15px;">
        <div class="m-t-30 col-sm-12 col-md-12 p-40">
            <img src="../../static/img/qa/Frame@2x.png" style="width: 100%; height: auto;" alt="" class="img-responsive  d-block m-auto"/>
            <?php
            if (!empty($qa['score'])) {
                ?>
                <div style="clear:both; text-align: center;">
                        <span>
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 75px; height: 75px; vertical-align: middle;" class=""/>
                            </span>

                    <span class="answer-detail" style="box-sizing: border-box; color: yellow; font-size: 24px; font-weight: 500; line-height: 24px;letter-spacing: 2px; text-align: center;">
                    +<?= $qa['score'] ?>枚
                        </span>
                </div>
                <?php
            }
            ?>
            <div class="answer-title m-t-40" style="color: yellow; font-size: 36px; font-weight: 500; line-height: 28px;letter-spacing: 2px;  text-align: center;">
                <?= $qa['st_selected'] ?>
            </div>
            <div class="answer-detail m-t-40" style="color: yellow; line-height: 24px; font-size: 14px; margin-top: 14px; margin: 15px;">
                <?= $qa['st_answer'] ?>
            </div>
        </div>

    </div>