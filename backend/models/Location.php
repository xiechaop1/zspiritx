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

class Location extends \common\models\Location
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [['is_delete', 'status', 'created_at', 'updated_at'], 'integer'],
            [['location_name', 'location_type', 'address',
                'businessarea', 'adcode', 'tel', 'aoi_type',
                'amap_ret', 'amap_prop', 'resource', ], 'string'],
            [['lng','lat'], 'number']
        ];
    }

    public function search($params)
    {
        $query = \common\models\Location::find();
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
            'like', 'location_name', $this->location_name
        ]);

        $query->andFilterWhere([
            'like', 'amap_prop', $this->amap_prop
        ]);

        $query->andFilterWhere([
            'like', 'location_type', $this->location_type
        ]);

        return $dataProvider;
    }
}