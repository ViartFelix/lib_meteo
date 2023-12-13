<?php

namespace Viartfelix\Freather\common;

use stdClass;
use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\config\Cache;


class Baser
{
    private Config $config;
    private Cache $cache;

    public const ACTU = 1;
    public const PREVISIONS = 2;

    
    function __construct(Config &$config, Cache &$cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }


}

?>