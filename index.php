<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";

use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Freather;

$a=new Freather([
  "lang" => "fr",
]);

$h = $a->fetchMap(
    5,
    5,
    5,
    Freather::PR0,
)->getMap();

var_dump($h);

?>