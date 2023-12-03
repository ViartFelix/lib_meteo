<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\Config\Config;
use Symfony\Component\HttpClient\HttpClient;

class Carte {
  private Config $config;
  public int $x;
  public int $y;
  public int $zoom;
  public mixed $rawResponse;
  public mixed $response;

  function __construct(Config $config, int $zoom, int $x, int $y) {
    $this->config = $config;
    $this->x = $x;
    $this->y = $y;
    $this->zoom = $zoom;
  }

  public function getLink(): string {
    return "http://maps.openweathermap.org/maps/2.0/weather/(OP)/".$this->zoom."/".$this->x."/".$this->y."?appid=".$this->config->apiKey;
  }

  public function getRaw() {
    return $this->rawResponse;
  }

  public function get() {
    return $this->response;
  }
}

?>