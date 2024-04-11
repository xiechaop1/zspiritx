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

class Models extends \common\models\Models
{


    public function rules()
    {
        return [
            [['is_active', 'width', 'height', 'length', 'is_delete', 'created_at', 'updated_at'], 'integer'],
            [['model_name'], 'string', 'max' => 32],
            [['model_uri', 'model_desc', 'model_u_id'], 'string'],
        ];
    }

    public function exec()
    {
        return $this->save();
    }

    public function search($params)
    {
        $query = \common\models\Models::find();
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

        if (!$this->model_name) {
            $query->andFilterWhere(['like', 'model_name', $this->model_name]);
        }

        return $dataProvider;
    }
}