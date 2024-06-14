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

class Poem extends \common\models\Poem
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [['poem_type', 'level', 'poem_class', 'poem_class2', 'created_at', 'updated_at'], 'integer'],
            [['title', 'content', 'story', 'image', 'author', 'content', 'age', 'prop'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\Poem::find();
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

        if (!empty($this->poem_type)) {
            $query->andFilterWhere([
                'poem_type' => $this->poem_type,
            ]);
        }

        if (!empty($this->poem_class)) {
            $query->andFilterWhere([
                'poem_class' => $this->poem_class,
            ]);
        }

        if (!empty($this->poem_class2)) {
            $query->andFilterWhere([
                'poem_class2' => $this->poem_class2,
            ]);
        }

        if (!empty($this->level)) {
            $query->andFilterWhere([
                'level' => $this->level,
            ]);
        }

        $query->andFilterWhere([
            'like', 'title', $this->title
        ]);

        $query->andFilterWhere([
            'like', 'content', $this->content
        ]);

        $query->andFilterWhere([
            'like', 'story', $this->story,
        ]);


        return $dataProvider;
    }
}