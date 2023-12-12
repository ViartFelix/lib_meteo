<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";

use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Freather;
use Viartfelix\Freather\meteo\Adresses;

$Freather = new Freather(
    "da12be2d8d525a3ef78aff509a1b0cad",
    [
        "lang" => "kr",
        "measurement" => "metric"
    ]
);



?>