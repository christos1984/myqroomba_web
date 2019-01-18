<?php

namespace MyQRoomba\Entities;

/**
 * Implementation of Robot as domain object.
 *
 * Main object that our application is interacting with, works with an instance of Room and other initialization
 * data (such as cost of operation and back off strategy data that are configurable per app level and set via setters)
 *
 * There are some hardcoded assumption is this class (like the horizon points names that could be configurable) that
 * it would make sense to be configurable; for the sake of this app though, they are OK.
 *
 * @author Christos Patsatzis
 */
class Robot
{
    /**
     * @var \MyQRoomba\Entities\Room
     */
    private $room;

    /**
     * $var string
     */
    public $currentXPosition;

    /**
     * $var string
     */
    public $currentYPosition;

    /**
     * $var string
     */
    public $currentDirection;

    /**
     * $var string
     */
    public $battery;

    /**
     * $var array
     */
    private $costOfOperation;

    /**
     * $var array
     */
    private $backoffStrategy;

    /**
     * $var array
     */
    private $executionResult = [];

    /**
     * setter for costOfOperation
     *
     * @param array $costOfOperation
     */
    public function setCostOfOperation(array $costOfOperation)
    {
        $this->costOfOperation = $costOfOperation;
    }

    /**
     * getter for costOfOperation
     *
     * @return array $costOfOperation
     */
    public function getCostOfOperation()
    {
        return $this->costOfOperation;
    }

    /**
     * setter for backoffStrategy
     *
     * @param array $backoffStrategy
     */
    public function setBackOffStrategy(array $backoffStrategy)
    {
        $this->backoffStrategy = $backoffStrategy;
    }

    /**
     * getter for backoffStrategy
     *
     * @return array $backoffStrategy
     */
    public function getBackOffStrategy()
    {
        return $this->backOffStrategy;
    }

    /**
     * setter for room
     *
     * @param \MyQRoomba\Entities\Room $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * getter for room
     *
     * @return \MyQRoomba\Entities\Room $room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Checks if the robot's battery is sufficient for execution
     * of the current command
     *
     * @param string $command   The command to be executed
     *
     * @return bool
     */
    public function checkIfEnoughBatteryForCommand(string $command)
    {
        switch ($command) {
            case 'TL':
            case 'TR':
                return ($this->battery >= $this->costOfOperation['TR']) ? true : false;
                break;

            case 'A':
                return ($this->battery >= $this->costOfOperation['A']) ? true : false;
                break;
            case 'B':
                return ($this->battery >= $this->costOfOperation['B']) ? true : false;
                break;
            case 'C':
                return ($this->battery >= $this->costOfOperation['C']) ? true : false;
                break;
        }
    }

    /**
     * Recalculates battery level after command execution
     *
     * @param string $command   The command to be executed
     *
     */
    public function recalculateBattery(string $command)
    {
        $this->battery = $this->battery - $this->costOfOperation["$command"];
    }

    /**
     * Moves the robot towards the direction received. If the action can be performed
     * the cell is added to visited.
     *
     * @param string $direction   The direction that the robot should move to
     *
     * @return bool
     */
    public function move(string $direction)
    {
        switch ($direction) {
            case 'E':
                $newXposition = $this->currentXPosition + 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    ++$this->currentXPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            case 'W':
                $newXposition = $this->currentXPosition - 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    --$this->currentXPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            case 'S':
                $newYposition = $this->currentYPosition + 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    ++$this->currentYPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            case 'N':
                $newYposition = $this->currentYPosition - 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    --$this->currentYPosition;
                    $this->addCellToVisited();

                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return true;
                break;
        }
    }

    /**
     * Method to start the initiation of the back off strategy
     * once the robot cannot peform the advance movement.
     *
     * Since the back off strategy is a sequence of steps, this function breaks down
     * the strategy to each sequence, calls executeBackOffCommandSequence() and checks
     * if this sequence worked. If not, this process is repeated. If none of the sequence
     * works, then false is returned.
     *
     * @return $bool
     *
     */
    public function initiateBackOffStrategy()
    {
        foreach ($this->backoffStrategy as $strategySteps) {
            if ($this->executeBackOffCommandSequence($strategySteps)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Moves the robot one cell back, based on his current facing direction
     *
     * It will retun false if the movement cannot be performed.
     *
     * @param string $direction    The direction that the robot is facing
     *
     * @return $bool
     *
     */
    private function back(string $direction)
    {
        switch ($direction) {
            case 'E':
                $newXposition = $this->currentXPosition - 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    --$this->currentXPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            case 'W':
                $newXposition = $this->currentXPosition + 1;
                if ($this->room->isCellVisitable($newXposition, $this->currentYPosition)) {
                    --$this->currentXPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            case 'S':
                $newYposition = $this->currentYPosition - 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    --$this->currentYPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            case 'N':
                $newYposition = $this->currentYPosition + 1;
                if ($this->room->isCellVisitable($this->currentXPosition, $newYposition)) {
                    ++$this->currentYPosition;
                    $this->addCellToVisited();
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return true;
                break;
        }
    }

    /**
     * Method to execute all of the commands inside a back off command sequence
     * once the robot cannot peform the advance movement.
     *
     * If something fails in the middle of the sequence, none of the steps following
     * should be performed (i.e. a B command fails due to column)
     *
     * @param array $strategySteps  The array of commands that should be executed
     *
     * @return $bool
     *
     */
    public function executeBackOffCommandSequence(array $strategySteps)
    {
        $status = true;
        // start with the first set
        foreach ($strategySteps as $command) {
            $breakStatus = false;
            if ($this->checkIfEnoughBatteryForCommand($command)) {
                switch ($command) {
                    case 'TL':
                    case 'TR':
                        $this->changeDirection($command);
                        $this->recalculateBattery($command);
                        break;

                    case 'A':
                        $this->recalculateBattery($command);
                        if ($this->move($this->currentDirection)) {
                            break;
                        } else {
                            $status = false;
                            $breakStatus = true;
                        }
                        break;
                    case 'B':
                          $this->recalculateBattery($command);
                          if ($this->back($this->currentDirection)) {
                              break;
                          } else {
                              $status = false;
                              $breakStatus = true;
                          }
                        break;
                    case 'C':
                        $this->addCellToCleaned();
                        $this->recalculateBattery($command);
                        break;
                }
            } else {
                break;
            }
            if ($breakStatus === true) break;
        }


        return $status;
    }

    /**
     * Main function for executing a command
     *
     * In case an Advance 'A' command fails the back off sequence is initiated
     *
     * @param string $command  The command to be executed
     *
     * @return $bool
     *
     */
    public function executeCommand(string $command)
    {
        if ($this->checkIfEnoughBatteryForCommand($command)) {
            switch ($command) {
                case 'TL':
                case 'TR':
                    $this->changeDirection($command);
                    $this->recalculateBattery($command);
                    return true;
                    break;

                case 'A':
                    $this->recalculateBattery($command);
                    if ($this->move($this->currentDirection)) {
                        return true;
                        break;
                    } else {
                        if ($this->initiateBackOffStrategy() === false){
                            return false;
                        }
                    }
                    // This will never be in the valid commands sequence - it makes a bit of our lives easier in testing
                case 'B':
                    return ($this->battery >= $this->costOfOperation['B']) ? true : false;
                    break;
                case 'C':
                    $this->addCellToCleaned();
                    $this->recalculateBattery($command);
                    return true;
                    break;
            }
            return true;

        } else {
            return false;
        }

    }

    /**
     * Executing the received command sequence by breaking down the array and
     * executing one by one.
     *
     * In case the execute command fails , halt the program and return us the results
     *
     * @param array $commands  The commands to be executed
     *
     * @return array
     */
    public function executeCommandSequence(array $commands)
    {
        // irregardless of the command, we are going to have to add the current
        // cell to visited ones
        $this->addCellToVisited();
        foreach ($commands as $command) {
            if ($this->executeCommand($command) === false) {
                return $this->returnResults();
            }
        }

        return $this->returnResults();
    }

    /**
     * Method to populate the results table with current robot position, battery,
     * cells visited and cells cleaned.
     *
     * @return array
     */
    public function returnResults()
    {
        $this->executionResult['final']['X'] = $this->currentXPosition;
        $this->executionResult['final']['Y'] = $this->currentYPosition;
        $this->executionResult['final']['facing'] = $this->currentDirection;
        $this->executionResult['battery'] = $this->battery;
        if (isset($this->executionResult['visited'])) {
            array_multisort($this->executionResult['visited'], SORT_ASC);
        }
        if (isset($this->executionResult['cleaned'])) {
            array_multisort($this->executionResult['cleaned'], SORT_ASC);
        }

        return $this->executionResult;
    }

    /**
     * Method to change the direction of the robot left or right
     *
     * @param string $command   The command to change direction (left or right)
     *
     */
    private function changeDirection(string $command)
    {
        switch ($command) {
            case 'TL':
                if ('E' === $this->currentDirection) {
                    $this->currentDirection = 'N';
                } elseif ('N' === $this->currentDirection) {
                    $this->currentDirection = 'W';
                } elseif ('W' === $this->currentDirection) {
                    $this->currentDirection = 'S';
                } elseif ('S' === $this->currentDirection) {
                    $this->currentDirection = 'E';
                }
                break;
            case 'TR':
                if ('E' === $this->currentDirection) {
                    $this->currentDirection = 'S';
                } elseif ('N' === $this->currentDirection) {
                    $this->currentDirection = 'E';
                } elseif ('W' === $this->currentDirection) {
                    $this->currentDirection = 'N';
                } elseif ('S' === $this->currentDirection) {
                    $this->currentDirection = 'W';
                }
                break;
        }
    }

    /**
     * Method to add the current cell to visited ones in the internal array of the robot
     *
     * @return bool
     */
    public function addCellToVisited()
    {
        if (!$this->isCellVisited($this->currentXPosition, $this->currentYPosition)) {
            $this->executionResult['visited'][] = ['X' => $this->currentXPosition, 'Y' => $this->currentYPosition];
            return true;
        }
        else return false;
    }


    /**
     * Method to add the current cell to cleaned ones in the internal array of the robot
     *
     * @return bool
     */
    public function addCellToCleaned()
    {
        if (!$this->isCellCleaned($this->currentXPosition, $this->currentYPosition)) {
            $this->executionResult['cleaned'][] = ['X' => $this->currentXPosition, 'Y' => $this->currentYPosition];
            return true;
        }
        else return false;
    }

    /**
     * Method that returns if the current cell is  visited or not
     *
     * @param string $x     The X coordinate
     * @param string $y     The Y coordinate
     *
     * @return bool
     */
    public function isCellVisited($x, $y)
    {
        if (isset($this->executionResult['visited'])) {
            foreach ($this->executionResult['visited'] as $cell) {
                if (($cell['X'] == $x) && ($cell['Y'] == $y)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Method that returns if the current cell is cleaned or not
     *
     * @param string $x     The X coordinate
     * @param string $y     The Y coordinate
     *
     * @return bool
     */
    private function isCellCleaned($x, $y)
    {
        if (isset($this->executionResult['cleaned'])) {
            foreach ($this->executionResult['cleaned'] as $cell) {
                if (($cell['X'] == $x) && ($cell['Y'] == $y)) {
                    return true;
                }
            }
        }

        return false;
    }
}
