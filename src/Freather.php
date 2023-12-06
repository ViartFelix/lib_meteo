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
  private Actu $actu;
  private Carte $carte;
  private Previsions $previsions;

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

    $this->actu = new Actu($this->config);
    $this->carte = new Carte($this->config);
    $this->previsions = new Previsions($this->config);
  }

  /* ---------------------------------------- Class-specific methods ---------------------------------------- */

   /* ------------------------- Config ------------------------- */
  public function defineConfig(array $config): Freather
  {
    $this->config->defineConfig($config);
    return $this;
  }

  public function rollbackConfig(): Freather
  {
    $this->config->rollbackConfig();
    return $this;
  }

  /* ------------------------- Actu ------------------------- */
  public function fetchActu(string|float $latitude, string|float $longitude): Freather
  {
    $this->actu->fetchActu(
      floatval($latitude),
      floatval($longitude),
    );

    return $this;
  }

  public function getActu(bool $raw = false): mixed
  {
    return $this->actu->returnResults($raw);
  }

  /* ------------------------- Carte ------------------------- */
  /** Fonction qui permet de récupérer le lien vers la carte */
  public function fetchMap(int $zoom, int $x, int $y)
  {
    $this->carte->fetchMap($zoom,$x,$y);
    return $this;
  }

  public function getMap(): mixed
  {
    return $this->carte->getLink();
  }

  /* ------------------------- Prévisions ------------------------- */
  /** Fonction qui permet de récupérer les préivisions météo */
  function fetchPrevisions(string|float $lat, string|float $lon): Freather
  {
    $this->previsions->fetchPrevisions(
      floatval($lon),
      floatval($lat)
    );

    return $this;
  }

  public function getPrevisions(bool $raw = false): mixed
  {
    return $this->previsions->returnResults($raw);
  }

  /* ---------------------------------------- Getters and setters ---------------------------------------- */

  /* ------------------------- Config ------------------------- */

  public function getConfig(): array
  {
    return $this->config->getConfig();
  }

  public function getLastConfig(): array
  {
    return $this->config->getLastConfig();
  }

  public function setConfig(array $config): void
  {
    $this->defineConfig($config);
  }
}

?>