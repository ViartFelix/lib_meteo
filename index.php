<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";
use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Exceptions\FreatherException as FreatherException;
use Viartfelix\Freather\Freather;

$a=new Freather([
  "apiKey"=>"da12be2d8d525a3ef78aff509a1b0cad",
  "lang"=>"fr",
  "measurement"=>"metric",
  "timestamps"=>56
]);

$j = $a->fetchPrevisions(
  "50.623790",
  3.097328,
)->getPrevisions(false);

var_dump($j);

?>