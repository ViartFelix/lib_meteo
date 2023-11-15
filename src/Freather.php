<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\Exceptions\FreatherException;
use Viartfelix\Freather\meteo\Actu;
use Viartfelix\Freather\meteo\Previsions;

use Viartfelix\Freather\Carte\Carte;

class Freather {
  private Config $config;

  private mixed $actuResponse;
  private mixed $previResponse;
  private mixed $carteResponse;

  public Config $previousConfig;

  function __construct(
    string $key=null,
    string $apiEntrypoint=null,
    string $lang="en",
    bool $metric=true,
    int $timestamps=-1,
  ) {
    $this->config=new Config(
      isset($key) ? $key : null,
      isset($apiEntrypoint) ? $apiEntrypoint : null,
      $lang,
      $metric,
      $timestamps,
    );
  }

  public function defineConfig(
    string $key=null,
    string $lang="en",
    bool $metric=true,
    string $entrypoint=null,
    int $timestamps=-1,
  ) {
    $this->previousConfig=$this->config;

    $this->config=new Config(
      isset($key) ? $key : $this->previousConfig->apiKey,
      isset($entrypoint) ? $entrypoint : $this->previousConfig->apiEntrypoint,
      isset($lang) ? $lang : $this->previousConfig->lang,
      isset($metric) ? $metric : false,
      isset($timestamps) ? $timestamps : $this->previousConfig->timestamps,
    );

    return $this;
  }

  public function rollbackConfig() {
    if(isset($this->previousConfig)) {
      $this->config=$this->previousConfig;
    } else {
      throw new FreatherException("No previous config to rollback to.");
    }
    
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
  function fetchMap() {
    //TODO: Appel vers méthode dans Carte.php
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
    //TODO: Appel vers méthode dans Previsions.php
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