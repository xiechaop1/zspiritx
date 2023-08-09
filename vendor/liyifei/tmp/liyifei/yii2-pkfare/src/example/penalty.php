<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/15
 * Time: 4:35 PM
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


$data = new \ota\pkfare\libs\params\Penalty([
    'journeys' => $journeys
]);

$driver = $app->driver('penalty');

print_r($driver->request($data));