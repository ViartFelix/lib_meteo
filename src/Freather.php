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

class Freather {
	private Config $config;
    private Cache $cache;
	private Current $current;
	private Map $map;
	private Forecast $forecast;
    
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
        if(isset($cacheDuration)) $this->cache = new Cache($this->config->cacheDuration);


		$this->current = new Current($this->config, $this->cache);
		$this->map = new Map($this->config);
		$this->forecast = new Forecast($this->config, $this->cache);
    }

    /* ---------------------------------------- Class-specific methods ---------------------------------------- */

    /* ------------------------- Config ------------------------- */
	public function defineConfig(array $config): Freather
	{
		$this->config->defineConfig($config);
		return $this;
	}

	public function rollbackConfig(): Freather
	{
		$this->config->rollbackConfig();
		return $this;
	}

  /* ------------------------- Current ------------------------- */
	public function fetchCurrent(string|float|int|Addresses $p1, string|float|int|null $p2 = null, $raw = false, array $options=array()): Freather
	{
        $authorisedTypes = array("string","float","int","double");

        if(!isset($p1)) throw new FreatherException("Error when preparing query: Addresses or latitude parameter (p1) is required.", 1);

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
        //If the first parametter is not an adress, then that means the latitude / longitude system is used. 
        else {
            //We need the 2 parametters (lat and long)
            if(!isset($p1)) throw new FreatherException("Error when preparing query: latitude parameter (p1) is required.", 1);
		    if(!isset($p2)) throw new FreatherException("Error when preparing query: longitude parameter (p2) is required.", 1);

            //If the 2 parameters are of authorised types, then that mean we have a longitude and latitude !
            if(in_array(gettype($p1),$authorisedTypes,false) && in_array(gettype($p2),$authorisedTypes,false))
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
                //If p1 or p2 is not of any authorised type.
                if(!in_array(gettype($p1),$authorisedTypes,false)) throw new FreatherException("Error when preparing query: latitude parameter (p1) is not of any acceptable types: string, int or float. (Type of p1: ".gettype($p1).")", 1);
                if(!in_array(gettype($p1),$authorisedTypes,false)) throw new FreatherException("Error when preparing query: latitude parameter (p2) is not of any acceptable types: string, int or float. (Type of p2: ".gettype($p2).")", 1);
            }
        }

		return $this;
    }

	public function getAllCurrent(): array
    {
		return $this->current->getAll();
	}

    public function fetchGetCurrent(string|float|int|Addresses $p1, string|float|int|null $p2 = null, array $options=array(), bool $raw = false): string|stdClass
    {
        $this->current->fetchCurrent($p1, $p2, $options, false);
        return $this->current->returnRes($raw);
    }

	/* ------------------------- Map ------------------------- */
    /**
     * fetchMap
     * /!\ Please refer to [the zoom levels](https://openweathermap.org/faq#zoom_levels) to get meanings for the zoom, x and y parameters, or you might get unwanted results from this method. /!\
     * @param string|float|int $zoom The zoom value to use. Values must range between 0 and 9 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $x The X coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $y The Y coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param MapLayer $op The Layer to use. Please refer to [this link](https://openweathermap.org/api/weather-map-2#layers), or the MapLayer enum to get the layer you want.
     * @param array $options The optionnal parameters to use. Possible parameters are: date, opacity, palette, fill_bound, arrow_step and use_norm.
     * @return Freather The current instance of Freather
     * 
     * @throws FreatherException
     */
	public function fetchMap(string|float|int $zoom, string|float|int $x, string|float|int $y, MapLayer $op, array $options=array()): Freather
	{

        //First, check of valid values before initializing the fetch request
        //Sources for those numbers: https://openweathermap.org/faq#zoom_levels
        //If zoom is not between 0 and 9
        if(0 > intval($zoom) || 9 < intval($zoom)) throw new FreatherException("Invalid zoom value. Possible values: an integer between 0 and 9. (Value given: ".$zoom. ")", 1);
        //If X and Y are not between 0 and 511
        if(0 > intval($x) || 511 < intval($x)) throw new FreatherException("Invalid x value. Possible values: an integer between 0 and 511. (Value given: ".$x.")", 1);
        if(0 > intval($y) || 511 < intval($y)) throw new FreatherException("Invalid y value. Possible values: an integer between 0 and 511. (Value given: ".$y.")", 1);

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

        //And we return the object to allow "methods follow up" (idk how it's called but you get what i'm trying to say)
		return $this;
	}

	public function getMap(): mixed
	{
		return $this->map->getLink();
	}

	/* ------------------------- Forecast ------------------------- */
	function fetchForecast(string|float|int|Addresses $p1, string|float|int|null $p2 = null, bool $raw = false, array $options=array()): Freather
	{
		$authorisedTypes = array("string","float","int","double");

        if(!isset($p1)) throw new FreatherException("Error when preparing query: Addresses or latitude parameter (p1) is required.", 1);

        //if p1 is an adress
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
        //If the first parametter is not an adress, then that means the latitude / longitude system is used. 
        else {
            //We need the 2 parametters (lat and long)
            if(!isset($p1)) throw new FreatherException("Error when preparing query: latitude parameter (p1) is required.", 1);
		    if(!isset($p2)) throw new FreatherException("Error when preparing query: longitude parameter (p2) is required.", 1);

            //If the 2 parameters are of authorised types, then that mean we have a longitude and latitude !
            if(in_array(gettype($p1),$authorisedTypes,false) && in_array(gettype($p2),$authorisedTypes,false))
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
                if(!in_array(gettype($p1),$authorisedTypes,false)) throw new FreatherException("Error when preparing query: latitude parameter (p1) is not of any acceptable types: string, int or float. (Type of p1: ".gettype($p1).")", 1);
                if(!in_array(gettype($p1),$authorisedTypes,false)) throw new FreatherException("Error when preparing query: latitude parameter (p2) is not of any acceptable types: string, int or float. (Type of p2: ".gettype($p2).")", 1);
            }
        }

		return $this;
	}

	public function getAllForecast(): mixed
	{
		return $this->forecast->getAll();
	}

    public function fetchGetForecast(string|float|int|Addresses $p1, string|float|int|null $p2 = null, bool $raw = false, array $options=array())
    {
        $this->forecast->fetchForecast($p1, $p2, $options, false, $raw);
        return $this->forecast->returnRes($raw);
    }

	/* ---------------------------------------- Getters and setters ---------------------------------------- */

	/* ------------------------- Config ------------------------- */

	public function getConfig(): array
	{
		return $this->config->getConfig();
	}

	public function getLastConfig(): array
	{
		return $this->config->getLastConfig();
	}

	public function setConfig(array $config): Freather
	{
		$this->defineConfig($config);

		return $this;
	}
}

?>