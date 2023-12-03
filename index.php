<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";
use Spatie\Ignition\Ignition;
//Ignition::make()->register();

use Viartfelix\Freather\Exceptions\FreatherException as FreatherException;
use Viartfelix\Freather\Freather;

$a=new Freather([
  "apiKey"=>"da12be2d8d525a3ef78aff509a1b0cad",
  "lang"=>"fr",
  "measurement"=>"metric",
  "timestamps"=>"56"
]);

/*
//var_dump($a);

$j=$a->fetchActu(
  50.639129,
  3.074458,
  false,
)->getActu();

echo "<br/><br/><br/>";
$a->defineConfig([
  "lang"=>"kr",
  "timestamps"=>25,
]);

$k=$a->fetchActu(
  50.639129,
  3.074458,
  false,
)->getActu();

//var_dump($k);
echo "<br/><br/><br/>";


$l=$a->rollbackConfig()->fetchActu(
  50.639129,
  3.074458,
  false,
)->getActu();

var_dump($l);
echo "<br/><br/><br/>";



$hey=$a->fetchPrevi(
  50.639129,
  3.074458,
  10,
  false,
)->getPrevi();

foreach ($hey as $key => $value) {
  echo "<br/><br/><br/><br/><br/>";
  echo $key. " | ";
  var_dump($value);
}
*/

// try {
//   throw new FreatherException("hello");
// } catch(FreatherException $e) {
//   var_dump($e);
// }

$test = $a->fetchMap(3,5,3)->getMap();
var_dump($test);

?>