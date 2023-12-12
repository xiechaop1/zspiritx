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

\frontend\assets\secreth5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '密码锁';

?>
<input type="hidden" name="pin_code" value="<?= $pinCode ?>">

<div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
  返回
</div>

<div class="loader"></div>
<div class="lock">
  <div class="screen">
    <div class="code">0000</div>
    <div class="status">LOCKED</div>
    <div class="scanlines"></div>
  </div>
  <div class="rows">
    <div class="row">
      <div class="cell">
        <div class="text">0</div>
      </div>
      <div class="cell">
        <div class="text">1</div>
      </div>
      <div class="cell">
        <div class="text">2</div>
      </div>
      <div class="cell">
        <div class="text">3</div>
      </div>
      <div class="cell">
        <div class="text">4</div>
      </div>
      <div class="cell">
        <div class="text">5</div>
      </div>
      <div class="cell">
        <div class="text">6</div>
      </div>
      <div class="cell">
        <div class="text">7</div>
      </div>
      <div class="cell">
        <div class="text">8</div>
      </div>
      <div class="cell">
        <div class="text">9</div>
      </div>
    </div>
    <div class="row">
      <div class="cell">
        <div class="text">0</div>
      </div>
      <div class="cell">
        <div class="text">1</div>
      </div>
      <div class="cell">
        <div class="text">2</div>
      </div>
      <div class="cell">
        <div class="text">3</div>
      </div>
      <div class="cell">
        <div class="text">4</div>
      </div>
      <div class="cell">
        <div class="text">5</div>
      </div>
      <div class="cell">
        <div class="text">6</div>
      </div>
      <div class="cell">
        <div class="text">7</div>
      </div>
      <div class="cell">
        <div class="text">8</div>
      </div>
      <div class="cell">
        <div class="text">9</div>
      </div>
    </div>
    <div class="row">
      <div class="cell">
        <div class="text">0</div>
      </div>
      <div class="cell">
        <div class="text">1</div>
      </div>
      <div class="cell">
        <div class="text">2</div>
      </div>
      <div class="cell">
        <div class="text">3</div>
      </div>
      <div class="cell">
        <div class="text">4</div>
      </div>
      <div class="cell">
        <div class="text">5</div>
      </div>
      <div class="cell">
        <div class="text">6</div>
      </div>
      <div class="cell">
        <div class="text">7</div>
      </div>
      <div class="cell">
        <div class="text">8</div>
      </div>
      <div class="cell">
        <div class="text">9</div>
      </div>
    </div>
    <div class="row">
      <div class="cell">
        <div class="text">0</div>
      </div>
      <div class="cell">
        <div class="text">1</div>
      </div>
      <div class="cell">
        <div class="text">2</div>
      </div>
      <div class="cell">
        <div class="text">3</div>
      </div>
      <div class="cell">
        <div class="text">4</div>
      </div>
      <div class="cell">
        <div class="text">5</div>
      </div>
      <div class="cell">
        <div class="text">6</div>
      </div>
      <div class="cell">
        <div class="text">7</div>
      </div>
      <div class="cell">
        <div class="text">8</div>
      </div>
      <div class="cell">
        <div class="text">9</div>
      </div>
    </div>
  </div>
</div>
