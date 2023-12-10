<?php

namespace Viartfelix\Freather\Config;

use Viartfelix\Freather\Exceptions\FreatherException;

class Config {
    private string $apiKey;

    private string $defaultActuEntrypoint = "https://api.openweathermap.org/data/2.5/weather";
    private string $actuEntrypoint = "https://api.openweathermap.org/data/2.5/weather";

    private string $defaultPreviEntrypoint = "https://api.openweathermap.org/data/2.5/forecast";
    private string $previEntrypoint = "https://api.openweathermap.org/data/2.5/forecast";

    private string $defaultMapEntrypoint = "http://maps.openweathermap.org/maps/2.0/weather";
    private string $mapEntrypoint = "http://maps.openweathermap.org/maps/2.0/weather";

    private string $lang;
    private string $unit;
    private int $timestamps;
    private array $currConfig;
    private array $lastConfig;

    public function __construct($config=[
        "apiKey"=>null,
        "apiEntrypoint"=>null,
        "lang"=>"en",
        "measurement"=>"standard",
        "timestamps"=>null,
    ]) {
        $this->defineConfig($config);
    }

    public function defineConfig(array $config=array(
        "apiKey"=>null,
        "apiEntrypoint"=>null,
        "lang"=>"en",
        "measurement"=>true,
        "timestamps"=>0,
    )) {

        if(isset($this->currConfig))
        {
            $this->lastConfig = $this->currConfig;
        }

        if(!isset($config["apiKey"]) && !isset($this->lastConfig["apiKey"])) throw new FreatherException("Error when preparing query: API key is required. Please see https://openweathermap.org/api to get one.", 1);

        $this->setApiKey($config["apiKey"] ?? $this->getApiKey() ?? "");
        $this->setLang($config["lang"] ?? $this->getLang() ?? "en");
        $this->setTimestamps($config["timestamps"] ?? $this->getTimestamps() ?? 1);
        $this->setUnit($config["measurement"] ?? $this->getUnit() ?? "standard");

        $this->setActuEntrypoint($config["actu_entrypoint"] ?? $this->getActuEntrypoint() ?? $this->defaultActuEntrypoint);
        $this->setMapEntrypoint($config["map_entrypoint"] ?? $this->getMapEntrypoint() ?? $this->defaultMapEntrypoint);
        $this->setPreviEntrypoint($config["previ_entrypoint"] ?? $this->getPreviEntrypoint() ?? $this->defaultPreviEntrypoint);

        $this->currConfig = array(
            "apiKey" => $this->getApiKey(),
            "lang" => $this->getLang(),
            "measurement" => $this->getTimestamps(),
            "timestamps" => $this->getUnit(),

            "actu_entrypoint" => $this->getActuEntrypoint(),
            "map_entrypoint" => $this->getMapEntrypoint(),
            "previ_entrypoint" => $this->getPreviEntrypoint(),
            
        );
    }

    public function rollbackConfig()
    {
        if(isset($this->lastConfig)) $this->currConfig=$this->lastConfig;
        else throw new FreatherException("No previous config to rollback to.");
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

    public function getActuEntrypoint(): string|null
    {
        return $this->actuEntrypoint ?? null;
    }

    public function setActuEntrypoint(string $actuEntrypoint): void
    {
        $this->actuEntrypoint = $actuEntrypoint;
    }

    public function getPreviEntrypoint(): string|null
    {
        return $this->previEntrypoint ?? null;
    }

    public function setPreviEntrypoint(string $previEntrypoint): void
    {
        $this->previEntrypoint = $previEntrypoint;
    }

    public function getMapEntrypoint(): string|null
    {
        return $this->mapEntrypoint ?? null;
    }

    public function setMapEntrypoint(string $mapEntrypoint): void
    {
        $this->mapEntrypoint = $mapEntrypoint;
    }
}

?>