<?php

namespace MyQRoomba\Controllers;

use MyQRoomba\Libs\ArrayTransposer;

class RobotController
{
    private $container = null;

    public function __construct($container = null)
    {
        $this->container = $container;
    }

	public function getPosition($request, $response, $args)
    {
        $robot = $this->container->get('robot');

        if (!(empty($robot->currentXPosition)) && (!(empty($robot->currentYPosition)))) {
            return $response->withJson(["success" => true, "data" => ["x" => $position->currentXPosition, "y" => $robot->currentYposition]]);
        }
        else return $response->withJson(["success" => false, "message"=> "Robot has not been initalized yet", "data" => []]);
    }

    public function setPosition($request, $response, $args)
    {
        $robot = $this->container->get('robot');
        if (!(empty($robot->room))) {
            //return $response->withJson(["success" => true, "data" => ["x" => $position->currentXPosition, "y" => $robot->currentYposition]]);
        }
        else return $response->withJson(["success" => false, "message"=> "Robot has not been initalized yet", "data" => []]);
    }

    public function start($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $room = $this->container->get('room');
        $robot = $this->container->get('robot');
        $room->setMatrix(ArrayTransposer::transposeArray($data['map']));
        $robot->setRoom($room);
        $commands = $data['commands'];
        $robot->currentXPosition = $data['start']['X'];
        $robot->currentYPosition = $data['start']['Y'];
        $robot->currentDirection = $data['start']['facing'];
        $robot->battery = $data['battery'];

        $result = $robot->executeCommandSequence($commands);

        return $response->withJSON($result);
    }
}
