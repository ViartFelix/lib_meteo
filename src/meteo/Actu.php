<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\common\BaseService;
use Viartfelix\Freather\config\Cache;
use Viartfelix\Freather\Config\Config;

use Viartfelix\Freather\Exceptions\FreatherException;

class Actu extends BaseService {
	private float $longitude;
	private float $latitude;
	private array $options;

	function __construct(Config &$config, Cache &$cache)
	{
		parent::__construct($config, $cache);
	}

	public function fetchActu(float $lat, float $long, array $options): void
	{
		$this->setLat($lat);
		$this->setLon($long);
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
		$options["longitude"] = $this->getLon();

		$this->parseMode($options["mode"] ?? "json");

		$err = $this->fetchAndParse(BaseService::ACTU, $options);

        if(isset($err) && isset($err->errCode))
        {
            throw new FreatherException("Error when fetching or parsing response from server. Logs are likely present on top of this error.", $err->errCode);
        }
	}

	public function returnResults(bool $raw = false): mixed
	{
		return ($this->getRes($raw));
	}

	public function getLat(): float
	{
		return $this->latitude;
	}

	public function setLat(float $lat): void
	{
		$this->latitude = $lat;
	}

	public function getLon(): float
	{
		return $this->longitude;
	}

	public function setLon(float $long): void
	{
		$this->longitude = $long;
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