<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 1:33 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

/**
 * @desc 首页静态资源
 * Class IndexAsset
 * @package frontend\assets
 */
class IndexAsset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    public $css = [
         'site/css/style.css',
         'site/css/bootstrap.css',
         'site/css/plugin/jPushMenu.css',
         'site/css/plugin/animate.css',
    ];

    public $js = [
         'site/js/jquery-1.11.2.min.js',
         'site/js/plugin/jquery.easing.js',
         'site/js/jquery-ui.min.js',
         'site/js/bootstrap.min.js',
         'site/js/plugin/jquery.flexslider.js',
         'site/js/plugin/background-check.min.js',
         'site/js/plugin/jquery.fitvids.js',
         'site/js/plugin/jquery.viewportchecker.js',
         'site/js/plugin/jquery.stellar.min.js',
         'site/js/plugin/wow.min.js',
         'site/js/plugin/jquery.colorbox-min.js',
         'site/js/plugin/owl.carousel.min.js',
         'site/js/plugin/isotope.pkgd.min.js',
         'site/js/plugin/masonry.pkgd.min.js',
         'site/js/plugin/imagesloaded.pkgd.min.js',
          'site/js/plugin/jPushMenu.js',
          'site/js/plugin/jquery.fs.tipper.min.js',
          'site/js/plugin/jquery.backstretch.js',
          'site/js/plugin/mediaelement-and-player.min.js',
          'site/js/theme.js',
          'site/js/navigation.js',
    ];

}