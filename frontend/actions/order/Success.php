<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/5
 * Time: 3:49 PM
 */

namespace frontend\actions\order;

use yii\base\Action;

class Success extends Action
{
    public function run()
    {
        return $this->controller->render('success', [
            'is_succ' => true,
            'tips'  => '',
        ]);

    }
}