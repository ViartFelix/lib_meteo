<?php

namespace Viartfelix\Freather\meteo;

use stdClass;
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

    private string $rawResponse;
    private stdClass $response;

	function __construct(Config &$config, Cache &$cache)
	{
        parent::__construct($config, $cache);
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
                //Compiling of the params, for the fetch
                $finalGet = $this->compileOptions([
                    "lat" => $p1,
                    "lon" => $p2,
                    $options,
                ],$this->config);

                //parsing the response mode (for security sakes)
                $finalGet["mode"] = $this->parseMode($options["mode"] ?? null);

                //compile URL for the cache
                $finalUrl = $this->compileUrl(Baser::ACTU, $finalGet);

                $response = new stdClass();

                //Note: I convert the url into md5 because PHPfastcache doesn't support the following characters {}()/\@:
                $cacheKey = md5($finalUrl);

                $isCached = true;

                //If the item is not in the cache
                if(!$this->checkItem($cacheKey))
                {
                    //Then we fetch it to openweathermap
                    $this->rawResponse = $this->fetch(Baser::ACTU, $finalGet);
                    //We tell that the reponse is not cached
                    $isCached = false;
                    //And we put the item in the cache
                    $this->setItem($cacheKey,$this->rawResponse);
                }
                //If there is this item in the cache
                else {
                    //We get the item from the cache
                    $this->rawResponse = $this->getItem($cacheKey);
                    $isCached = true;
                }

                $response = $this->parseResponse($this->rawResponse, $finalGet["mode"]);

                /**
                 * Last added data (Freather infos)
                 */
                $response->FreatherInfos = new stdClass();
                //if the object is cached
                $response->FreatherInfos->isCached = $isCached;
                //response mode
                $response->FreatherInfos->mode = $finalGet["mode"];
                //finalUrl
                $response->FreatherInfos->finalUrl = $finalUrl;
                //all the options in the query
                $response->FreatherInfos->options = $finalGet;

                //and we attribute the final response, alongside Freather's data to the response
                $this->response = $response;                
            }
            
        }
	}

    public function returnRes(bool $isRaw = false)
    {
        return ($isRaw ? $this->getRaw() : $this->getResponse());
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRaw(): string
    {
        return $this->rawResponse;
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