<?php

namespace Viartfelix\Freather;
use Viartfelix\Freather\Actu\Actu;
use Viartfelix\Freather\Carte\Carte;
use Viartfelix\Freather\Previsions\Previsions;

class Freather {

  public string $apiKey;

  function __construct(string $apiKey=null) {
    if(isset($apiKey)) $this->apiKey=$apiKey;
  }

  function setKey(string $key): void {
    $this->apiKey=$key;
  }

  function getKey(): string {
    return $this->apiKey;
  }

  /** Fonction qui permet de récupérer la météo actuelle */
  function getActu() {
    //TODO: Appel vers méthode dans Actu.php
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