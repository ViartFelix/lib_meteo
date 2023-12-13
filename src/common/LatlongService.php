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

    /**
     * compileOptions
     * compileOptions will try to chery-pick options and give them back, to prevent unnecessary options.
     */
    public function compileOptions(array $options, Config $config): array
    {
        $finalArray = array();

        //Latitude and logitude
        $finalArray["lat"] = $options["lat"];
        $finalArray["lon"] = $options["lon"];

        //Common options
        $finalArray["appid"] = $config->getApiKey();
        $finalArray["units"] = $config->getUnit() ?? "standard";
        $finalArray["lang"] = $config->getLang() ?? "en";
        $finalArray["cnt"] = $config->getTimestamps() ?? 1;

        return $finalArray;
    }
}

?>