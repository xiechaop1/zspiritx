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
<div class="w-100 m-auto">

    <div class="p-20 bg-F5">
        <div class="w-100 p-30 bg-FF m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100">
                    <?= $qa['topic'] ?>
                </div>
            </div>
             <?php
                $answers = ['A', 'B', 'C', 'D'];
                foreach ($answers as $an) {
                    echo '<div><div class="form-check form-check-inline m-t-5">
                    <input class="form-check-input"  type=radio name="answer" value="' . $an . '" id="answer-' . $an . '"> <label class="form-check-label fs-30 text-66" for="answer-' . $an . '">' . $an .'</label>
                                        </div>
             </div>';
                }
                ?>

                    <div class="text-center m-t-30">
            <label class="h5-btn-green-big loginBtn">
                提交
            </label>
        </div>
        </div>
       
    </div>

</div>
