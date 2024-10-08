<aside class="main-sidebar control-sidebar-dark">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->name ?></p>
                <a><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <?php
        $role = Yii::$app->user->identity->role;
        $specialType = !empty($_GET['special_type'][0]) ? $_GET['special_type'][0] : '';
        ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => [
                    ['label' => 'Menus', 'options' => ['class' => 'header']],

                    [
                        'label' => '总览',
                        'icon' => 'dashboard',
                        'url' =>'/'
                    ],
                    [
                        'label' => '剧本管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '剧本列表',
                                'url' => ['/story/story'],
                                'active' => in_array($this->context->route, ['story/story', 'story/edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '剧本扩展管理',
                                'url' => ['/story/story_extend'],
                                'active' => in_array($this->context->route, ['story/story_extend', 'story/story_extend_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户剧本管理',
                                'url' => ['/story/user_story'],
                                'active' => in_array($this->context->route, ['story/user_story', 'story/user_story_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '场次',
                                'url' => ['/story/session'],
                                'active' => in_array($this->context->route, ['story/session', 'story/session_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '角色',
                                'url' => ['/story/role'],
                                'active' => in_array($this->context->route, ['story/role', ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                        ]
                    ],
                    [
                        'label' => '模型管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '剧本模型列表',
                                'url' => ['/model/story_model'],
                                'active' => in_array($this->context->route, ['model/story_model', 'model/story_model_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '剧本模型关联列表',
                                'url' => ['/model/story_model_link'],
                                'active' => in_array($this->context->route, ['model/story_model_link', 'model/story_model_link_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '剧本模型详情列表',
                                'url' => ['/model/story_model_detail'],
                                'active' => in_array($this->context->route, ['model/story_model_detail', 'model/story_model_detail_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '模型特效列表',
                                'url' => ['/model/story_model_special_eff'],
                                'active' => in_array($this->context->route, ['model/story_model_special_eff', 'model/story_model_special_eff_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户模型（背包）列表',
                                'url' => ['/model/user_model'],
                                'active' => in_array($this->context->route, ['model/user_model', 'model/user_model_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户地点模型列表',
                                'url' => ['/model/user_model_loc'],
                                'active' => in_array($this->context->route, ['model/user_model_loc', 'model/user_model_loc_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '模型列表',
                                'url' => ['/model/models'],
                                'active' => in_array($this->context->route, ['model/models', 'model/models_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '剧本Stage列表',
                                'url' => ['/model/story_stage'],
                                'active' => in_array($this->context->route, ['model/story_stage', 'model/story_stage_edit']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '剧本Stage关系列表',
                                'url' => ['/model/story_stage_link'],
                                'active' => in_array($this->context->route, ['model/story_stage_link']),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                        ]
                    ],
                    [
                        'label' => '问答管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '问答列表',
                                'url' => ['/qa/qa'],
                                'active' => in_array($this->context->route, ['qa/qa', 'qa/edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '课包列表',
                                'url' => ['/qa/qa_package'],
                                'active' => in_array($this->context->route, ['qa/qa_package', 'qa/package_edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户作答',
                                'url' => ['/qa/user_qa'],
                                'active' => in_array($this->context->route, ['qa/user_qa', ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '诗词列表',
                                'url' => ['/qa/poem'],
                                'active' => in_array($this->context->route, ['qa/poem', 'qa/poem_edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
//                            [
//                                'label' => '诗词编辑',
//                                'url' => ['/qa/poem_edit'],
//                                'active' => in_array($this->context->route, ['qa/poem_edit' ]),
//                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
//                            ],
                        ]
                    ],
                    [
                        'label' => '地盘管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '地盘列表',
                                'url' => ['/location/location'],
                                'active' => in_array($this->context->route, ['location/location', 'location/edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                        ]
                    ],
                    [
                        'label' => '商品管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '商品列表',
                                'url' => ['/shop/shop_ware'],
                                'active' => in_array($this->context->route, ['shop/shop_ware', 'shop/shop_ware_edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                        ]
                    ],
                    [
                        'label' => '抽奖管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '抽奖列表',
                                'url' => ['/lottery/lottery'],
                                'active' => in_array($this->context->route, ['lottery/lottery', ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '奖品列表',
                                'url' => ['/lottery/lottery_prize'],
                                'active' => in_array($this->context->route, ['lottery/lottery_prize', 'lottery/lottery_prize_edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户奖品',
                                'url' => ['/lottery/user_prize'],
                                'active' => in_array($this->context->route, ['lottery/user_prize', 'lottery/user_prize_edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_BACKEND_EDITOR)
                            ],
                            [
                                'label' => '用户奖券',
                                'url' => ['/lottery/user_lottery'],
                                'active' => in_array($this->context->route, ['lottery/user_lottery', 'lottery/user_lottery_edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_BACKEND_EDITOR)
                            ],
                        ]
                    ],
                    [
                        'label' => '知识管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '知识列表',
                                'url' => ['/knowledge/knowledge'],
                                'active' => in_array($this->context->route, ['knowledge/knowledge', 'knowledge/edit' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '知识关联列表',
                                'url' => ['/knowledge/item_knowledge'],
                                'active' => in_array($this->context->route, ['knowledge/item_knowledge' ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户知识',
                                'url' => ['/knowledge/user_knowledge'],
                                'active' => in_array($this->context->route, ['knowledge/user_knowledge', ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                        ]
                    ],
                    [
                        'label' => '用户管理',
                        'icon' => 'folder-open',
                        'items' => [
                            [
                                'label' => '用户列表',
                                'icon' => 'users',
                                'url' => ['/user/users'],
                                'active' => in_array($this->context->route, ['user/users', 'user/edit', ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                            [
                                'label' => '用户积分',
                                'icon' => 'users',
                                'url' => ['/user/user_score'],
                                'active' => in_array($this->context->route, ['user/user_score', ]),
                                'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                            ],
                        ],
                    ],
                    [
                        'label' => '分类管理',
                        'icon' => 'cubes',
                        'url' => ['/base/categories'],
                        'active' => in_array($this->context->route, ['base/categories', 'base/category_edit']),
                        'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_PLATFORM)
                    ],
                    [
                        'label' => 'Banner管理',
                        'icon' => 'image',
                        'url' => ['/banner/banners'],
                        'active' => in_array($this->context->route, ['banner/banners', 'banner/edit']),
                        'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_PLATFORM)
                    ],
                    [
                        'label' => '订单列表',
                        'icon' => 'shopping-cart',
                        'url' => ['/order/orders'],
                        'active' => in_array($this->context->route, ['order/orders', 'order/edit', ]),
                        'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_EDITOR)
                    ],
                    [
                        'label' => '管理员列表',
                        'icon' => 'user',
                        'url' => ['/admin/index'],
                        'active' => in_array($this->context->route, ['admin/index', 'admin/edit', ]),
                        'visible' => \common\helpers\AdminRole::checkRole(\common\definitions\Admin::ROLE_PLATFORM)
                    ],
//                    [
//                        'label' => '分类管理',
//                        'icon' => 'folder-open',
//                        'items' => [
//                            [
//                                'label' => '分类管理',
//                                'url' => ['/base/categories'],
//                                'active' => in_array($this->context->route, ['base/categories', 'base/category_edit']),
//                                'visible' => $role == 0
//                            ],
////                            [
////                                'label' => '歌手管理',
////                                'url' => ['/base/singers'],
////                                'active' => in_array($this->context->route, ['base/singers', 'base/singer_edit' ]),
////                                'visible' => $role == 0
////                            ],
//                        ]
//
//                    ],
//                    [
//                        'label' => 'Banner管理',
//                        'icon' => 'folder-open',
//                        'items' => [
//                            [
//                                'label' => 'Banner管理',
//                                'url' => ['/banner/banners'],
//                                'active' => in_array($this->context->route, ['banner/banners', 'banner/edit']),
//                                'visible' => $role == 0
//                            ],
//
//                        ]
//
//                    ],
//                    [
//                        'label' => 'Demo管理',
//                        'icon' => 'folder-open',
//                        'items' => [
//                            [
//                                'label' => '歌曲列表',
//                                'url' => ['/music/music'],
//                                'active' => in_array($this->context->route, ['music/music', ]),
//                                'visible' => $role == 0
//                            ],
//                            [
//                                'label' => '上传歌曲',
//                                'url' => ['/music/edit'],
//                                'active' => in_array($this->context->route, ['music/edit', ]),
//                                'visible' => $role == 0
//                            ],
//                        ]
//                    ],
//                    [
//                        'label' => '订单管理',
//                        'icon' => 'folder-open',
//                        'items' => [
//                            [
//                                'label' => '订单列表',
//                                'url' => ['/order/orders'],
//                                'active' => in_array($this->context->route, ['order/orders', 'order/edit', ]),
//                                'visible' => $role == 0
//                            ],
//                        ]
//                    ],
//                    [
//                        'label' => '白名单管理',
//                        'icon' => 'folder-open',
//                        'items' => [
//                            [
//                                'label' => '用户列表',
//                                'url' => ['/user/users'],
//                                'active' => in_array($this->context->route, ['user/users', 'user/edit', ]),
//                                'visible' => $role == 0
//                            ],
//                        ]
//                    ],


                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                ],
            ]
        ) ?>

    </section>

</aside>
