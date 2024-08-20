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

class Qa extends \common\models\Qa
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [['qa_type', 'qa_class', 'qa_mode', 'story_id', 'knowledge_id',
                'story_stage_id', 'score', 'created_at', 'updated_at'], 'integer'],
            [['topic', 'voice', 'attachment', 'st_answer', 'st_selected', 'selected', 'prop'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\Qa::find();
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

        if (!empty($_REQUEST['Qa'])) {
            $query->andFilterWhere([
                'id' => (int)$_REQUEST['Qa']['id'],
            ]);
        }

        $query->andFilterWhere([
            'like', 'topic', $this->topic
        ]);

        $query->andFilterWhere([
            'story_id' => $this->story_id,
        ]);

        if (!empty($this->qa_type)) {
            $query->andFilterWhere([
                'qa_type' => $this->qa_type,
            ]);
        }

        if (!empty($this->qa_class)) {
            $query->andFilterWhere([
                'qa_class' => $this->qa_class,
            ]);
        }

        return $dataProvider;
    }
}