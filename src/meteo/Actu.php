<?php

namespace Viartfelix\Freather\meteo;

use Illuminate\Support\Facades\Http;
use Viartfelix\Freather\Config\Config;
use Symfony\Component\HttpClient\HttpClient;
use Viartfelix\Freather\Exceptions\FreatherException;

class Actu {
  private Config $config;
  private float $longitude;
  private float $latitude;
  private array $options;

  private $client;

  private mixed $rawResponse;
  private mixed $response;

  function __construct(Config &$config)
  {
    $this->config = &$config;
  }

  public function fetchActu(float $lat, float $long, array $options): void
  {
    $this->setLat($lat);
    $this->setLon($long);

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
    $this->rawResponse = $this->client->request(
      "GET",
      $this->config->getApiEntrypoint() . "weather",
      [
        "verify_peer"=>false,
        "query"=>[
          "lat" => $this->getLat(),
          "lon" => $this->getLon(),
          "appid" => $this->config->getApiKey(),

          "lang" => $this->getOptions()["lang"] ?? $this->config->getLang() ?? "en",
          "mode" => $this->getOptions()["mode"] ?? "json",
          "units" => $this->getOptions()["unit"] ?? $this->config->getUnit() ?? "standard",
        ],
      ],
    );

    $this->response = json_decode($this->rawResponse->getContent());
  }

  public function returnResults(bool $raw = false): mixed
  {
    return ($raw ? $this->getRawResponse() : $this->getResponse());
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

  public function getResponse(): mixed
  {
    return $this->response;
  }

  public function getRawResponse(): mixed
  {
    return $this->rawResponse;
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