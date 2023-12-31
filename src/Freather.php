<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\{
    Config,
    Cache,
};

use stdClass;
use Viartfelix\Freather\Exceptions\FreatherException;

use Viartfelix\Freather\weather\{
  Current,
  Forecast,
  Map,
  Addresses,
};

use Viartfelix\Freather\enums\MapLayer;

/**
 * The Freather class.
 * Before doing any fetching, you have to specify its configuration
 */
class Freather {
	private Config $config;
    private Cache $cache;
	private Current $current;
	private Map $map;
	private Forecast $forecast;

    /**
     * The Freather initializer.
     *
     * @param string $apiKey You Openweathermap's API key. You can get one [here](https://home.openweathermap.org/api_keys). Required parameter.
     * @param array $init An associative array of options you can specify that will be used when doing queries to Openweathermap. Optional parameter. Possible values are as follows:
     * <ul>
     *      <li> lang (string): The language to use from Openweathermap. Defaults to 'en'. </li>
     *      <li> measurement (string): The measurement to use. Possible values are: 'metric' (°C), 'standard' (°K), 'imperial' (°F). Defaults to 'standard' (°K) </li>
     *      <li> timestamps (int): The number of timestamps to divide when fetching the Forecast. Defaults to 0 </li>
     *
     *      <li> currentEntrypoint (string): Openweathermap's API entrypoint to use when fetching the current weather. Defaults to https://api.openweathermap.org/data/2.5/weather </li>
     *      <li> mapEntrypoint (string): Openweathermap's API entrypoint to use when constructing the map link. Defaults to http://maps.openweathermap.org/maps/2.0/weather </li>
     *      <li> forecastEntrypoint (string): Openweathermap's API entrypoint to use when fetching the forecast. Defaults to https://api.openweathermap.org/data/2.5/forecast </li>
     *
     *      <li> cacheDuration (int): The cache duration in seconds. Defaults to -1 (cache disabled) </li>
     * </ul>
     * @param int $cacheDuration The cache duration in seconds. You disable it by, either, putting -1 as a value or any other value bellow or equal to 0. If the value is higher than 0, then the cache is going to be used. See documentation on how the cache works for more details.
     */
	function __construct(
        string $apiKey,

		array $init=array(
			"lang"=>"en",
			"measurement"=>"standard",
			"timestamps"=>1,

            "currentEntrypoint"=>null,
            "mapEntrypoint"=>null,
            "forecastEntrypoint"=>null,
		),
        
        int $cacheDuration = -1,
	) {
		$this->config=new Config ([
            "apiKey"=>$apiKey,
            "lang"=>$init["lang"] ?? null,
            "measurement"=>$init["measurement"] ?? null,
            "timestamps"=>$init["timestamps"] ?? null,

            "currentEntrypoint" => $init["currentEntrypoint"] ?? null,
            "mapEntrypoint" => $init["mapEntrypoint"] ?? null,
            "forecastEntrypoint" => $init["forecastEntrypoint"] ?? null,

            "cacheDuration" => $cacheDuration ?? -1,
        ]);


        //If the user didn't specify a duration for the cache (as it is required for Phpfastcache), then it will not be enabled.
        $this->cache = new Cache($this->config->cacheDuration);

		$this->current = new Current($this->config, $this->cache);
		$this->map = new Map($this->config, $this->cache);
		$this->forecast = new Forecast($this->config, $this->cache);
    }

    /* ---------------------------------------- Class-specific methods ---------------------------------------- */

    /* ------------------------- Config ------------------------- */
    /**
     * Defines a new configuration based on the previous configuration. All params are optional and any defined configuration in the array will override the old parameter.
     * @param array $config The array of configuration to define. Works as an associative array. The possible values are as follows:
     * <ul>
     *      <li> apiKey (string): The new API key to use. </li>
     *      <li> lang (string): The language to use from Openweathermap. Defaults to 'en'. </li>
     *      <li> measurement (string): The measurement to use. Possible values are: 'metric', 'standard', 'imperial'. Defaults to 'standard' </li>
     *      <li> timestamps (int): The number of timestamps to divide when fetching the Forecast. Defaults to 0 </li>
     *
     *      <li> currentEntrypoint (string): Openweathermap's API entrypoint to use when fetching the current weather. Defaults to https://api.openweathermap.org/data/2.5/weather </li>
     *      <li> mapEntrypoint (string): Openweathermap's API entrypoint to use when constructing the map link. Defaults to http://maps.openweathermap.org/maps/2.0/weather </li>
     *      <li> forecastEntrypoint (string): Openweathermap's API entrypoint to use when fetching the forecast. Defaults to https://api.openweathermap.org/data/2.5/forecast </li>
     *
     *      <li> cacheDuration (int): The cache duration in seconds. Defaults to -1 (cache disabled) </li>
     * </ul>
     *
     * @return Freather The current Freather instance
     * @throws FreatherException
     */
	public function defineConfig(array $config): Freather
	{
		$this->config->defineConfig($config);
		return $this;
	}

    /**
     * Will roll back to the previous configuration. Throws a FreatherException if not previous config was defined.
     * @return Freather The current Freather instance
     * @throws FreatherException
     */
	public function rollbackConfig(): Freather
	{
		//We roll back the config
        $this->config->rollbackConfig();

		return $this;
	}

    /* ------------------------- Current ------------------------- */
    /**
     * Will fetch the current weather.
     *
     * <hr/>
     *
     * 2 modes are available:
     * <ul>
     *      <li> latitude (p1) and longitude (p2). The two params are required and can be of type string, int of float. </li>
     *      <li> Addresses (p1). This mode will allow you to enter different information to get weather at a city. If you want this mode, then p1 is required, but not p2. </li>
     * </ul>
     *
     * If the Addresses' system is selected, Freather will completely ignore the p2 param.
     *
     * <hr/>
     *
     * @param string|float|int|Addresses $p1 The latitude or Addresses to use. Required.
     * @param string|float|int|null $p2 The longitude. Not used if the Addresses' system is used. Will throw a FreatherException if the latitude is defined but not the longitude.
     * @param bool $raw If the response should be the raw from Openweathermap's or a basic json or xml parse.
     * @param array $options An array of key-values for optional parameters. Possible options are as follows:
     * <ul>
     *      <li>mode (string): The response method used by Openweathermap. Either json or xml. Defaults to json if not specified.</li>
     * </ul>
     *
     * @return Freather The current Freather instance
     *
     * @throws FreatherException
     */
	public function fetchCurrent(string|float|int|Addresses $p1, string|float|int|null $p2 = null, bool $raw = false, array $options=array()): Freather
	{
        $authorisedTypes = array("string","float","int","integer","double");

        if(!isset($p1)) {
            throw new FreatherException("Error when preparing query: Addresses or latitude parameter (p1) is required.", 1);
        }

        //if p1 is an address
        if($p1 instanceof Addresses)
        {
            $this->current->fetchCurrent(
            //We pass addresses to the first parameter
                $p1,
                //We don't need longitude to be passed
                null,
                //And we pass the options to the "controller" of Freather.
                $options ?? array(),
                true,
                $raw,
            );
        }
        //If the first parameter is not an address, then that means the latitude / longitude system is used.
        else {
            //We need the 2 parameters (lat and long)
            //Notice: The p1 param is already defined, so no need to put an if for p1
            if(!isset($p2)) {
                throw new FreatherException("Error when preparing query: longitude parameter (p2) is required.", 1);
            }

            //If the 2 parameters are of authorised types, then that mean we have a longitude and latitude !
            if(in_array(gettype($p1),$authorisedTypes) && in_array(gettype($p2),$authorisedTypes))
            {
                //float value of latitude
                $floatLat = round((float)$p1,7);

                //float value of longitude
                $floatLon = round((float)$p2,7);

                //then we can pass to the "controller" of Freather.
                $this->current->fetchCurrent(
                    $floatLat,
                    $floatLon,
                    $options ?? array(),
                    false,
                    $raw
                );
            } else {
                //If p1 or p2 is not of any authorized type.
                if(!in_array(gettype($p1),$authorisedTypes)) {
                    throw new FreatherException("Error when preparing query: latitude parameter (p1) is not of any acceptable types: string, int or float. (Type of p1: " . gettype($p1) . ")", 1);
                }

                else if(!in_array(gettype($p1),$authorisedTypes)) {
                    throw new FreatherException("Error when preparing query: latitude parameter (p2) is not of any acceptable types: string, int or float. (Type of p2: ".gettype($p2).")", 1);
                }
            }
        }

		return $this;
    }

    /**
     * Will return all the fetches previously made for the 'Current' service.
     * @return array All the responses previously fetched. Will return an empty array if no data was fetched.
     */
	public function getAllCurrent(): array
    {
		return $this->current->getAll();
	}

    /**
     * Will fetch and return the result at the same time. Works the same as fetching and getting.
     *
     * <hr/>
     *
     * 2 modes are available:
     * <ul>
     *      <li> latitude (p1) and longitude (p2). The two params are required and can be of type string, int of float. </li>
     *      <li> Addresses (p1). This mode will allow you to enter different information to get weather at a city. If you want this mode, then p1 is required, but not p2. </li>
     * </ul>
     *
     * If the Addresses' system is selected, Freather will completely ignore the p2 param.
     *
     * <hr/>
     *
     * @param string|float|int|Addresses $p1 The latitude or Addresses to use. Required.
     * @param string|float|int|null $p2 The longitude. Not used if the Addresses' system is used. Will throw a FreatherException if the latitude is defined but not the longitude.
     * @param bool $raw If the response should be the raw from Openweathermap's or a basic json or xml parse.
     * @param array $options An array of key-values for optional parameters. Possible options are as follows:
     * <ul>
     *      <li>mode (string): The response method used by Openweathermap. Either json or xml. Defaults to json if not specified.</li>
     * </ul>
     *
     * @return string|stdClass The raw or parsed response from Openweathermap.
     *
     * @throws FreatherException
     */
    public function fetchGetCurrent(string|float|int|Addresses $p1, string|float|int|null $p2 = null, bool $raw = false, array $options=array()): string|stdClass
    {
        $this->fetchCurrent($p1, $p2, $raw, $options);
        return $this->current->returnRes($raw);
    }

	/* ------------------------- Map ------------------------- */
    /**
     * Will 'build' the URL to get the desired map URL.
     *
     * <hr/>
     *
     * /!\ Please refer to [the zoom levels](https://openweathermap.org/faq#zoom_levels) and [the official documentation](https://openweathermap.org/api/weather-map-2) to get meanings for the zoom, x, y and all the options, or you might get unwanted results from this method. /!\
     *
     * <hr/>
     *
     * @param string|float|int $zoom The zoom value to use. Values must range between 0 and 9 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $x The X coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $y The Y coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param MapLayer $op The map Layer to use. Please refer to [this link](https://openweathermap.org/api/weather-map-2#layers), or the MapLayer enum to get the layer you want.
     * @param array $options The optional parameters to use. Possible parameters are as follows:
     * <ul>
     *      <li> date (string): A unix timestamp (UTC). Defaults to the current Unix timestamp (UTC) if not specified. </li>
     *      <li> opacity (string|int|float): The opacity of the map layer. Ranges between 0 and 1 (inclusive). Defaults to 0.8 if not specified. </li>
     *      <li> palette (string|array): The palette of HEX colors to use. Defaults to the default Openweathermap's palette if not specified. </li>
     *      <li> fill_bound (bool|string): (From Openweathermap's documentation): 'If true, then all weather values outside the specified set of values will be filled by color corresponding to the nearest specified value (default value - false: all weather values outside the specified set of values are not filled).' Defaults to false if not specified. </li>
     *      <li> arrow_step (string|int): (From Openweathermap's documentation): Step of values for drawing wind arrows, specify in pixels. Parameter only for wind layers (WND enum). Defaults to 32 if not specified. </li>
     *      <li> use_norm (bool|string): If arrows should be proportionate to the wind speed. Only for WND enum. Defaults to false. </li>
     * </ul>
     *
     * @return Freather The current instance of Freather
     * 
     * @throws FreatherException
     */
	public function fetchMap(string|float|int $zoom, string|float|int $x, string|float|int $y, MapLayer $op, array $options=array()): Freather
	{
        //If the zoom is not between 0 and 9
        if(0 > intval($zoom) || 9 < intval($zoom)) {
            throw new FreatherException("Invalid zoom value. Possible values: an integer between 0 and 9. (Value given: " . $zoom . ")", 1);
        }

        //If X is not between 0 and 511
        else if(0 > intval($x) || 511 < intval($x)) {
            throw new FreatherException("Invalid x value. Possible values: an integer between 0 and 511. (Value given: " . $x . ")", 1);
        }

        //If Y is not between 0 and 511
        else if(0 > intval($y) || 511 < intval($y)) {
            throw new FreatherException("Invalid y value. Possible values: an integer between 0 and 511. (Value given: ".$y.")", 1);
        }

        //If all values are OK
        $_zoom = intval($zoom);
        $_x = intval($x);
        $_y = intval($y);

        //Next, we can let the 'back-end' of Freather do the rest, since all values are correct.
        $this->map->fetchMap(
            $_zoom,
            $_x,
            $_y,
            $op->name,
            $options
        );

        //And we return the object to allow "methods follow up" (IDK how it's called, but you get what I'm trying to say)
		return $this;
	}

    /**
     * Will return all the previous built maps with fetchMap in a simple array.
     * If no fetchMap was called, an empty array will be returned.
     *
     * @return array
     */
	public function getAllMap(): array
    {
		return $this->map->getAll();
	}

    /**
     * Will 'build' the URL to get the desired map URL and return the build Map URL.
     *
     * <hr/>
     *
     * /!\ Please refer to [the zoom levels](https://openweathermap.org/faq#zoom_levels) and [the official documentation](https://openweathermap.org/api/weather-map-2) to get meanings for the zoom, x, y and all the options, or you might get unwanted results from this method. /!\
     *
     * <hr/>
     *
     * @param string|float|int $zoom The zoom value to use. Values must range between 0 and 9 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $x The X coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $y The Y coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param MapLayer $op The map Layer to use. Please refer to [this link](https://openweathermap.org/api/weather-map-2#layers), or the MapLayer enum to get the layer you want.
     * @param array $options The optional parameters to use. Possible parameters are as follows:
     * <ul>
     *      <li> date (string): A unix timestamp (UTC). Defaults to the current Unix timestamp (UTC) if not specified. </li>
     *      <li> opacity (string|int|float): The opacity of the map layer. Ranges between 0 and 1 (inclusive). Defaults to 0.8 if not specified. </li>
     *      <li> palette (string|array): The palette of HEX colors to use. Defaults to the default Openweathermap's palette if not specified. </li>
     *      <li> fill_bound (bool|string): (From Openweathermap's documentation): 'If true, then all weather values outside the specified set of values will be filled by color corresponding to the nearest specified value (default value - false: all weather values outside the specified set of values are not filled).' Defaults to false if not specified. </li>
     *      <li> arrow_step (string|int): (From Openweathermap's documentation): Step of values for drawing wind arrows, specify in pixels. Parameter only for wind layers (WND enum). Defaults to 32 if not specified. </li>
     *      <li> use_norm (bool|string): If arrows should be proportionate to the wind speed. Only for WND enum. Defaults to false. </li>
     * </ul>
     *
     * @return string The built URL.
     *
     * @throws FreatherException
     */
    public function fetchGetMap(string|float|int $zoom, string|float|int $x, string|float|int $y, MapLayer $op, array $options=array()): string
    {
        $this->fetchMap($zoom, $x, $y, $op, $options);

        return $this->map->getLink();
    }

	/* ------------------------- Forecast ------------------------- */
    /**
     * Will fetch the forecast, on the last 5 days.
     *
     * <hr/>
     *
     * 2 modes are available:
     * <ul>
     *      <li> latitude (p1) and longitude (p2). The two params are required and can be of type string, int of float. </li>
     *      <li> Addresses (p1). This mode will allow you to enter different information to get weather at a city. If you want this mode, then p1 is required, but not p2. </li>
     * </ul>
     *
     * If the Addresses' system is selected, Freather will completely ignore the p2 param.
     *
     * <hr/>
     *
     * This method will take the following information defined in the configuration:
     * <ul>
     *      <li>lang</li>
     *      <li>measurement</li>
     *      <li>timestamps</li>
     *      <li>forecastEntrypoint</li>
     *      <li>cacheDuration</li>
     * </ul>
     *
     *
     * @param string|float|int|Addresses $p1 Either latitude or the Addresses. Required.
     * @param string|float|int|null $p2 Longitude. Required if latitude method is used.
     * @param bool $raw If the response stored should be raw (a string), or a basic XML or JSON parse. Optional. Defaults to false.
     * @param array $options An array of options. Optional. Here are the possible options:
     * <ul>
     *      <li>mode (string): JSON or XML. What response mode Openweathermap is chosen.</li>
     * </ul>
     *
     * @return Freather
     *
     * @throws FreatherException
     */
	function fetchForecast(string|float|int|Addresses $p1, string|float|int|null $p2 = null, bool $raw = false, array $options=array()): Freather
	{
		$authorisedTypes = array("string","float","int","integer","double");

        if(!isset($p1)) throw new FreatherException("Error when preparing query: Addresses or latitude parameter (p1) is required.", 1);

        //if p1 is an address
        if($p1 instanceof Addresses)
        {
            $this->forecast->fetchForecast(
                //We pass addresses to the first parameter
                $p1,
                //We don't need longitude to be passed
                null,
                //And we pass the options to the "controller" of Freather.
                $options ?? array(),
                true,
                $raw,
            );
        }
        //If the first parameter is not an address, then that means the latitude / longitude system is used.
        else {
            //We need the 2 parameters (lat and long)
		    if(!isset($p2)) {
                throw new FreatherException("Error when preparing query: longitude parameter (p2) is required.", 1);
            }

            //If the 2 parameters are of authorised types, then that mean we have a longitude and latitude !
            if(in_array(gettype($p1),$authorisedTypes) && in_array(gettype($p2),$authorisedTypes))
            {
                //float value of latitude
                $floatLat = round((float)$p1,7);

                //float value of longitude
                $floatLon = round((float)$p2,7);

                //then we can pass to the "controller" of Freather.
                $this->forecast->fetchforecast(
                    $floatLat,
                    $floatLon,
                    $options ?? array()
                );

            } else {
                //If p1 or p2 is not of any authorised type.
                if(!in_array(gettype($p1),$authorisedTypes)) throw new FreatherException("Error when preparing query: latitude parameter (p1) is not of any acceptable types: string, int or float. (Type of p1: ".gettype($p1).")", 1);
                if(!in_array(gettype($p1),$authorisedTypes)) throw new FreatherException("Error when preparing query: longitude parameter (p2) is not of any acceptable types: string, int or float. (Type of p2: ".gettype($p2).")", 1);
            }
        }

		return $this;
	}

    /**
     * Will return all the fetches previously made for the 'Forecast' service.
     * @return array All the responses previously fetched. Will return an empty array if no data was fetched.
     */
    public function getAllForecast(): array
    {
		return $this->forecast->getAll();
	}

    /**
     * Will fetch the forecast on the last 5 days and return the result.
     *
     * <hr/>
     *
     * 2 modes are available:
     * <ul>
     *      <li> latitude (p1) and longitude (p2). The two params are required and can be of type string, int of float. </li>
     *      <li> Addresses (p1). This mode will allow you to enter different information to get weather at a city. If you want this mode, then p1 is required, but not p2. </li>
     * </ul>
     *
     * If the Addresses' system is selected, Freather will completely ignore the p2 param.
     *
     * <hr/>
     *
     * This method will take the following information defined in the configuration:
     * <ul>
     *      <li>lang</li>
     *      <li>measurement</li>
     *      <li>timestamps</li>
     *      <li>forecastEntrypoint</li>
     *      <li>cacheDuration</li>
     * </ul>
     *
     *
     * @param string|float|int|Addresses $p1 Either latitude or the Addresses. Required.
     * @param string|float|int|null $p2 Longitude. Required if latitude method is used.
     * @param bool $raw If the response stored should be raw (a string), or a basic XML or JSON parse. Optional. Defaults to false.
     * @param array $options An array of options. Optional. Here are the possible options:
     * <ul>
     *      <li>mode (string): JSON or XML. What response mode Openweathermap is chosen.</li>
     * </ul>
     *
     * @return string|stdClass
     *
     * @throws FreatherException
     */
    public function fetchGetForecast(string|float|int|Addresses $p1, string|float|int|null $p2 = null, bool $raw = false, array $options=array()): string|stdClass
    {
        $this->forecast->fetchForecast($p1, $p2, $options, false, $raw);
        return $this->forecast->returnRes($raw);
    }

	/* ---------------------------------------- Getters and setters ---------------------------------------- */

	/* ------------------------- Config ------------------------- */

    /**
     * Will return the current configuration as an associative array.
     *
     * @return array The current configuration
     */
	public function getConfig(): array
	{
		return $this->config->getConfig();
	}

    /**
     * Will return the previous config defined.
     * Returns an empty array if either the config was rolled-back or the 'definedConfig'/'setConfig' was not called after a first instantiation of Freather.
     *
     * @return array The last used config.
     */
	public function getLastConfig(): array
	{
		return $this->config->getLastConfig();
	}

    /**
     * The same as 'defineConfig'.
     *
     * @alias defineConfig
     * @param array $config
     * @return Freather
     * @throws FreatherException
     */
	public function setConfig(array &$config): Freather
	{
		$this->defineConfig($config);

		return $this;
	}
}
