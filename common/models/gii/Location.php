<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%location}}".
 *
 * @property int $id
 * @property string $poi_name POI名称
 * @property int $created_at
 * @property int $updated_at
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_delete', 'status', 'created_at', 'updated_at'], 'integer'],
            [['location_name', 'location_type', 'address',
                'businessarea', 'adcode', 'tel', 'aoi_type',
                'amap_ret', 'amap_prop', 'resource', ], 'string'],
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
            'poi_name' => 'Poi Name',
            'deleted_at'    => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
