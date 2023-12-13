<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\common\LatlongService as CoordsService;
use Viartfelix\Freather\common\AdressesService as AdressesService;
//use Viartfelix\Freather\common\BaseService as BaseService;
use Viartfelix\Freather\common\Baser as Baser;

use Viartfelix\Freather\config\Cache;
use Viartfelix\Freather\Config\Config;

use Viartfelix\Freather\Exceptions\FreatherException;

class Actu extends Baser
{
    //using Helper's trait methods
    use AdressesService, CoordsService;

    private Config $config;
    private Cache $cache;

    private float|Adresses $p1;
    private float|null $p2;

	private array $options;

	function __construct(Config &$config, Cache &$cache)
	{
        $this->config = &$config;
        $this->cache = &$cache;
	}

	public function fetchActu(float|Adresses $p1, float|null $p2 = null, array $options = array()): void
	{
        //If the adresses system is used
        if($p1 instanceof Adresses)
        {
            
        }
        //If p1 is type of floating, then the lat-lon system is used.
        else
        {
            //If latitude or longitude is inside the authorised range
            if($this->isInRange($p1, $p2))
            {
                
            }
            
        }


    
	}

	private function prepare(): void
	{}

	private function exec(): void
	{
        /*
		$options = $this->getOptions();
		$options["latitude"] = $this->getLat();
		$options["longitude"] = $this->getLon();

		$this->parseMode($options["mode"] ?? "json");

		$err = $this->fetchAndParse(BaseService::ACTU, $options);

        if(isset($err) && isset($err->errCode))
        {
            throw new FreatherException("Error when fetching or parsing response from server. Logs are likely present on top of this error.", $err->errCode);
        }
        */
	}

    public function getP1(): float|Adresses
    {
        return $this->p1;
    }

    public function setP1(float|Adresses $p1): void
    {
        $this->p1 = $p1;
    }

    public function getP2(): float|null
    {
        return $this->p2;
    }

    public function setP2(float $p2): void
    {
        $this->p2 = $p2;
    }



    /*
	public function returnResults(bool $raw = false): mixed
	{
		return ($this->getRes($raw));
	}

	public function getLat(): float
	{
		return $this->latitude;
	}

	public function setLat(float $lat): void
	{
		$this->latitude = $lat;
	}

	public function getLon(): float
	{
		return $this->longitude;
	}

	public function setLon(float $long): void
	{
		$this->longitude = $long;
	}
    */
    public function addOption(string $key, mixed $value): void
    {
        $this->options[$key] = $value;
    }
    
	public function setOptions(array $options): void
	{
		$this->options = $options;
	}

	public function getOptions(): array
	{
		return $this->options;
	}
}

?>