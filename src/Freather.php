<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\meteo\Actu;
use Viartfelix\Freather\meteo\Previsions;

use Viartfelix\Freather\Carte\Carte;

class Freather {
  private Config $config;

  private mixed $actuResponse;
  private mixed $previResponse;
  private mixed $carteResponse;

  function __construct(string $key=null) {
    $this->config=new Config(
      isset($key) ? $key : null,
    );
  }

  

  /** Fonction qui permet de récupérer la météo actuelle */
  function fetchActu(string|float $lat, string|float $lon, bool $raw) {
    $actu=new Actu(
      $this->config,
      floatval($lat),
      floatval($lon),
    );

    $actu->exec();

    return $raw===true ? $actu->getRaw() : $actu->get();
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
  }

  function setKey(string $key): void {
    $this->config->apiKey=$key;
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