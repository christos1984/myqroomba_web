<?php

namespace MyQRoomba\Libs;

/**
 * Helper class to perform transpose on a PHP array
 * so it can be accessed by invoking $array[x,y] instead
 * of $array[y,x]
 *
 * @author Christos Patsatzis
 */
class ArrayTransposer
{
    /**
     * Main function to perform the transposition
     *
     * @param array $arr array to be transposed
     *
     * @return array $retData transposed array
     */
    public static function transposeArray(array $arr)
    {
        $retData = array();

        foreach ($arr as $row => $columns) {
            foreach ($columns as $row2 => $column2) {
                $retData[$row2][$row] = $column2;
            }
        }

        return $retData;
    }
}
