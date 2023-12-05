<?php

namespace Viartfelix\Freather\Config;

use Viartfelix\Freather\Exceptions\FreatherException;

class Config {
  private string $apiKey;
  private string $apiEntrypoint="https://api.openweathermap.org/data/2.5/";
  private string $lang;
  private string $unit;
  private int $timestamps;
  private array $currConfig;
  private array $lastConfig;

  public function __construct($config=[
    "apiKey"=>null,
    "apiEntrypoint"=>null,
    "lang"=>"en",
    "measurement"=>"standard",
    "timestamps"=>null,
  ]) {
    $this->defineConfig($config);
  }

  public function defineConfig(array $config=array(
    "apiKey"=>null,
    "apiEntrypoint"=>null,
    "lang"=>"en",
    "measurement"=>true,
    "timestamps"=>0,
  )) {
    if(isset($this->currConfig)) {
      $this->lastConfig = $this->currConfig;
    }

    $this->apiKey = $config["apiKey"] ?? $this->getApiKey() ?? "";
    $this->apiEntrypoint = $config["apiEntrypoint"] ?? $this->getApiEntrypoint() ?? "";
    $this->lang = $config["lang"] ?? $this->getLang() ?? "en";
    $this->timestamps = $config["timestamps"] ?? $this->getTimestamps() ?? 1;
    $this->unit = $config["measurement"] ?? $this->getUnit() ?? "standard";

    $this->currConfig = array(
      "apiKey"=>$this->apiKey,
      "apiEntrypoint"=>$this->apiEntrypoint,
      "lang"=>$this->lang,
      "measurement"=>$this->timestamps,
      "timestamps"=>$this->unit,
    );
  }

  public function rollbackConfig()
  {
    if(isset($this->lastConfig)) $this->currConfig=$this->lastConfig;
    else throw new FreatherException("No previous config to rollback to.");
  }

  /* ---------------------------------------- Getters and setters ---------------------------------------- */

  public function getConfig(): array
  {
    return $this->currConfig;
  }

  public function getLastConfig(): array
  {
    return $this->lastConfig;
  }

  public function setConfig(array $config): void
  {
    $this->defineConfig($config);
  }

  public function getApiKey(): string
  {
    return $this->apiKey;
  }

  public function setApiKey(string $key): void
  {
    $this->apiKey = $key;
  }

  public function getApiEntrypoint(): string
  {
    return $this->apiEntrypoint;
  }

  public function setApiEntrypoint(string $entrypoint): void
  {
    $this->apiEntrypoint = $entrypoint;
  }

  public function getLang(): string
  {
    return $this->lang;
  }

  public function setLang(string $lang): void
  {
    $this->lang = $lang;
  }

  public function getUnit(): string
  {
    return $this->unit;
  }

  public function setUnit(string $unit): void
  {
    $this->unit = $unit;
  }

  public function getTimestamps(): int
  {
    return $this->timestamps;
  }

  public function setTimestamps(int $timstamps): void
  {
    $this->timestamps = $timstamps;
  }
}

?>