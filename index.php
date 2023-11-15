<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";
use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Exceptions\FreatherException as FreatherException;
use Viartfelix\Freather\Freather;

$a=new Freather("da12be2d8d525a3ef78aff509a1b0cad");

// $a->fetchActu(
//   50.639129,
//   3.074458,
//   false,
// );

$a->fetchPrevi(
  50.639129,
  3.074458,
  10,
  false,
);

$hey=$a->getPrevi();

foreach ($hey as $key => $value) {
  echo "<br/><br/><br/><br/><br/>";
  echo $key. " | ";
  var_dump($value);
}





// try {
//   throw new FreatherException("hello");
// } catch(FreatherException $e) {
//   var_dump($e);
// }

?>