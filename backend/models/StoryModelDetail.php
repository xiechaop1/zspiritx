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

class StoryModelDetail extends \common\models\StoryModelDetail
{

    public function rules()
    {
        return [
            [['is_unique', 'pre_story_model_id', 'story_id', 'model_id',
                'active_type', 'direction', 'sort_by', 'status'], 'integer'],
            [['active_expiretime', 'created_at', 'updated_at',], 'integer'],
            [['active_next', 'dialog', 'title', 'story_model_image', 'icon',  ], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\StoryModelDetail::find();
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