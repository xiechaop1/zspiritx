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

class QaPackage extends \common\models\QaPackage
{
    public $date_range;

    public $category_ids;

    public function rules()
    {
        return [
            [['package_type', 'package_class', 'story_id', 'grade', 'level',
                'link_story_model_id', 'package_status',
                'created_at', 'updated_at'], 'integer'],
            [['package_name', 'qa_ids', 'package_desc', 'prop', ], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\QaPackage::find();
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
            'like', 'package_name', $this->package_name
        ]);
        
        $query->andFilterWhere([
            'like', 'qa_ids', ',' . $this->qa_ids . ','
        ]);

        $query->andFilterWhere([
            'story_id' => $this->story_id,
        ]);

        $query->andFilterWhere([
            'package_type' => $this->package_type,
        ]);

        $query->andFilterWhere([
            'package_class' => $this->package_class,
        ]);

        $query->andFilterWhere([
            'package_status' => $this->package_status,
        ]);

        return $dataProvider;
    }
}