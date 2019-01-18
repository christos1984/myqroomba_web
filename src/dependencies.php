<?php
// DIC configuration

$container = $app->getContainer();

$container['room'] = function ($c) {    
    $room = new MyQRoomba\Entities\Room;
    return $room;
};

$container['robot'] = function ($c) {    
    
    $robot = new MyQRoomba\Entities\Robot;
    $robot->setCostOfOperation([
        'TR' => 1,
        'TL' => 1,
        'A' => 2,
        'B' => 3,
        'C' => 5,
    ]);

    $robot->setBackOffStrategy([
        ['TR', 'A'],
        ['TL', 'B', 'TR', 'A'],
        ['TL', 'TL', 'A'],
        ['TR', 'B', 'TR', 'A'],
        ['TL', 'TL', 'A'],
    ]);

    return $robot;
};