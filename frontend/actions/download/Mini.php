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

class Mini extends Action
{

    public function run()
    {
//        $uri = 'https://www.zspiritx.com.cn/wx4fcf3c1035010828?id=78';
        $uri = 'https://www.zspiritx.com.cn/wx4fcf3c1035010828?id=78';

        $downloadAllow = 1;
        if ($downloadAllow == 1) {
            header('location: ' . $uri);
        }

        return $this->controller->render('redirect', [
            'uri' => $uri,
        ]);

    }
}