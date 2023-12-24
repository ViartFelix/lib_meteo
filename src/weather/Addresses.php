<?php

namespace Viartfelix\Freather\weather;

/**
 * The addresses service.
 * Very handy for getting the weather at a precise city or place.
 */
class Addresses
{
    private ?string $city;
    private ?string $countryCode;
    private ?string $stateCode;
    private ?string $cityID;
    private ?string $zipCode;

    /**
     * Constructor for addresses service.
     * Will allow you to query various locations stored among Openweathermap's database (approx. 200 000 cities).
     *
     * <hr/>
     *
     * For parsing the query params in this system, a priority order is set such that Freather can contact easily with desired params Openweathermap. This order is 3 distinct 'modes': <br/>
     *  *Please note that these roles are in order of priority used by Freather and are not compatible with each other at all.*
     *
     * <ul>
     *      <li>Mode 1: city, countryCode and stateCode. (you can use any of those methods, and a method 'mode1' is available if you want to input all 3 at the same time.)</li>
     *      <li>Mode 2: cityID</li>
     *      <li>Mode 3: zipCode, countryCode</li>
     * </ul>
     *
     * <hr/>
     *
     * @param string|null $city The city name you want to get weather for. Optional parameter. It Can be combined (and will be used) with '$countryCode' and '$stateCode' params for more precision.
     * @param string|null $countryCode The country code. Optional. This param must be [ISO-3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible if used, or else Openweathermap is not going to recognize it. Can be combined (and will be used) with '$city' and '$stateCode' params for more precision.
     * @param string|null $stateCode The state code. Only for US. Optional. This param must be [ISO-3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible if used, or else Openweathermap is not going to recognize it. Can be combined (and will be used) with '$countrycode' and '$stateCode' params for more precision.
     * @param string|null $cityID The city ID. A list of all city IDs can be found online [here](https://public.opendatasoft.com/explore/dataset/geonames-all-cities-with-a-population-1000/table) or can be downloaded [here](http://bulk.openweathermap.org/sample/). Optional.
     * @param string|null $zipCode The ZIP code (only for US). Optional. Specific country is not important, as Openweathermap will assume and use the US is the chosen country.
     *
     * @return void
     */
    public function __construct(string $city=null, string $countryCode=null, string $stateCode=null, string $cityID=null, string $zipCode=null)
    {
        $this->city = $city ?? null;
        $this->countryCode = $countryCode ?? null;
        $this->stateCode = $stateCode ?? null;
        $this->cityID = $cityID ?? null;
        $this->zipCode = $zipCode ?? null;
    }

    /**
     * Defines a city to be used when using the Addresses system.
     * Part of the first mode out of the 3 available. <br/>
     *
     * Can be used with those other methods in the query:
     * <ul>
     *      <li>countryCode</li>
     *      <li>stateCode</li>
     * </ul>
     *
     * <hr/>
     *
     * @param string $city The city name.
     * @return Addresses The current Addresses instance.
     */
    public function city(string $city): Addresses
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Defines a country code to be used when using the Addresses system.
     *
     * Part of the first mode out of the 3 available. <br/>
     *
     * Can be used with those other methods in the query:
     * <ul>
     *       <li>city</li>
     *       <li>stateCode</li>
     * </ul>
     *
     * <hr/>
     *
     * This param must be [ISO-3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible if used, or else Openweathermap is not going to recognize it.
     *
     * <hr/>
     *
     * @param string $code The country code.
     * @return Addresses The current Addresses instance.
     */
    public function countryCode(string $code): Addresses
    {
        $this->countryCode = $code;
        return $this;
    }

    /**
     * Defines a state code to be used when using the Addresses system. <br/>
     * Beware, this option is only available for the US.
     *
     * Part of the first mode out of the 3 available. <br/>
     *
     * Can be used with those other methods in the query:
     * <ul>
     *      <li>countryCode</li>
     *      <li>city</li>
     * </ul>
     *
     * <hr/>
     *
     * This param must be [ISO-3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible if used, or else Openweathermap is not going to recognize it.
     *
     * <hr/>
     *
     * @param string $code The state code (only for the US).
     * @return Addresses The current Addresses instance.
     */
    public function stateCode(string $code): Addresses
    {
        $this->stateCode = $code;
        return $this;
    }

    /**
     * Defines the city ID to be used when fetching data. <br/>
     * Part of the second mode out of the 3 available. <br/>
     * If any of the 3 methods of the first mode was called (city, countryCode, stateCode or mode1) Freather will prioritize the first mode out of this one if specified.
     *
     * <hr/>
     *
     * A list of all city IDs can be found online [here](https://public.opendatasoft.com/explore/dataset/geonames-all-cities-with-a-population-1000/table) or can be downloaded [here](http://bulk.openweathermap.org/sample/) <br/>
     * Note: the website given for the list of all cities list all cities with a population of over or equal to 1000 people.
     *
     * <hr/>
     *
     * @param string $cityID The city ID.
     * @return Addresses The current Addresses instance.
     */
    public function cityID(string $cityID): Addresses
    {
        $this->cityID = $cityID;
        return $this;
    }

    /**
     * Defines the US zip code to be used when fetching data. <br/>
     * Part of the third mode out of the 3 available. <br/>
     * If any of the 3 methods of the first mode (city, countryCode, stateCode or mode1) or cityID was called, Freather will prioritize the first mode out of this one if specified, and the second mode (cityID) if the first mode is not specified.
     *
     * <hr/>
     *
     * Can be used with those other methods in the query:
     * <ul>
     *      <li>countryCode</li>
     * </ul>
     *
     * <hr/>
     *
     * @param string $zipCode The US zip code to be used.
     * @return $this The current Addresses instance.
     */
    public function zipCode(string $zipCode): Addresses
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * The first mode out of the 3 available.
     * Same as doing the following methods in one:
     * <ul>
     *      <li>city</li>
     *      <li>countryCode</li>
     *      <li>stateCode</li>
     * </ul>
     *
     * <hr/>
     *
     * @param string|null $city The city name. Optional.
     * @param string|null $countryCode The country code. Must be [ISO 3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible. Optional
     * @param string|null $stateCode The state code. Must be [ISO 3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible. Optional
     * @return Addresses The current Addresses instance.
     */
    public function mode1(string $city = null, string $countryCode=null, string $stateCode=null): Addresses
    {
        $this->city = $city ?? null;
        $this->countryCode = $countryCode ?? null;
        $this->stateCode = $stateCode ?? null;

        return $this;
    }

    /**
     * The second mode out of the 3 available. Exactly the same as the method 'cityID'.
     *
     * <hr/>
     *
     * @param string $cityID The city ID.
     * @return $this The current Addresses instance.
     */
    public function mode2(string $cityID): Addresses
    {
        $this->cityID = $cityID;
        return $this;
    }

    /**
     * The second mode ou of the 3 available. Excactly the same as doing the following methods:
     *
     * <ul>
     *      <li>zipCode</li>
     *      <li>countryCode</li>
     * </ul>
     *
     * <hr/>
     *
     * @param string|null $zipCode The US zip code to be used.
     * @param string|null $countryCode The country code.
     * @return $this The current Addresses instance.
     */
    public function mode3(string $zipCode = null, string $countryCode = null): Addresses
    {
        $this->zipCode = $zipCode ?? null;
        $this->countryCode = $countryCode ?? null;

        return $this;
    }

    /**
     * Will return all the infos you gave to the Addresses as an associative array. Can be used as a var_dumper if you wish so.
     *
     * <hr/>
     *
     * @return array All the infos on the Addresses system. Used by Freather to easily get what you inputted to fetch data.
     */
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