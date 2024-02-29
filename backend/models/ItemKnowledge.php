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

class ItemKnowledge extends \common\models\ItemKnowledge
{

    public $date_range;
    
    public function rules()
    {
        return [
            [['item_id', 'item_type', 'knowledge_id', 'story_id'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['knowledge_set_status'], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\ItemKnowledge::find();
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

        if (!empty($this->knowledge_id)) {
            $query->andFilterWhere(['knowledge_id' => $this->knowledge_id]);
        }

        if (!empty($this->item_id)) {
            $query->andFilterWhere(['item_id' => $this->item_id]);
        }

        if (!empty($this->item_type)) {
            $query->andFilterWhere(['item_type' => $this->item_type]);
        }

        return $dataProvider;
    }
}