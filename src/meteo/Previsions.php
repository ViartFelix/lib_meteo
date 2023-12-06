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
  private array $options = array();

  private $client;
  
  function __construct(Config &$config)
  {
    $this->config=&$config;
  }

  public function fetchPrevisions(float $lon, float $lat, array $options=array()): void
  {
    $this->setLong($lon);
    $this->setLat($lat);
    $this->setOptions($options);

    $this->prepare();
    $this->exec();
  }

  private function prepare(): void
  {
    $this->client = HttpClient::create();
  }

  private function exec(): void
  {
    $this->rawResponse=$this->client->request(
      "GET",
      $this->config->getApiEntrypoint() . "forecast",
      [
        "verify_peer"=>false,
        "query"=>[
          "lat" => $this->getLat(),
          "lon" => $this->getLong(),
          "appid" => $this->config->getApiKey(),

          "units" => $this->getOptions()["units"] ?? $this->config->getUnit() ?? "standard",
          "mode" => $this->getOptions()["mode"] ?? "json",
          "cnt" => $this->getOptions()["cnt"] ?? $this->getOptions()["timestamps"] ?? $this->config->getTimestamps() ?? 1,
          
          "lang" => $this->config->getLang(),
        ],
      ]
    );

    $this->response=json_decode($this->rawResponse->getContent());
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