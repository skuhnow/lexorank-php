<?php

namespace SKuhnow\LexoRank;

class LexoHelper
{

    public static function arrayCopy($sourceArray, $sourceIndex, &$destinationArray, $destinationIndex, $length)
    {
        $destination = $destinationIndex;
        $finalLength = $sourceIndex + $length;
        for ($i = $sourceIndex; $i < $finalLength; $i++) {
            $destinationArray[$destination] = $sourceArray[$i];
            $destination++;
        }
    }


}
