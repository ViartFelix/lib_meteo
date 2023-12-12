<?php

namespace Viartfelix\Freather\common;

use stdClass;
use Symfony\Component\HttpClient\HttpClient;

use Viartfelix\Freather\interfaces\{
    Adresses,
    API,
    CacheInterface,
};

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\config\Cache;

class BaseAdresse
{
    private Config $config;
    private Cache $cache;

    private $response;
    private $returnedRaw;
    private $returnedRep;

    private string $mode;

    private array $optionsGlobal;

    const ACTU = 1;
    const PREVISIONS = 2;

    private string $finalUrl;
    private bool $isCached = false;
    private string $hashedUrl;



    public function construct(Config &$config, Cache &$cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    public function fetchAndParse(int $service, array $options): stdClass
    {
        $toReturn = new stdClass();
        
        $this->mode = $this->parseMode($options["mode"] ?? "json");

        $this->prepareFetch($options);

        //If the element is not present in the cache.
        if(!$this->checkCache($service)) {
            //Fetch the data
            $this->fetch($service);
            //Set the raw response to the cache
            $this->cache->setItem($this->hashedUrl, $this->returnedRaw);

            $this->isCached = false;
        } else {
            //Get the stored item
            $this->returnedRaw = $this->cache->getItem($this->hashedUrl);

            $this->isCached = true;
        }

        $this->parseResponse();
        
        return $toReturn;
    }


}


?>