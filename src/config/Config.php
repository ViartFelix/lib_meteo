<?php

namespace Viartfelix\Freather\Config;

class Config {
  public string $apiKey;
  public string $apiEntrypoint="https://api.openweathermap.org/data/2.5/";
  public string $lang;
  public string $unit;
  public string $timestamps;

  public function __construct($config=[
    "apiKey"=>null,
    "apiEntrypoint"=>null,
    "lang"=>"en",
    "measurement"=>"standard",
    "timestamps"=>null,
  ]) {
    $this->apiKey = $config["apiKey"] ?? "";
    $this->apiEntrypoint = $config["apiEntrypoint"] ?? $this->apiEntrypoint ?? "";
    $this->lang = $config["lang"] ?? "en";
    $this->timestamps = $config["timestamps"] ?? 1;
    $this->unit = $config["measurement"] ?? "standard";
  }
}

?>