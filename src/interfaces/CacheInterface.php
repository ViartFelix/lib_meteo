<?php

namespace Viartfelix\Freather\interfaces;

interface CacheInterface
{
    /*
    public function checkItem(string $key);
    public function setItem(string $key, mixed $value);
    public function getItem(string $key);
    */

    //public function decodeUrl(string &$url): void;
    public function encodeUrl(string $url): string;
}

?>