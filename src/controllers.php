<?php
// DIC configuration

$container = $app->getContainer();

$container['RobotController'] = function ($c) {
    return new MyQRoomba\Controllers\RobotController($c);
};
