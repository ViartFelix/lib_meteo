<?php

namespace Viartfelix\Freather\Config;

class Config {
  public string $apiKey;
  public string $apiEntrypoint="https://api.openweathermap.org/data/2.5/";
  public string $lang;
  public string $unit;
  public string $timestamps;

  public function __construct(
    $key=null,
    $api=null,
    $lang="en",
    $metric=true,
    $timestamps=null,
  ) {
    if(isset($key)) $this->apiKey=$key;
    if(isset($api)) $this->apiEntrypoint=$api;
    
    if(isset($timestamps) && is_numeric($timestamps)) $this->timestamps=$timestamps;
    else $this->timestamps=-1;
    
    $this->lang=$lang;

    if(!isset($metric)) $this->unit="metric";
    else $this->unit = $metric===true ? "metric" : "imperial";

    
  }
}

?>