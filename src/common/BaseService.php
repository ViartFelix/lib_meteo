<?php

namespace Viartfelix\Freather\common;

use Symfony\Component\HttpClient\HttpClient;

use Viartfelix\Freather\interfaces\Adresses;
use Viartfelix\Freather\interfaces\API;

use Viartfelix\Freather\Config\Config;

use Viartfelix\Freather\Exceptions\FreatherException;

use stdClass;
use Exception;

class BaseService implements API, Adresses {
    private Config $config;
    private $response;
    private $returnedRaw;
    private $returnedRep;

    private string $mode;

    private array $optionsGlobal;

    const ACTU = 1;
    const PREVISIONS = 2;

    function __construct(Config &$config)
    {
        $this->config = $config;
    }

    public function fetchAndParse(int $service, array $options): stdClass
    {
        $toReturn = new stdClass();
        
        try {
            $this->mode = $this->parseMode($options["mode"] ?? "json");

            $this->prepareFetch($options);
            $this->fetch($service);
            $this->parseResponse();

        } catch(FreatherException $e) {
            $toReturn->code = 500;
            $toReturn->errCode = 2;
            $toReturn->msg = $e->getMessage();
            $toReturn->err = $e;

        } finally {
            return $toReturn;
        }
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

    public function fetch(int $service): void
    {

        //try {
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

        //} catch(FreatherException $e) {
        //}
    }

    private function parseResponse(): void
    {
        $finalData = new stdClass();

        try {
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
        } catch(FreatherException $e) {
            $finalData->code = 2;
            $finalData->msg = $e->getMessage();
            $finalData->err = $e;
    
        } catch (Exception $e) {
            $finalData->code = 1;
            $finalData->msg = $e->getMessage();
            $finalData->err = $e;
    
        } finally {
            $this->returnedRep = $finalData;
        }
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

    public function getRes(bool $raw = false): mixed
    {
        return ($raw ? $this->returnedRaw : $this->returnedRep);
    }

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