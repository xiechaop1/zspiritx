<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=750,user-scalable=no, target-densitydpi=device-dpi">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content=",AR游戏|AR剧本杀|剧本杀|AR教育|儿童剧本杀|素质教育|实景教育|实景剧本杀|实景游戏">
    <meta name="description" content="灵境剧本杀是做实景剧本和玩家之间的关联平台，包括教育类剧本，游戏类剧本，儿童剧本，成人剧本等，可以满足不同年龄段玩家的需求，并且可以亲身体验实景剧本带来的全新玩法">
    <link rel="shortcut icon" type="image/x-icon" href="../../static/image/favicon.ico">
    <link rel="icon" type="image/png" href="../../static/imagee/favicon.ico">
    <link rel="apple-touch-icon" href="../../static/image/favicon.ico">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
    <?php $this->head() ?>
</head>
<body class="bg-black" >
<?php $this->beginBody() ?>

        <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
