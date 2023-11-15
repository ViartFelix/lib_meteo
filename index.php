<?php
//THIS IS A TEST FILE
require __DIR__."/vendor/autoload.php";

use Viartfelix\Freather\Exceptions\FreatherException as FreatherException;
use Viartfelix\Freather\Freather;

$a=new Freather("kjqzdnjkqzndj");

try {
  throw new FreatherException("hello");
} catch(FreatherException $e) {
  var_dump($e);
}

?>