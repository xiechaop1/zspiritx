<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/1
 * Time: 3:13 PM
 */

namespace frontend\actions\download;


use common\helpers\Common;

use yii\base\Action;

class Redirect extends Action
{

    public function run()
    {
        $system = Common::chooseSystem();

        $downloadAllow = 0;
        switch ($system) {
            case 'ios':
                $uri = 'https://apps.apple.com/cn/app/%E7%81%B5%E9%95%9C%E6%96%B0%E4%B8%96%E7%95%8C/id6471038525';
                $downloadAllow = 1;
                break;
            case 'android':
                $uri = 'https://download.zspiritx.com.cn/download/lingjing_huawei_v1.apk';
                $downloadAllow = 1;
                break;
            default:
                break;
        }

        if ($downloadAllow == 1) {
            header('location: ' . $uri);
        }
        return $this->controller->render('redirect', [
            'uri' => $uri,
        ]);

    }
}