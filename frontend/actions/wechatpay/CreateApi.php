<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\wechatpay;


use common\definitions\ErrorCode;
use common\models\Order;
use common\models\Story;
use common\models\StoryExtend;
use common\models\User;
use frontend\actions\ApiAction;
use WechatPay\GuzzleMiddleware\Util\AesUtil;
use Yii;

class CreateApi extends ApiAction
{
    public $action;
    private $_get;
    private $_storyId;
    private $_userId;

    private $_storyInfo;

    private $_userInfo;

    public function run()
    {

        try {
//            $this->valToken();

            $this->_get = Yii::$app->request->get();

//            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
//
//            if (empty($this->_userId)) {
//                return $this->fail('请您给出用户信息', ErrorCode::USER_NOT_FOUND);
//            }
//
//            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'jsapi':
                    $ret = $this->jsapi();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->success($ret);
    }


    /**
     * 微信支付回调
     * @return array
     * @throws \yii\db\Exception
     */
    public function jsapi() {
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;



    }



}