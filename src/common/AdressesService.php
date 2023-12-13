<?php

namespace Viartfelix\Freather\common;

use Viartfelix\Freather\config\{
    Cache,
    Config,
};

use Viartfelix\Freather\meteo\Adresses;

use Viartfelix\Freather\Exceptions\FreatherException;

trait AdressesService
{
    /**
     * will parse the adresses info, and guess the mode: country/city mode, citi ID mode and zip code mode
     */
    public function parseAdresses(Adresses $options): array
    {
        $finalData = array();
        $arrayOptions = $options->toArray();

        //If, in the adresses system, the first mode (city name, country code and/or state code)
        if(
            isset($arrayOptions["city"]) ||
            isset($arrayOptions["countryCode"]) ||
            isset($arrayOptions["stateCode"])
        ) {
            $finalData["q"] = $arrayOptions["city"];
            $finalData["q"] .= (isset($arrayOptions["countryCode"]) ? (",".$arrayOptions["countryCode"]): "");
            $finalData["q"] .= (isset($arrayOptions["stateCode"]) ? (",".$arrayOptions["stateCode"]): "");
        }
        //or if it's the second mode (cityID)
        else if(isset($arrayOptions["cityID"])) {
            $finalData["id"] = $arrayOptions["cityID"];
        }
        //or it's the third mode (zipCode)
        else if(isset($arrayOptions["zipCode"])) {
            $finalData["zip"] = $arrayOptions["zipCode"];
            $finalData["zip"] .= (isset($arrayOptions["countryCode"]) ? (",".$arrayOptions["countryCode"]): "");
        }
        //or else nothing corresponds
        else {
            throw new FreatherException("All the params in Adresses are empty, please make sure they are values in Adresses.", 1);
        }

        return $finalData;
    }

    public function compileAdresses(array $adresseParse, Config $config)
    {
        $finalArray = array();

        //the parsed adresses
        if(isset($adresseParse["q"])) $finalArray["q"] = $adresseParse["q"];
        if(isset($adresseParse["id"])) $finalArray["id"] = $adresseParse["id"];
        if(isset($adresseParse["zip"])) $finalArray["zip"] = $adresseParse["zip"];

        //Common options
        $finalArray["appid"] = $config->getApiKey();
        $finalArray["units"] = $config->getUnit() ?? "standard";
        $finalArray["lang"] = $config->getLang() ?? "en";
        $finalArray["cnt"] = $config->getTimestamps() ?? 1;

        return $finalArray;
    }
}

?>