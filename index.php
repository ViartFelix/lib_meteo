<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";
use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Exceptions\FreatherException as FreatherException;
use Viartfelix\Freather\Freather;
use Viartfelix\Freather\meteo\Carte;

$a=new Freather([
  "apiKey"=>"da12be2d8d525a3ef78aff509a1b0cad",
  "lang"=>"fr",
  "measurement"=>"metric",
  "timestamps"=>56
]);

$j = $a->fetchMap(
  5,
  5,
  5,
  Carte::TD2,
)->getMap();

var_dump($j)

?>