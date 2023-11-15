<?php

namespace Viartfelix\Freather\Config;

class Config {
  public string $apiKey;
  public string $apiEntrypoint="https://api.openweathermap.org/data/2.5/";
  public string $lang;
  public string $unit;

  public function __construct(
    $key=null,
    $lang="en",
    $unit="metric",
  ) {
    if(isset($key)) $this->apiKey=$key;
    $this->lang=$lang;
    $this->unit=$unit;
  }
}

?>