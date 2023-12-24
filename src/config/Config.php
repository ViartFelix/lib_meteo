<?php

namespace Viartfelix\Freather\Config;

use Viartfelix\Freather\Exceptions\FreatherException;


class Config {
    private string $apiKey;

    private string $defaultCurrentEntrypoint = "https://api.openweathermap.org/data/2.5/weather";
    private string $currentEntrypoint = "https://api.openweathermap.org/data/2.5/weather";

    private string $defaultForecastEntrypoint = "https://api.openweathermap.org/data/2.5/forecast";
    private string $forecastEntrypoint = "https://api.openweathermap.org/data/2.5/forecast";

    private string $defaultMapEntrypoint = "https://maps.openweathermap.org/maps/2.0/weather";
    private string $mapEntrypoint = "https://maps.openweathermap.org/maps/2.0/weather";

    private string $lang;
    private string $unit;
    private int $timestamps;
    private array $currConfig;
    private array $lastConfig;

    private Cache $cache;

    //Public to be able to be passed by reference across all library
    public int $cacheDuration;
    private int $defaultCacheDuration = -1;

    public function __construct($config=[
        "apiKey"=>null,
        "lang"=>"en",
        "measurement"=>"standard",
        "timestamps"=>null,

        "currentEntrypoint"=>null,
        "mapEntrypoint"=>null,
        "forecastEntrypoint"=>null,

        "cacheDuration"=>-1,
    ]) {
        $this->defineConfig($config);
    }

    public function defineConfig(array $config=array(
        "apiKey"=>null,
        "lang"=>"en",
        "measurement"=>true,
        "timestamps"=>0,

        "currentEntrypoint"=>null,
        "mapEntrypoint"=>null,
        "forecastEntrypoint"=>null,

        "cacheDuration"=>-1,
    )) {

        if(isset($this->currConfig))
        {
            $this->lastConfig = $this->currConfig;
        }

        if(!isset($config["apiKey"]) && !isset($this->lastConfig["apiKey"])) throw new FreatherException("Error when preparing query: API key is required. Please see https://openweathermap.org/api to get one.", 1);

        $this->setParams($config);

        if(isset($this->cache))
        {
            $this->cache = new Cache($this->cacheDuration);
        }

        $this->currConfig = array(
            "apiKey" => $this->getApiKey(),
            "lang" => $this->getLang(),
            "measurement" => $this->getUnit(),
            "timestamps" => $this->getTimestamps(),

            "currentEntrypoint" => $this->getCurrentEntrypoint(),
            "mapEntrypoint" => $this->getMapEntrypoint(),
            "forecastEntrypoint" => $this->getForecastEntrypoint(),

            "cacheDuration" => $this->getCacheDuration(),
        );


    }

    public function rollbackConfig()
    {
        if(isset($this->lastConfig)) {
            $this->currConfig = $this->lastConfig;
            $this->setParams($this->getConfig());
        }
        else {
            throw new FreatherException("No previous config to rollback to.");
        }
    }

    /**
     * Will set the params of the config. Used to set and update params on all Services.
     * @param array $options
     * @return void
     */
    private function setParams(array $options = array(
        "apiKey"=>null,
        "lang"=>"en",
        "measurement"=>true,
        "timestamps"=>0,

        "currentEntrypoint"=>null,
        "mapEntrypoint"=>null,
        "forecastEntrypoint"=>null,

        "cacheDuration"=>-1,
    ))
    {
        $this->setApiKey($options["apiKey"] ?? $this->getApiKey() ?? "");
        $this->setLang($options["lang"] ?? $this->getLang() ?? "en");
        $this->setTimestamps($options["timestamps"] ?? $this->getTimestamps() ?? 1);
        $this->setUnit($options["measurement"] ?? $this->getUnit() ?? "standard");

        $this->setCurrentEntrypoint($options["currentEntrypoint"] ?? $this->getCurrentEntrypoint() ?? $this->defaultCurrentEntrypoint);
        $this->setMapEntrypoint($options["mapEntrypoint"] ?? $this->getMapEntrypoint() ?? $this->defaultMapEntrypoint);
        $this->setForecastEntrypoint($options["previEntrypoint"] ?? $this->getForecastEntrypoint() ?? $this->defaultForecastEntrypoint);

        $this->setCacheDuration($options["cacheDuration"] ?? $this->getCacheDuration() ?? $this->defaultCacheDuration);

    }

    /* ---------------------------------------- Getters and setters ---------------------------------------- */

    public function getConfig(): array
    {
        return $this->currConfig;
    }

    public function getLastConfig(): array
    {
        return $this->lastConfig;
    }

    public function setConfig(array $config): void
    {
        $this->defineConfig($config);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $key): void
    {
        $this->apiKey = $key;
    }

    public function getLang(): string|null
    {
        return $this->lang ?? null;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    public function getUnit(): string|null
    {
        return $this->unit ?? null;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function getTimestamps(): int|null
    {
        return $this->timestamps ?? null;
    }

    public function setTimestamps(int $timstamps): void
    {
        $this->timestamps = $timstamps;
    }

    public function getCurrentEntrypoint(): string|null
    {
        return $this->currentEntrypoint ?? null;
    }

    public function setCurrentEntrypoint(string $currentEntrypoint): void
    {
        $this->currentEntrypoint = $currentEntrypoint;
    }

    public function getForecastEntrypoint(): string|null
    {
        return $this->forecastEntrypoint ?? null;
    }

    public function setForecastEntrypoint(string $forecastEntrypoint): void
    {
        $this->forecastEntrypoint = $forecastEntrypoint;
    }

    public function getMapEntrypoint(): string|null
    {
        return $this->mapEntrypoint ?? null;
    }

    public function setMapEntrypoint(string $mapEntrypoint): void
    {
        $this->mapEntrypoint = $mapEntrypoint;
    }

    public function getCacheDuration(): int|null
    {
        return $this->cacheDuration ?? null;
    }

    public function setCacheDuration(int $seconds): void
    {
        $this->cacheDuration = $seconds;
    }
}

?>