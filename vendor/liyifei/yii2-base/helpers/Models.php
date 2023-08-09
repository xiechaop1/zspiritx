<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/2/11
 * Time: 上午11:31
 */

namespace liyifei\base\helpers;


use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Models
{

    /**
     * @desc 获取MODEL SAVE后第一个错误内容
     * @param Model $model
     * @return string
     */
    public static function getModelFirstError(Model $model)
    {
        if ($errors = $model->getFirstErrors()) {
            return ArrayHelper::getValue(array_values($errors), 0, '');
        }

        return '';
    }

    /**
     * @desc 数据友好创建时间
     * @param ActiveRecord $record
     * @return false|string
     */
    public static function friendlyCreatedAt(ActiveRecord $record)
    {
        return Time::friendly($record->created_at);
    }

}