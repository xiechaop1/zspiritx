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

class Knowledge extends \common\models\Knowledge
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [[ 'story_id', 'pre_knowledge_id', 'sort_by', 'is_delete', 'knowledge_class', 'knowledge_type', 'created_at', 'updated_at'], 'integer'],
            [['title', 'content', 'voice', 'linkurl', 'image' ], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\Knowledge::find();
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
            'like', 'title', $this->title
        ]);

        $query->andFilterWhere([
            'story_id' => $this->story_id,
        ]);

        return $dataProvider;
    }
}