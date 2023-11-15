<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\meteo\Actu;
use Viartfelix\Freather\meteo\Previsions;

use Viartfelix\Freather\Carte\Carte;

class Freather {
  private Config $config;

  function __construct(string $key=null) {
    $this->config=new Config(
      isset($key) ? $key : null,
    );
  }

  function setKey(string $key): void {
    $this->config->apiKey=$key;
  }

  function getKey(): string {
    return $this->config->apiKey;
  }

  /** Fonction qui permet de récupérer la météo actuelle */
  function getActu(string|float $lat, string|float $lon, bool $raw) {
    $actu=new Actu(
      $this->config,
      floatval($lat),
      floatval($lon),
    );

    $actu->exec();

    return $raw===true ? $actu->getRaw() : $actu->get();
  }

  /** Fonction qui permet de récupérer le lien vers la carte */
  function getMap() {
    //TODO: Appel vers méthode dans Carte.php
  }

  /** Fonction qui permet de récupérer les préivisions météo */
  function getPrevi(string|float $lat, string|float $lon, bool $raw) {
    $previ=new Previsions(
      $this->config,
      floatval($lat),
      floatval($lon),
    );

    $previ->exec();

    return $raw===true ? $previ->getRaw() : $previ->get();
    //TODO: Appel vers méthode dans Previsions.php
  }
}

?>