<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";
use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Freather;

$a=new Freather([
  "apiKey"=>"da12be2d8d525a3ef78aff509a1b0cad",
  "lang"=>"fr",
  "measurement"=>"metric",
  "timestamps"=>56
]);



//https://api.openweathermap.org/data/2.5/weather?lat=50.6232405&lon=3.0978745&appid=da12be2d8d525a3ef78aff509a1b0cad

?>