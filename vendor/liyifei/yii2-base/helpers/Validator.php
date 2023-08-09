<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/6/25
 * Time: 下午2:32
 */

namespace liyifei\base\helpers;


use yii\validators\EmailValidator;

class Validator
{
    /**
     * @desc validate mobile
     * @param $mobile
     * @return bool
     */
    public static function isMobile($mobile)
    {
        return (bool)preg_match('/1\d{10}/', $mobile);
    }

    /**
     * @desc validate email
     * @param $email
     * @return bool
     */
    public static function isEmail($email)
    {
        $validator = new EmailValidator();
        return $validator->validate($email);
    }
}