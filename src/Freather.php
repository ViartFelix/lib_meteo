<?php

namespace Viartfelix\Freather;

use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\meteo\Actu;

use Viartfelix\Freather\Carte\Carte;
use Viartfelix\Freather\Previsions\Previsions;

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
  function getPrevi() {
    //TODO: Appel vers méthode dans Previsions.php
  }
}

?>