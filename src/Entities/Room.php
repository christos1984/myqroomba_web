<?php

namespace MyQRoomba\Entities;

/**
 * Implementation of Room as domain object.
 *
 * Contains variables that hold the depiction of the room as a matrix,
 * constants for different types of cells and methods to interact with
 * these data.
 *
 * @author Christos Patsatzis
 */
class Room
{
    /**
     * @var array
     */
    private $matrix;

    const CLEANABLE = 'S';
    const WALL = 'null';
    const COLUMN = 'C';

    /**
     * Setter for $matrix
     *
     * @param array $matrix
     *
     * @return array $matrix
     */
    public function setMatrix($matrix)
    {
        $this->matrix = $matrix;

        return $this;
    }

    /**
     * Getter for $matrix
     *
     * @return array $matrix
     */
    public function getMatrix()
    {
        return $this->matrix;
    }

    /**
     * Method to determine if a cell on the matrix is visitable
     * this means no wall and no column
     *
     * @param string $dimensionX The X dimension
     * @param string $dimensionY The Y dimension
     *
     * @return bool
     */
    public function isCellVisitable(string $dimensionX, string $dimensionY)
    {
        if (isset($this->matrix["$dimensionX"]["$dimensionY"])) {
            if ((self::WALL !== $this->matrix["$dimensionX"]["$dimensionY"]) && (self::COLUMN !== $this->matrix["$dimensionX"]["$dimensionY"])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
