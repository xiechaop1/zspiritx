<?php

/* @var $this yii\web\View */

\frontend\assets\Qah5Asset::register($this);

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
                    <a href="index.html">
                        <img class="l-black" src="image/logo.png" />
                        <img class="l-white" src="image/logo.png" />
                        <img class="l-color" src="image/logo.png" />
                    </a>
                </div>
                <!--End logo-->
                <!-- Navigation Menu -->
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
                </nav>
                <!--End Navigation Menu -->

            </div>
        </header>
        <!-- END HEADER -->

        <!-- CONTENT --------------------------------------------------------------------------------->

        <!--About Section-->
        <section id="about" class="wow ptb ptb-sm-40 dark-bg">
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
                                            灵镜AR游戏
                                        </p>
                                    </div>
                                    <div class="col-md-12 ma-t-50 col-xs-12" >
                                        <p class="white text-left  content-t-1">
                                            庄生科技，全面打造AR剧本杀，主要面向儿童群体，打造儿童AR剧本杀<br>
                                            目前<span class="color-red">陶然亭、凯德茂大峡谷</span>等项目正在建设中，敬请期待！

                                        </p>
                                    </div>
                                </div>
                                <div class="row ma-t-50">
                                    <div class="col-md-8 xs-m-b-40">
                                        <div class="row">
                                            <div class="col-md-6 col-xs-6">
                                                <p class="text-left">
                                                    华为下载
                                                </p>
                                                <div class="qr-box-1">
                                                    <img src="<?= $qrCode['huawei']; ?>" class="img-responsive"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-6">
                                                <p class="text-left">
                                                    敬请期待
                                                </p>

                                                <div class="margin-top-30">
                                                    <img src="../../static/image/ios.png" class="img-responsive"/>
                                                </div>
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
                        <span class="margin-10 footer-m-span white">Copyright © 2004-2023 庄生科技 zspiritx.com.cn 版权所有</span>
                        <span class="margin-10 footer-m-span"><a href="https://beian.miit.gov.cn" class="white">京ICP备2023021255号</a></span>
                    </div>
                </div>
                <!-- End Footer Info -->
            </div>
        </footer>
        <!-- END FOOTER -->


    </div>
    <!-- Site Wraper End -->

