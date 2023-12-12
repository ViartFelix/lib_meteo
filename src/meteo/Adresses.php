<?php

namespace Viartfelix\Freather\meteo;

abstract class Adresses
{
    function city(string $city): string
    {
        return $city;
    }

    function countryCode(string $code): string
    {
        return $code;
    }

    function stateCode(string $code): string
    {
        return $code;
    }

    function cityID(string $cityID): string
    {
        return "";
    }

    function all(string $city, string $countryCode, string $stateCode)
    {
        return array(
            "city" => $city,
            "c_code" => $countryCode,
            "s_code" => $stateCode,
        );
    }
}

?>