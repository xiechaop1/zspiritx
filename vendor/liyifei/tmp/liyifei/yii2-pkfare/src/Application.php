<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 19-4-13
 * Time: 下午8:33
 */

namespace ota\pkfare;


use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use Yii;


class Application extends Component
{
    public $partnerId;

    public $partnerKey;

    public $debug;

    private $_classMap = [
        'shopping' => 'ota\pkfare\behaviors\Shopping',
        'precise-pricing' => 'ota\pkfare\behaviors\PrecisPricing',
        'precise-booking' => 'ota\pkfare\behaviors\PrecisBooking',
        'penalty' => 'ota\pkfare\behaviors\Penalty',
        'cancel-order' => 'ota\pkfare\behaviors\CancelOrder',
        'order-pricing' => 'ota\pkfare\behaviors\OrderPricing',
        'ticketing' => 'ota\pkfare\behaviors\Ticketing'
    ];

    public function driver($api, $extra = [])
    {
        if (empty($api) || (ArrayHelper::getValue($this->_classMap, $api, '') == '')) {
            throw new Exception('很抱歉，你输入的api不合法。');
        }
        $config = [
            'partnerId' => $this->partnerId,
            'partnerKey' => $this->partnerKey,
            'debug' => $this->debug,
        ];
        if ($extra) {
            foreach ($extra as $key => $val) {
                $config[$key] = $val;
            }
        }
        $config['class'] = $this->_classMap[$api];
        return Yii::createObject($config);
    }
}