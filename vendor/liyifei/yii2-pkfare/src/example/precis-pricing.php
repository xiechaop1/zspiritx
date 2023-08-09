<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午10:27
 */

require 'common.php';

$journeys = [
    'journey_0' => [
        new \ota\pkfare\libs\params\Journey([
            'airline' => 'CX',
            'flightNum' => '883',
            'arrival' => 'HKG',
            'arrivalDate' => '2019-09-12',
            'arrivalTime' => '05:45',
            'departure' => 'LAX',
            'departureDate' => '2019-09-10',
            'departureTime' => '23:30',
            'bookingCode' => 'H'
        ])
    ]
];


$data = new \ota\pkfare\libs\params\PrecisePricing([
    'adults' => 1,
    'children' => 1,
    'cabin' => 'W',
    'journeys' => $journeys
]);

$driver = $app->driver('precise-pricing');

print_r($driver->request($data));