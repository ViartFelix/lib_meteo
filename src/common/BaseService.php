<?php

namespace Viartfelix\Freather\common;

use Exception;
use Symfony\Component\HttpClient\HttpClient;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\config\Cache;

use Viartfelix\Freather\Exceptions\FreatherException;

use Symfony\Component\HttpClient\Exception\{
    ServerException,
    ClientException,
    RedirectionException
};

use stdClass;
class BaseService
{
    private Config $config;
    private Cache $cache;

    public const CURRENT = 1;
    public const FORECAST = 2;

    
    function __construct(Config &$config, Cache &$cache)
    {
        $this->config = $config;
        $this->cache = $cache;
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


    /**
     * Will loop throught the freshly compiled options and create the API Url, to later check in the cache.
     */
    public function compileUrl(int $service, array $options): string
    {
        $toReturn = "";

        switch ($service) {
            //CURRENT service
            case 1:
                $toReturn = $this->config->getCurrentEntrypoint();
                break;

            //FORECAST service
            case 2:
                $toReturn = $this->config->getForecastEntrypoint();
                break;
        }

        $toReturn .= "?";

        $indexOption = 0;
        foreach ($options as $key => $value) {
            $toReturn .= (++$indexOption > 1 ? "&" : "") . $key . "=" .$value;
        }

        return $toReturn;
    }

    public function fetch(int $service, array $options): string
    {
        $client = HttpClient::create();
        $entrypoint = "";

        switch ($service) {
            //CURRENT service
            case 1:
                $entrypoint = $this->config->getCurrentEntrypoint();
                break;

            //FORECAST service
            case 2:
                $entrypoint = $this->config->getForecastEntrypoint();
                break;
        }

        try {
            $response = $client->request(
                //Method
                "GET",
                //API entrypoint, specified in the switch above
                $entrypoint,
                [
                    //verify peer to avoid error
                    "verify_peer" => false,
                    //$_GET options, speified in prepareFetch
                    "query" => $options,
                ],
            );

    
            //Response body
            $bodyRep = $response->getContent();

            return $bodyRep;
        }
        //500 errors
        catch(ServerException $e) {
            throw new FreatherException("Error when fetching the data to OpenWeatherMap (HTTP error 5XX). HttpClient's error: " . $e->getMessage());
        }
        //400 errors
        catch (ClientException $e) {
            throw new FreatherException("Error when fetching the data to OpenWeatherMap (HTTP error 4XX). HttpClient's error: " . $e->getMessage());
        }
        //300 errors
        catch(RedirectionException $e) {
            throw new FreatherException("Error when fetching the data to OpenWeatherMap (HTTP error 3XX). HttpClient's error: " . $e->getMessage());
        }
        catch(Exception $e) {
            throw new FreatherException("Unknown error when fetching the data to OpenWeatherMap. HttpClient's error: " . $e->getMessage());
        }
    }

    public function parseResponse(string $response, string $mode): stdClass
    {
        $finalData = $nonRawResponse = new stdClass();

        switch (strtolower($mode)) {
            case 'json':
            $nonRawResponse = json_decode($response, true, 512) or throw new FreatherException("Could not parse JSON reponse.", 1);
                break;

            case 'xml':
                $decoded_xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA) or throw new FreatherException("Could not parse XML response. (error when loading the XML)", 1);
                $json_decoded = json_encode($decoded_xml) or throw new FreatherException("Could not parse XML response. (error when encoding the XML response)", 1);
                $nonRawResponse = json_decode($json_decoded, true) or throw new FreatherException("Could not parse XML response. (error when decoding the XML response)", 1);
                break;
            
            default:
              throw new FreatherException("Error when trying to parse response: the mode '$mode' is not supported. Please use 'xml' or 'json' modes.", 1);
          }

        //If the decoded response is a stdClass (what we want to return), the we return the final data
        if($nonRawResponse instanceof stdClass) {
            $finalData = $nonRawResponse;
        } else {
            $finalData = json_decode(json_encode($nonRawResponse));
        }

        return $finalData;
    }

    public function checkItem(string $key): bool
    {
        return $this->cache->getInstance()->has($key);
    }

    public function setItem(string $key, mixed $value): void
    {
        $this->cache->getInstance()->set($key, $value, ($this->cache->getCacheDuration() ?? -1));
    }

    public function getItem(string $key): mixed
    {
        return $this->cache->getInstance()->get($key);
    }


}

?>