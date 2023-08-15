<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%poi}}".
 *
 * @property int $id
 * @property string $poi_name POI名称
 * @property int $created_at
 * @property int $updated_at
 */
class Poi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%poi}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_id', 'created_at', 'updated_at'], 'integer'],
            [['poi_name'], 'string'],
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
