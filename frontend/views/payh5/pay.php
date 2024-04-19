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

\frontend\assets\Payh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

//$this->title = $qa['topic'];

?>
<div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
  返回
</div>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">

<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<div class="toast-box hide">
  <div class="toast">
    Toast提示
  </div>
</div>


<div class="keypad-top">
  <div class="keypad-shop-info">
    <span class="shop-name">支付金额(￥)</span>
  </div>
  <label class="inputlabel" id="keypadNum" type="text" data-value="100.00">100.00</label>
</div>
<div class="pay-box ">
  <div class="pay">
    微信支付
  </div>
  <div class="btn-m-border hide" id="pay-complete">
    支付完成
  </div>
  <div class="btn-m-border hide" id="pay-retry">
    重新支付
  </div>
</div>