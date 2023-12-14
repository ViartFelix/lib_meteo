<?php

namespace Viartfelix\Freather\weather;

class Addresses
{

    private ?string $city;
    private ?string $countryCode;
    private ?string $stateCode;
    private ?string $cityID;
    private ?string $zipCode;

    /**
     * Beware, stateCode is only avaliable to the US
     */
    public function __construct(string $city=null, string $countryCode=null, string $stateCode=null, string $cityID=null, string $zipCode=null)
    {
        $this->city = $city ?? null;
        $this->countryCode = $countryCode ?? null;
        $this->stateCode = $stateCode ?? null;
        $this->cityID = $cityID ?? null;
        $this->zipCode = $zipCode ?? null;
    }

    public function city(string $city): Addresses
    {
        $this->city = $city;
        return $this;
    }

    public function countryCode(string $code): Addresses
    {
        $this->countryCode = $code;
        return $this;
    }

    /**
     * Beware, stateCode is only avaliable to the US
     */
    public function stateCode(string $code): Addresses
    {
        $this->stateCode = $code;
        return $this;
    }

    public function cityID(string $cityID): Addresses
    {
        $this->cityID = $cityID;
        return $this;
    }

    public function zipCode(string $zipCode): Addresses
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function mode1(string $city, string $countryCode=null, string $stateCcode=null)
    {
        $this->city = $city;
        $this->countryCode = $countryCode ?? null;
        $this->stateCode = $stateCcode ?? null;
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