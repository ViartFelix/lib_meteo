<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\common\BaseService;
use Viartfelix\Freather\Config\Config;
use Viartfelix\Freather\Exceptions\FreatherException;

class Previsions extends BaseService {
    private float $longitude;
    private float $latitude;
    private array $options = array();
  
    function __construct(Config &$config)
    {
        parent::__construct($config);
    }

    public function fetchPrevisions(float $lon, float $lat, array $options=array()): void
    {
        $this->setLong($lon);
        $this->setLat($lat);
        $this->setOptions($options);

        $this->prepare();
        $this->exec();
    }

    private function prepare(): void
    {}

    private function exec(): void
    {
        $options = $this->getOptions();
        $options["latitude"] = $this->getLat();
        $options["longitude"] = $this->getLong();

        $this->parseMode($options["mode"] ?? "json");

        $err = $this->fetchAndParse(BaseService::PREVISIONS, $options);

        if(isset($err->errCode)) {
            throw new FreatherException("Error when fetching or parsing response from server. Logs are likely present on top of this error.", $err->errCode);
        }
    }

    public function returnResults(bool $raw): mixed
    {
        return ($this->getRes($raw));
    }

    public function setLong(float $long): void
    {
        $this->longitude = $long;
    }

    public function getLong(): float
    {
        return $this->longitude;
    }

    public function setLat(float $lat): void
    {
        $this->latitude = $lat;
    }

    public function getLat(): float
    {
        return $this->latitude;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}

?>