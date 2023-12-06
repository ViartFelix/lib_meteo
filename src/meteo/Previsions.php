<?php

namespace Viartfelix\Freather\meteo;
use Viartfelix\Freather\Adresse\Adresse;

use Viartfelix\Freather\Config\Config;
use Symfony\Component\HttpClient\HttpClient;

class Previsions {
  private Config $config;
  private float $longitude;
  private float $latitude;
  private mixed $rawResponse;
  private mixed $response;

  private $client;
  
  function __construct(Config &$config)
  {
    $this->config=&$config;
  }

  public function fetchPrevisions(float $lon, float $lat): void
  {
    $this->setLong($lon);
    $this->setLat($lat);

    $this->prepare();
    $this->exec();
  }

  public function prepare(): void
  {
    $this->client = HttpClient::create();
  }

  public function exec(): void
  {
    $this->rawResponse=$this->client->request(
      "GET",
      $this->config->getApiEntrypoint() . "forecast",
      [
        "verify_peer"=>false,
        "query"=>[
          "lang"=>$this->config->getLang(),
          "measurement"=>$this->config->getUnit(),
          "lat"=>$this->latitude,
          "lon"=>$this->longitude,
          "appid"=>$this->config->getApiKey(),
          "cnt"=>$this->config->getTimestamps(),
        ],
      ]
    );

    $this->response=json_decode($this->rawResponse->getContent());
    var_dump($this->response);
  }

  public function returnResults(bool $raw): mixed
  {
    return ($raw ? $this->getRaw() : $this->get());
  }

  public function getRaw(): mixed
  {
    return $this->rawResponse;
  }

  public function get(): mixed
  {
    return $this->response;
  }

  public function setLong(float $long): void
  {
    $this->longitude = $long;
  }

  public function getLong(): float
  {
    return $this->longitude;
  }

  public function setLat(float $lat): void
  {
    $this->latitude = $lat;
  }

  public function getLat(): float
  {
    return $this->latitude;
  }
}

?>