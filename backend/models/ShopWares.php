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

class ShopWares extends \common\models\ShopWares
{

    public $date_range;

    public function rules()
    {
        return [
            [['ware_name', 'intro', 'icon',], 'string'],
            [['price', 'discount', ], 'number'],
            [['link_id', 'link_type', 'ware_type', 'pay_way', 'store_ct', 'ware_status', 'status'], 'integer'],
            [['is_delete', 'created_at', 'updated_at',], 'integer'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\ShopWares::find();
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

        if (!$this->validate()) {
            return $dataProvider;
        }

//        if (!empty($this->story_id)) {
//            $query->andFilterWhere(['story_id' => $this->story_id]);
//        }

        if (!empty($this->ware_name)) {
            $query->andFilterWhere(['like', 'ware_name', $this->ware_name]);
        }

        if (!empty($this->link_id)) {
            $query->andFilterWhere(['link_id' => $this->link_id]);
        }

        return $dataProvider;
    }
}