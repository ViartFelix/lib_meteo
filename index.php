<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";

use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Freather;

$a=new Freather([
  "apiKey"=>"da12be2d8d525a3ef78aff509a1b0cad",
]);

//TODO: Commentaires sur fonctions en frontal

$j = $a->fetchActu(
    50.6232356,
    3.0979012,
    [
        "mode" => "xml"
    ]
)->getActu();

$h = $a->setConfig([
    "lang" => "jp",
    "measurement" => "imperial",
    "timestamps" => 14,
])->fetchActu(
    50.6232356,
    3.0979012,
    [
        "mode" => "xml",
    ]
)->getActu();

$a->rollbackConfig();

$b = $a->fetchPrevisions(
    50.6232356,
    3.0979012,
    [
        "mode" => "xml"
    ]
)->getPrevisions();

var_dump($h);
echo "--------------------------------------------<br/>";
var_dump($b);

?>