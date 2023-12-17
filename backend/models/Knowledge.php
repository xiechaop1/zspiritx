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
            [[ 'story_id', 'pre_knowledge_id', 'sort_by', 'is_delete', 'story_stage_id', 'knowledge_class', 'knowledge_type', 'created_at', 'updated_at'], 'integer'],
            [['title', 'content', 'suggestion', 'voice', 'linkurl', 'image' ], 'string'],
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
            'story_stage_id' => $this->story_stage_id,
        ]);

        $query->andFilterWhere([
            'like', 'title', $this->title
        ]);

        $query->andFilterWhere([
            'story_id' => $this->story_id,
        ]);

        if (!empty($this->knowledge_class)) {
            $query->andFilterWhere([
                'knowledge_class' => $this->knowledge_class,
            ]);
        }

        if (!empty($this->knowledge_type)) {
            $query->andFilterWhere([
                'knowledge_type' => $this->knowledge_type,
            ]);
        }

        return $dataProvider;
    }
}