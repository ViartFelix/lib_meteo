<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\Config\Config;
use Symfony\Component\HttpClient\HttpClient;

class Actu {
  private Config $config;
  private float $longitude;
  private float $latitude;

  public mixed $rawResponse;
  public mixed $response;


  function __construct(Config $config,float $latitude,float $longitude)
  {
    $this->config=$config;
    $this->latitude=$latitude;
    $this->longitude=$longitude;
  }

  public function exec() {
    $client=HttpClient::create();

    $res=$client->request(
      "GET",
      $this->config->apiEntrypoint . "weather",
      [
        "verify_peer"=>false,
        "query"=>[
          "lang"=>$this->config->lang,
          "measurement"=>$this->config->unit,
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