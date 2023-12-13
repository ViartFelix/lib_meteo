<?php

namespace Viartfelix\Freather\common;

use Viartfelix\Freather\config\{
    Cache,
    Config,
};

use Viartfelix\Freather\Exceptions\FreatherException;

trait LatlongService
{
    public function isInRange(float $latitude, float $longitude): bool
    {
        if( -90 > $latitude || 90 < $latitude ) throw new FreatherException("The latitude is outside the valid range. (Value of latitude: ".$latitude.")");
        if( -180 > $longitude || 180 < $longitude) throw new FreatherException("The longitude is outside the valid range. (Value of longitude: ".$longitude.")");

        return true;
    }
}

?>