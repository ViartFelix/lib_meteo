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
  
  function __construct(
    Config $config,
    float $lat,
    float $lon,
  ) {
    $this->config=$config;
    $this->latitude=$lat;
    $this->longitude=$lon;
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
        ],
      ]
    );

    $this->rawResponse=$res->getContent();
    $this->response=json_decode($this->rawResponse);
  }

  public function getRaw() {
    return $this->rawResponse;
  }

  public function get() {
    return $this->response;
  }


}

?>