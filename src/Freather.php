<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\{
    Config,
    Cache,
};

use Viartfelix\Freather\Exceptions\FreatherException;

use Viartfelix\Freather\meteo\{
  Actu,
  Previsions,
  Carte,
  Adresses,
};

//TODO: debug function (like toString): which is a ton of var_dumps echoed.
//TODO: fetchGet(Service): fetch and get at the same time.


class Freather {
	private Config $config;
    private Cache $cache;
	private Actu $actu;
	private Carte $carte;
	private Previsions $previsions;

	function __construct(
        string $apiKey,

		array $init=array(
			"lang"=>"en",
			"measurement"=>"standard",
			"timestamps"=>1,

            "actuEntrypoint"=>null,
            "mapEntrypoint"=>null,
            "previEntrypoint"=>null,
		),
        
        int $cacheDuration = -1,
	) {
		$this->config=new Config ([
			"apiKey"=>$apiKey,
			"lang"=>$init["lang"] ?? null,
			"measurement"=>$init["measurement"] ?? null,
			"timestamps"=>$init["timestamps"] ?? null,

            "actuEntrypoint" => $init["actuEntrypoint"] ?? null,
            "mapEntrypoint" => $init["mapEntrypoint"] ?? null,
            "previEntrypoint" => $init["previEntrypoint"] ?? null,

            "cacheDuration" => $cacheDuration ?? -1,
		]);


        //If the user didn't specify a duration for the cache (as it is required for Phpfastcache), then it will not be enabled.
        if(isset($cacheDuration)) $this->cache = new Cache($this->config->cacheDuration);


		$this->actu = new Actu($this->config, $this->cache);
		$this->carte = new Carte($this->config);
		$this->previsions = new Previsions($this->config, $this->cache);
    }

    /* ---------------------------------------- Class-specific constants ---------------------------------------- */

    /* ------------------------- Carte ------------------------- */

    /**  @var string Convective precipitation (mm) */
    public const PAC0 = "PAC0";

    /**  @var string Precipitation intensity (mm/s) */
    public const PR0 = "PR0";

    /**  @var string Accumulated precipitation (mm) */
    public const PA0 = "PA0";

    /**  @var string Accumulated precipitation - rain (mm) */
    public const PAR0 = "PAR0";

    /**  @var string Accumulated precipitation - snow (mm) */
    public const PAS0 = "PAS0";

    /**  @var string Depth of snow (m) */
    public const SD0 = "SD0";

    /**  @var string Wind speed at an altitude of 10 meters (m/s) */
    public const WS10 = "WS10";

    /**  @var string Joint display of speed wind (color) and wind direction (arrows), received by U and V components  (m/s) */
    public const WND = "WND";

    /**  @var string Atmospheric pressure on mean sea level (hPa) */
    public const APM = "APM";

    /**  @var string Air temperature at a height of 2 meters (°C) */
    public const TA2 = "TA2";

    /**  @var string Temperature of a dew point (°C) */
    public const TD2 = "TD2";

    /**  @var string Soil temperature 0-10 сm (K) */
    public const TS0 = "TS0";

    /**  @var string Soil temperature >10 сm (K) */
    public const TS10 = "TS10";

    /**  @var string Relative humidity (%) */
    public const HRD0 = "HRD0";

    /**  @var string Cloudiness (%) */
    public const CL = "CL";

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

  /* ------------------------- Actu ------------------------- */
	public function fetchActu(string|float|int|Adresses $p1, string|float|int|null $p2 = null, array $options=array()): Freather
	{
        $authorisedTypes = array("string","float","int","double");

        if(!isset($p1)) throw new FreatherException("Error when preparing query: Adresses or latitude parameter (p1) is required.", 1);

        //if p1 is an adress
        if($p1 instanceof Adresses)
        {
            $this->actu->fetchActu(
                //We pass adresses to the first parameter
                $p1,
                //We don't need longitude to be passed
                null,
                //And we pass the options to the "controller" of Freather.
                $options ?? array(),
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
                $this->actu->fetchActu(
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

	public function getActu(bool $raw = false): mixed
	{
		return $this->actu->returnRes($raw);
	}

	/* ------------------------- Carte ------------------------- */
    /**
     * fetchMap
     * /!\ Please refer to [the zoom levels](https://openweathermap.org/faq#zoom_levels) to get meanings for the zoom, x and y parameters, or you might get unwanted results from this method. /!\
     * @param string|float|int $zoom The zoom value to use. Values must range between 0 and 9 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $x The X coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param string|float|int $y The Y coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.
     * @param mixed $op The Layer to use. It is strongly recomanded to use the constants of Freather for this parameter, as they provide easy and safe access to layers. Please refer to [this link](https://openweathermap.org/api/weather-map-2#layers) to get the layer you want.
     * @param array $options The optionnal parameters to use. Possible parameters are: date, opacity, palette, fill_bound, arrow_step and use_norm.
     * @return Freather The current instance of Freather
     * 
     * @throws FreatherException
     */
    
    //TODO: Instance de FreatherAdresse dans les params.

	public function fetchMap(string|float|int $zoom, string|float|int $x, string|float|int $y, mixed $op, array $options=array()): Freather
	{

        //First, check of valid values before initializing the fetch request
        //Soruces for those numbers: https://openweathermap.org/faq#zoom_levels
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
		$this->carte->fetchMap(
            $_zoom,
            $_x,
            $_y,
            $op,
            $options
        );

        //And we return the object to allow "methods follow up" (idk how it's called but you get what i'm trying to say)
		return $this;
	}

	public function getMap(): mixed
	{
		return $this->carte->getLink();
	}

	/* ------------------------- Prévisions ------------------------- */
	/** Fonction qui permet de récupérer les préivisions météo */
	function fetchPrevisions(string|float|int|Adresses $p1, string|float|int|null $p2 = null, array $options=array()): Freather
	{
		$authorisedTypes = array("string","float","int","double");

        if(!isset($p1)) throw new FreatherException("Error when preparing query: Adresses or latitude parameter (p1) is required.", 1);

        //if p1 is an adress
        if($p1 instanceof Adresses)
        {
            $this->previsions->fetchPrevisions(
                //We pass adresses to the first parameter
                $p1,
                //We don't need longitude to be passed
                null,
                //And we pass the options to the "controller" of Freather.
                $options ?? array(),
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
                $this->previsions->fetchPrevisions(
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

	public function getPrevisions(bool $raw = false): mixed
	{
		return $this->previsions->returnRes($raw);
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