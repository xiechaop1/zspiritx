<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/16
 * Time: 10:31 AM
 */

require 'common.php';

print_r($app->driver('globepayexchangerate', [])->exchangeRate());