<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/09/02
 * Time: 下午5:30
 */

namespace backend\models;


use common\definitions\Common;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class User extends \common\models\User
{

    public function rules()
    {
        return [
            [['user_status', 'status', 'last_login_time', 'wx_token_expire_time', 'created_at', 'updated_at'], 'integer'],
            [['user_name', 'user_pass', 'nick_name', 'user_pass', 'wx_openid', 'wx_unionid', 'wx_token', 'mobile', 'avatar', ], 'string'],
            [['last_login_geo_lat', 'last_login_geo_lng'], 'number'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => false
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'like', 'mobile', $this->mobile
        ]);


        // 判断ID
        if (!empty($params['User']['id'])) {
            $query->andFilterWhere([
                'id' => $params['User']['id']
            ]);
        }


        // 判断用户状态
        if (isset($params['User']['user_status']) && $params['User']['user_status'] >= 0) {
            $query->andFilterWhere([
                'user_status'  => $params['User']['user_status']
            ]);
        }

        return $dataProvider;
    }
}