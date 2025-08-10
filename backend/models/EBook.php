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

class EBook extends \common\models\EBook
{

    public $date_range;

    public function rules()
    {
        return [
            [['story_id',  ], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['story_params', ], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\EBook::find();
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

        return $dataProvider;
    }
}