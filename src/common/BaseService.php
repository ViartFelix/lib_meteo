<?php

namespace Viartfelix\Freather\common;

use Symfony\Component\HttpClient\HttpClient;

use Viartfelix\Freather\interfaces\{
    Adresses,
    API,
    CacheInterface,
};

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\config\Cache;

use Viartfelix\Freather\Exceptions\FreatherException;

use stdClass;
use Exception;

class BaseService implements API, Adresses, CacheInterface {
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

    /* ------------------------------------ Functions native to baseService ------------------------------------ */

    function __construct(Config &$config, Cache &$cache)
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

    /**
     * prepareFetch
     * @param array $options The options called in either Previ or Actu
     * @return void
     */
    public function prepareFetch(array $options): void
    {
        /**
         * Common options, defined in config
         */
        $this->setOption("appid", $this->config->getApiKey());
        $this->setOption("lang", $this->config->getLang());
        $this->setOption("units", $this->config->getUnit());
        $this->setOption("cnt", $this->config->getTimestamps());

        /**
         * Other common options (lat, long, mode)
         */
        $this->setOption("mode", $this->mode);
        $this->setOption("lat", $options["latitude"]);
        $this->setOption("lon", $options["longitude"]);
    }

    public function prepareAdresses(Adresses $adresses): void
    {

    }

    /**
     * checkCache
     * 'Compiles' the URL for checking the chache later-on
     */
    public function checkCache(int $service): bool
    {
        $finalUrl="";

        switch ($service) {
            //ACTU service
            case 1:
                $finalUrl = $this->config->getActuEntrypoint();
                break;

            //PREVI service
            case 2:
                $finalUrl = $this->config->getPreviEntrypoint();
                break;
        }

        $finalUrl .= "?";

        $indexOption = 0;
        foreach ($this->getGlobalOptions() as $key => $value) {
            //If next item is the second from the array
            $finalUrl .= (++$indexOption == 1 ? "" : "&");
            $finalUrl .= $key . "=" . $value;
        }

        $this->hashedUrl = $this->encodeUrl($finalUrl);

        $this->finalUrl = $finalUrl;

        return $this->cache->checkItem($this->hashedUrl);
    }

    private function parseResponse(): void
    {
        $finalData = new stdClass();

        //try {
          switch (strtolower($this->mode)) {
            case 'json':
              $data = json_decode($this->returnedRaw, true, 512) or throw new FreatherException("Could not parse JSON reponse.", 1);
    
              if($data instanceof stdClass) {
                $finalData = $data;
              } else {
                $finalData = (object)$data;
              }
    
              break;
            case 'xml':
              $decoded_xml = simplexml_load_string($this->returnedRaw, "SimpleXMLElement", LIBXML_NOCDATA) or throw new FreatherException("Could not parse XML response. (error when loading the XML)", 1);
              $json_decoded = json_encode($decoded_xml) or throw new FreatherException("Could not parse XML response. (error when encoding the XML response)", 1);
              $data = json_decode($json_decoded, true) or throw new FreatherException("Could not parse XML response. (error when decoding the XML response)", 1);
    
              if($data instanceof stdClass) {
                $finalData = $data;
              } else {
                $finalData = (object)$data;
              }
      
              break;
            
            default:
              throw new FreatherException("Error when trying to parse response: the mode '$this->mode' is not supported. Please use 'xml' or 'json' modes.", 1);
          }

          $finalData->FreatherInfos = new stdClass();
          $finalData->FreatherInfos->isCached = $this->isCached;
          $finalData->FreatherInfos->queryUrl = $this->finalUrl;
          $finalData->FreatherInfos->responseMode = $this->mode;
          $finalData->FreatherInfos->options = $this->getGlobalOptions();
          $finalData->FreatherInfos->UrlHash = $this->hashedUrl;

        //} catch(FreatherException $e) {
        //    $finalData->code = 2;
        //    $finalData->msg = $e->getMessage();
        //    $finalData->err = $e;

        //} catch (Exception $e) {
        //    $finalData->code = 1;
        //    $finalData->msg = $e->getMessage();
        //    $finalData->err = $e;
    
        //} finally {
            $this->returnedRep = $finalData;
        //}
    }

    public function parseMode(string $mode=null): string
    {
        $allowedMethods = array (
            "json",
            "xml"
        );
        
        $modeRaw = strtolower($mode ?? "json");
        $searchMethod = array_search($modeRaw,$allowedMethods);
    
        //Fallbacking in json mode if the mode is unknown from authorised methods
        return ($searchMethod !== false ? $allowedMethods[array_search($modeRaw,$allowedMethods)] : $allowedMethods[0] ?? "json");
    }

    /* ------------------------------------ Interfaces ------------------------------------ */

    /* ------------ API Interface ------------ */

    public function fetch(int $service): void
    {
        $client = HttpClient::create();

        $apiEntrypoint="";

        switch ($service) {
            //ACTU service
            case 1:
                $apiEntrypoint = $this->config->getActuEntrypoint();
                break;

            //PREVI service
            case 2:
                $apiEntrypoint = $this->config->getPreviEntrypoint();
                break;
            
            //No idea what to put here, so here is a dog ˁ˚ᴥ˚ˀ
            default:
                throw new FreatherException("Error when setting up the fetch Service unknown", 1);
        }


        $this->response = $client->request(
            //Method
            "GET",
            //API entrypoint, specified in the switch above
            $apiEntrypoint,
            [
                //verify peer to avoid error
                "verify_peer" => false,
                //$_GET options, speified in prepareFetch
                "query" => $this->getGlobalOptions(),
            ],
        );
        

        $this->returnedRaw = $this->response->getContent();
    }

    public function getRes(bool $raw = false): mixed
    {
        return ($raw ? $this->returnedRaw : $this->returnedRep);
    }

    /* ------------ Adresses Interface ------------ */

    /**
     * Vas stocker les infos des adresses.
     */
    public function storeAdrInfos()
    {

    }

    /**
     * Vas convertir les donnes d'adresse stockées en query pour le fetch
     */
    public function convertToQuery()
    {

    }

    /* ------------ Cache Interface ------------ */

    /**
     * encoreUrl
     * Allowed 'encoding' of URL because PHPfastcache doesn't support those special charaters used in the URL: /, \ and :
     * @param string $url
     * @return string
     */
    public function encodeUrl(string $url): string
    {
        return md5($url);
    }


    /* ------------------------------------ Getters and setters ------------------------------------ */

    public function getGlobalOptions(): array
    {
        return $this->optionsGlobal;
    }

    public function getOption(string $key): mixed
    {
        return $this->optionsGlobal[$key];
    }

    public function setOption(string $key, mixed $value): void
    {
        $this->optionsGlobal[$key] = $value;
    }
}

?>