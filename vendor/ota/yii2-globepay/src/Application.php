<?php
/**
 * Created by PhpStorm.
 * User: yifei
 * Date: 2019/3/17
 * Time: 22:35
 */

namespace ota\globepay;

use ota\globepay\lib\ApiTrait;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii;

class Application extends Component
{
    use ApiTrait;

    public $partnerCode;

    public $credentialCode;

    private $_classMap = [
        'unifiedorder' => 'ota\globepay\lib\drivers\GlobePayUnifiedOrder',
        'glopepayapplyrefund' => 'ota\globepay\lib\drivers\GlobePayApplyRefund',
        'globepayorderquery' => 'ota\globepay\lib\drivers\GlobePayOrderQuery',
        'globepayexchangerate' => 'ota\globepay\lib\drivers\GlobePayExchangeRate',
    ];

    public function driver($api, $extra = [])
    {
        if (empty($api) || (ArrayHelper::getValue($this->_classMap, $api, '') == '')) {
            throw new Exception('很抱歉，你输入的api不合法。');
        }
        $config = [
            'partnerCode' => $this->partnerCode,
            'credentialCode' => $this->credentialCode,
        ];
        if ($extra) {
            foreach ($extra as $key => $val) {
                $config[$key] = $val;
            }
        }
        $config['class'] = $this->_classMap[$api];
        $this->driver = Yii::createObject($config);

        return $this;
    }

    public function createNotifyAction(callable $handler)
    {
        return [
            'class' => 'ota\globepay\NotifyAction',
            'application' => $this,
            'handler' => $handler
        ];
    }
}