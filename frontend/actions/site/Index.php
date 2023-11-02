<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/1
 * Time: 4:17 PM
 */

namespace frontend\actions\site;


use common\helpers\Attachment;
use common\helpers\Common;
use common\helpers\Cookie;
use common\models\Banner;
use common\models\ConsultantCompany;
use common\models\Industry;
use common\models\Job;
use common\models\Messages;
use common\models\Tag;
use common\models\PostBasic;
use common\models\SearchHistory;
use common\models\View;
use common\models\City;
use common\models\Member;
use common\services\HewaApi;
use yii\base\Action;
use yii;

class Index extends Action
{
    public function run()
    {
        $this->controller->layout = '@frontend/views/layouts/main_h5.php';

        $qrCode = [
            'huawei'    => Attachment::completeUrl('/img/home/qrcode/QRCode_Huawei_v1.png'),
            'xiaomi'    => '',
            'ios'     => '',
        ];

        return $this->controller->render(
            'index', [
                'qrCode'                   => $qrCode,
            ]
        );
    }


}