<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\Exceptions\FreatherException;

use Viartfelix\Freather\meteo\{
  Actu,
  Previsions,
  Carte,
};

class Freather {
  private Config $config;

  private mixed $actuResponse;
  private mixed $previResponse;
  private mixed $carteResponse;

  public Config $previousConfig;

  function __construct(
    array $init=array(
      "apiKey"=>null,
      "apiEntrypoint"=>null,
      "lang"=>"en",
      "measurement"=>true,
      "timestamps"=>1,
    ),
  ) {
    $this->config=new Config([
      "apiKey"=>$init["apiKey"] ?? null,
      "apiEntrypoint"=>$init["apiEntrypoint"] ?? null,
      "lang"=>$init["lang"] ?? null,
      "measurement"=>$init["measurement"] ?? null,
      "timestamps"=>$init["timestamps"] ?? null,
    ]);
  }

  public function defineConfig(
    array $config=array(
      "apiKey"=>null,
      "apiEntrypoint"=>null,
      "lang"=>"en",
      "measurement"=>true,
      "timestamps"=>0,
    )
  ) {
    $this->previousConfig=$this->config;

    $this->config=new Config([
      "apiKey" => $config["apiKey"] ?? $this->previousConfig->apiKey,
      "apiEntrypoint" => $config["entrypoint"] ?? $this->previousConfig->apiEntrypoint,
      "lang" => $config["lang"] ?? $this->previousConfig->lang,
      "measurement" => $config["measurement"] ?? $this->previousConfig->lang,
      "timestamps" => $config["timestamps"] ?? $this->previousConfig->timestamps,
    ]);

    return $this;
  }

  public function rollbackConfig() {
    if(isset($this->previousConfig)) $this->config=$this->previousConfig;
    else throw new FreatherException("No previous config to rollback to.");
    
    return $this;
  }

   /* ---------------------------------------- Fetchers ---------------------------------------- */  

  /** Fonction qui permet de récupérer la météo actuelle */
  function fetchActu(string|float $lat, string|float $lon, bool $raw) {
    $actu=new Actu(
      $this->config,
      floatval($lat),
      floatval($lon),
    );

    $actu->exec();

    $this->actuResponse = $raw===true ? $actu->getRaw() : $actu->get();

    return $this;
  }

  /** Fonction qui permet de récupérer le lien vers la carte */
  function fetchMap(int $zoom, int $x, int $y) {

    $map = new Carte(
      $this->config,
      $zoom,
      $x,
      $y,
    );

    $this->carteResponse=$map->getLink();

    return $this;
  }

  /** Fonction qui permet de récupérer les préivisions météo */
  function fetchPrevi(string|float $lat, string|float $lon, string|int $timestamps=null, bool $raw) {
    $previ=new Previsions(
      $this->config,
      floatval($lat),
      floatval($lon),
      isset($timestamps) ? intval($timestamps) : null,
    );

    $previ->exec();

    $this->previResponse = $raw===true ? $previ->getRaw() : $previ->get();
    
    return $this;
  }

  /* ---------------------------------------- Getters and setters ---------------------------------------- */

  function setKey(string $key) {
    $this->config->apiKey=$key;

    return $this;
  }

  function getKey(): string {
    return $this->config->apiKey;
  }

  //No setters for responses. The setters are the fetch methods.

  function getActu() {
    return $this->actuResponse;
  }

  function getPrevi() {
    return $this->previResponse;
  }

  function getMap() {
    return $this->carteResponse;
  }
}

?>