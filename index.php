<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";
use Spatie\Ignition\Ignition;
Ignition::make()->register();

use Viartfelix\Freather\Exceptions\FreatherException as FreatherException;
use Viartfelix\Freather\Freather;

$a=new Freather("da12be2d8d525a3ef78aff509a1b0cad");

var_dump($a->getActu(
  50.639129,
  3.074458,
  false,
));




// try {
//   throw new FreatherException("hello");
// } catch(FreatherException $e) {
//   var_dump($e);
// }

?>