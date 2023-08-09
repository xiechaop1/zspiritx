<?php
/**
 * Created by PhpStorm.
 * User=> liyifei
 * Date=> 19-4-13
 * Time=> 下午9=>10
 */

require 'common.php';

$data = [
    "adults" => 1,
    "airline" => "",
    "children" => 1,
    "nonstop" => 0,
    "searchAirLegs" => [
        [
            "cabinClass" => "Economy",
            "departureDate" => "2019-08-26",
            "destination" => "LAX",
            "origin" => "HKG"
        ],
        [
            "cabinClass" => "Economy",
            "departureDate" => "2019-09-10",
            "destination" => "HKG",
            "origin" => "LAX"
        ]
    ],
    "solutions" => 0
];

/**
 * @var \ota\pkfare\behaviors\Shopping $driver
 */
$driver = $app->driver('shopping');
$result = $driver->request($data);


print_r($result);