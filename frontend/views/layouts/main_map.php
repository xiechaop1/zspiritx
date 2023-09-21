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
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content=",AR游戏|AR剧本杀|剧本杀|AR教育|儿童剧本杀|素质教育|实景教育|实景剧本杀|实景游戏">
    <meta name="description" content="灵境剧本杀是做实景剧本和玩家之间的关联平台，包括教育类剧本，游戏类剧本，儿童剧本，成人剧本等，可以满足不同年龄段玩家的需求，并且可以亲身体验实景剧本带来的全新玩法">

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    
    <?php $this->head() ?>
     <link rel="stylesheet" href="https://a.amap.com/jsapi_demos/static/demo-center/css/demo-center.css"/>
<style type="text/css">
    html, body {
      height: 100%;
      width: 100%;
      padding:20px 0;
    }
     #container {
          height: 100%;
          width: 100%;
        }
</style>
</head>
<body class="bg-black"  id="map-page">
<?php $this->beginBody() ?>

        <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
