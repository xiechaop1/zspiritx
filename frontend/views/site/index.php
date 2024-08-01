<?php

/* @var $this yii\web\View */

\frontend\assets\IndexAsset::register($this);

$this->title = '庄生科技';
?>
    <!-- Preloader -->
    <section id="preloader">
        <div class="loader" id="loader">
            <div class="loader-img"></div>
        </div>
    </section>
    <!-- End Preloader -->

    <!-- Site Wraper -->
    <div class="wrapper">

        <!-- HEADER -->
        <header class="header">
            <div class="container">

                <!-- logo -->
                <div class="logo">
                    <a href="">
                        <img class="l-black" src="../../static/image/logo.png" />
                        <img class="l-white" src="../../static/image/logo.png" />
                        <img class="l-color" src="../../static/image/logo.png" />
                    </a>
                </div>
                <!--End logo-->
                <!-- Navigation Menu -->
                <!--
                <nav class='navigation'>
                    <ul>
                        <li>
                            <a href="product-detail.html" class="scroll-to-target">了解庄生</a>
                        </li>
                        <li>
                            <a href="customer-zone.html" class="scroll-to-target">官方咨询</a>
                        </li>
                        <li>
                            <a href="notices.html" class="scroll-to-target">联系我们</a>
                        </li>

                    </ul>
                </nav>-->
                <!--End Navigation Menu -->

            </div>
        </header>
        <!-- END HEADER -->

        <!-- CONTENT --------------------------------------------------------------------------------->

        <!--About Section-->
        <section id="about" class="wow ptb ptb-sm-40 dark-bg bg-imgbg">
            <div class="container" style="margin-bottom: 30px;">
                <div class="row text-center ma-t-50">
                    <div class="col-md-10 col-md-offset-1">
                        <!--                        <h2 class="h4">关于我们</h2>-->
                        <div class="spacer-15"></div>
                        <div class="row margin-top-80 xs-m-t-50">
                            <div class="col-md-7 col-sm-12">
                                <div class="row">
                                    <div class="col-md-3 erweima col-xs-4">
                                        <img src="../../static/image/icon.png" class="img-responsive"/>
                                    </div>
                                    <div class="col-md-9 col-xs-8">
                                        <p class="logo-t-h1">
                                            灵镜新世界
                                        </p>
                                    </div>
                                    <div class="col-md-12 ma-t-50 col-xs-12" >
                                        <p class="white text-left  content-t-1">
                                            灵镜新世界，是一个融入AR玩法，立足场景游戏+教育，结合AI的全新游戏教育领域。<br>
                                            在灵镜新世界中，每个商场、博物馆、公园，甚至是街道，机场等等每个场景，都可能有着不同的故事，等待着你的探索。<br>
                                            在此你还可以结交朋友，组队配合，一起打怪，一起探索，一起解谜，还原故事，感受每个故事背后的道理。<br>
                                            在游玩中学习知识，锻炼技能<br>
                                            目前在<span class="color-red">汽车博物馆极速狂飙、国家植物园桃花蜜语、自然博物馆的《奇幻之旅》项目已经上线</span>，目前支持<span color="color-red">iOS、华为，以及部分支持AR的Android机型都已经支持</span>，欢迎体验<br>
                                            <span class="color-red">陶然亭文化之旅、龙潭湖公园的灵石之谜、颐和园清宫探秘</span>等项目正在建设中，敬请期待！

                                        </p>
                                    </div>
                                </div>
                                <div class="row m-t-20">
                                    <div class="col-md-8 xs-m-b-40">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-6 m-t-20">
                                                <p class="text-left">
                                                    iPhone下载（支持iPhone8以上机型）
                                                </p>
                                                <div class="qr-box-1">
                                                    <img src="<?= $qrCode['ios']; ?>" class="img-responsive"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-6 m-t-20">
                                                <p class="text-left">
                                                    Android下载
                                                </p>
                                                <div class="qr-box-1">
                                                    <img src="<?= $qrCode['huawei']; ?>" class="img-responsive"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-6 m-t-20">
                                                <p class="text-left">
                                                    敬请期待
                                                </p>

                                                <div class="margin-top-20">
                                                    <img src="../../static/image/xiaochengxu.png" class="img-responsive"/>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">

                                    </div>
                                </div>

                            </div>
                            <div class="col-md-5">
                                <img src="../../static/image/mocup.png" class="img-responsive"/>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </section>

        <!-- End About Section-->



        <!-- END CONTENT ---------------------------------------------------------------------------->

        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <!--Footer Info -->
                <div class="row footer-info text-center">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <span class="margin-10 footer-m-span white">Copyright © 2023-2024 庄生科技 zspiritx.com.cn 版权所有</span>
                        <span class="margin-10 footer-m-span"><a href="https://beian.miit.gov.cn" class="white">京ICP备2023021255号</a></span>
                    </div>
                </div>
                <!-- End Footer Info -->
            </div>
        </footer>
        <!-- END FOOTER -->


    </div>
    <!-- Site Wraper End -->

