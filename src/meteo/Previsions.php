<?php

namespace Viartfelix\Freather\meteo;
use Viartfelix\Freather\Adresse\Adresse;

use Viartfelix\Freather\Config\Config;
use Symfony\Component\HttpClient\HttpClient;

class Previsions {
  private Config $config;
  public float $longitude;
  public float $latitude;
  public mixed $rawResponse;
  public mixed $response;

  public int $timestamps;
  
  function __construct(
    Config $config,
    float $lat,
    float $lon,
    int $timestamps=null,
  ) {
    $this->config=$config;
    $this->latitude=$lat;
    $this->longitude=$lon;
    $this->timestamps=isset($timestamps) ? $timestamps : null;
  }

  public function exec() {
    $client=HttpClient::create();
    $res=$client->request(
      "GET",
      $this->config->apiEntrypoint . "forecast",
      [
        "verify_peer"=>false,
        "query"=>[
          "lang"=>$this->config->lang,
          "units"=>$this->config->unit,
          "lat"=>$this->latitude,
          "lon"=>$this->longitude,
          "appid"=>$this->config->apiKey,
          "cnt"=>isset($this->timestamps) ? $this->timestamps : 0,
        ],
      ]
    );

    $this->rawResponse=$res->getContent();
    $this->response=json_decode($this->rawResponse)->list;
  }

  public function getRaw() {
    return $this->rawResponse;
  }

  public function get() {
    return $this->response;
  }
}

?>