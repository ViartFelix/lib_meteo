<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\Exceptions\FreatherException;

use Viartfelix\Freather\meteo\{
  Actu,
  Previsions,
  Carte,
};

//TODO: constantes de Carte ici.

class Freather {
	private Config $config;
	private Actu $actu;
	private Carte $carte;
	private Previsions $previsions;

	function __construct(
		array $init=array(
			"apiKey"=>null,
			"apiEntrypoint"=>null,
			"lang"=>"en",
			"measurement"=>true,
			"timestamps"=>1,
		),
	) {
		$this->config=new Config([
			"apiKey"=>$init["apiKey"] ?? null,
			"apiEntrypoint"=>$init["apiEntrypoint"] ?? null,
			"lang"=>$init["lang"] ?? null,
			"measurement"=>$init["measurement"] ?? null,
			"timestamps"=>$init["timestamps"] ?? null,
		]);

		$this->actu = new Actu($this->config);
		$this->carte = new Carte($this->config);
		$this->previsions = new Previsions($this->config);
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
	public function fetchActu(string|float|int $latitude, string|float|int $longitude, array $options=array()): Freather
	{
		if(!isset($latitude)) throw new FreatherException("Error when preparing query: latitude parameter is required.", 1);
		if(!isset($longitude)) throw new FreatherException("Error when preparing query: longitude parameter is required.", 1);

		$this->actu->fetchActu(
            //rounding at 8 decimals for world-wide coordinates
			round(floatval($latitude), 8),
			round(floatval($longitude), 8),
			$options,
		);

		return $this;
    }

	public function getActu(bool $raw = false): mixed
	{
		return $this->actu->returnResults($raw);
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
	function fetchPrevisions(string|float|int $lat, string|float|int $lon, array $options=array()): Freather
	{
		$this->previsions->fetchPrevisions(
            //rounding at 8 decimals for world-wide coordinates
			round(floatval($lon), 8),
			round(floatval($lat), 8),
			$options,
		);

		return $this;
	}

	public function getPrevisions(bool $raw = false): mixed
	{
		return $this->previsions->returnResults($raw);
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