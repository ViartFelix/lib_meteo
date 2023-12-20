<?php

namespace Viartfelix\Freather\weather;

use Viartfelix\Freather\common\LatlongService as CoordsService;
use Viartfelix\Freather\common\AddressesService as AddressesService;
use Viartfelix\Freather\common\BaseService as BaseService;

use Viartfelix\Freather\config\Cache;
use Viartfelix\Freather\Config\Config;

use Viartfelix\Freather\Exceptions\FreatherException;

use stdClass;

class Forecast extends BaseService
{

    //using Helper's trait methods
    use AddressesService, CoordsService;

    private Config $config;
    private Cache $cache;

    private float|Addresses $p1;
    private float|null $p2;

    private array $options;

    private string $rawResponse;
    private stdClass $response;

    private array $allResponses;
    private array $allResponsesRaw;

    private array $storedResponses;

    function __construct(Config &$config, Cache &$cache)
	{
        parent::__construct($config, $cache);
        $this->config = &$config;
        $this->cache = &$cache;
	}


    public function fetchForecast(float|Addresses $p1, float|null $p2 = null, array $options = array(), bool $storeResponse = false, bool $raw = false): void
	{
        $finalGet = "";

        //If the addresses system is used
        if($p1 instanceof Addresses)
        {
            //We parse the addresses
            $parsedAdresse = $this->parseAddresses($p1);

            //Compiling all the params
            $finalGet = $this->compileAddresses($parsedAdresse, $this->config);
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
            }
        }
        
        //parsing the response mode (for security sakes)
        $finalGet["mode"] = $this->parseMode($options["mode"] ?? null);

        //Adding custom param (which is not gonna be used by openweathermap)
        $finalGet["isRaw"] = $raw;

        //compile URL for the cache
        $finalUrl = $this->compileUrl(BaseService::FORECAST, $finalGet);

        //setting up the object for the response
        $response = new stdClass();

        //Note: I convert the url into md5 because PHPfastcache doesn't support the following characters {}()/\@:
        $cacheKey = md5($finalUrl);

        //is gonna be used to tell if the pbject has been cached.
        $isCached = true;

        //If the item is not in the cache
        if(!$this->checkItem($cacheKey))
        {
            //Then we fetch it to openweathermap
            $this->rawResponse = $this->fetch(BaseService::FORECAST, $finalGet);
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

        if($storeResponse)
        {
            //we store the non-raw response, for when getCurrent will be called
            $this->insertResponse($this->response);

            //we store the raw response, same reason
            $this->insertRawResponse($this->rawResponse);
        }

        //If the response is not raw
        if(!$raw)
        {
            $this->insertStoredResponses($this->response);
        } else {
            $this->insertStoredResponses($this->rawResponse);
        }
	}

    public function returnRes(bool $isRaw = false): string|stdClass
    {
        return ($isRaw ? $this->getRaw() : $this->getResponse());
    }

    public function getAll(): array
    {
        return $this->storedResponses;
    }

    public function getResponse(): stdClass
    {
        return $this->response;
    }

    public function getRaw(): string
    {
        return $this->rawResponse;
    }

    public function getAllResponses(): array
    {
        return $this->allResponses;
    }

    public function setAllResponses(array $allResponses): void
    {
        $this->allResponses = $allResponses;
    }

    public function getP1(): float|Addresses
    {
        return $this->p1;
    }

    public function setP1(float|Addresses $p1): void
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

    //any type
    public function insertResponse($response): void
    {
        $this->allResponses[] = $response;
    }

    public function getAllRawResponses(): array
    {
        return $this->allResponsesRaw;
    }

    public function insertRawResponse($rawResponse): void
    {
        $this->allResponsesRaw[] = $rawResponse;
    }

    public function insertStoredResponses($response): void
    {
        $this->storedResponses[] = $response;
    }
}

?>