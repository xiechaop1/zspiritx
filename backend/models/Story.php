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

class Story extends \common\models\Story
{

    public $date_range;

    public function rules()
    {
        return [
            [['title', 'desc', 'guide', 'story_bg', 'thumbnail', 'cover_image', 'image', 'latest_unity_version', ], 'string'],
            [['persons_ct', 'roles_ct', 'story_type', 'status', 'is_debug', 'story_status', 'sort_by'], 'integer'],
            [['resources'], 'string'],
            [[ 'created_at', 'updated_at',], 'integer'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\Story::find();
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