<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%building}}".
 *
 * @property int $id
 * @property string $building_name 建筑名
 * @property int $created_at
 * @property int $updated_at
 */
class Building extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%building}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['building_name'], 'string'],
            [['lng','lat'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_type' => 'Tag Type',
            'tag_name' => 'Tag Name',
            'level'     => 'Leve',
            'parent_id' => 'Parent Id',
            'deleted_at'    => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
