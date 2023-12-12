<?php

namespace Viartfelix\Freather\meteo;

class Adresses
{

    private ?string $city;
    private ?string $countryCode;
    private ?string $stateCode;
    private ?string $cityID;
    private ?string $zipCode;

    public function __construct(string $city=null, string $countryCode=null, string $stateCode=null, string $cityID=null, string $zipCode=null)
    {
        $this->city = $city ?? null;
        $this->countryCode = $countryCode ?? null;
        $this->stateCode = $stateCode ?? null;
        $this->cityID = $cityID ?? null;
        $this->zipCode = $zipCode ?? null;
    }

    public function city(string $city): Adresses
    {
        $this->city = $city;
        return $this;
    }

    public function countryCode(string $code): Adresses
    {
        $this->countryCode = $code;
        return $this;
    }

    public function stateCode(string $code): Adresses
    {
        $this->stateCode = $code;
        return $this;
    }

    public function cityID(string $cityID): Adresses
    {
        $this->cityID = $cityID;
        return $this;
    }

    public function zipCode(string $zipCode): Adresses
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function toArray(): array
    {
        return array(
            "city" => $this->city ?? null,
            "countryCode" => $this->countryCode ?? null,
            "stateCode" => $this->stateCode ?? null,
            "cityID" => $this->cityID ?? null,
            "zipCode" => $this->zipCode ?? null,
        );
    }
}

?>