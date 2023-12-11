<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\common\BaseService;
use Viartfelix\Freather\config\Cache;
use Viartfelix\Freather\Config\Config;

class Carte extends BaseService {
    private Config $config;
    private int $x;
    private int $y;
    private int $zoom;
    private string $op;
    private array $options = array();

    private string $link;
    private string $compiledOptions="";

    function __construct(Config &$config)
    {
        $this->config = &$config;
    }

    public function fetchMap(int $zoom, int $x, int $y, $op, array $options=[])
    {
        $this->setX($x);
        $this->setY($y);
        $this->setZoom($zoom);
        $this->setOP($op);
        $this->setOptions($options);

        $this->exec();
    }

    private function exec(): void
    {
        $this->optionsConstruct();
        $this->linkConstruct();
    }

    private function optionsConstruct(): void
    {
        if(isset($this->getOptions()["date"])) {
            $date = strtotime($this->getOptions()["date"]);
            $this->compiledOptions .= "&date=".(gettype($date)!=="boolean" ? $date : time());
        } else {
            $this->compiledOptions .= "&date=".time();
        }

        $this->compiledOptions .= "&opacity=" . ($this->getOptions()["opacity"] ?? "0.8");

        if(isset($this->getOptions()["palete"])) {
            $this->compiledOptions .= "&palete=";

            foreach ($this->getOptions()["palete"] as $index => $color) {
                $this->compiledOptions .= (($index==0 || array_key_last($this->getOptions()["palete"])==$index) ? "" : ";") . $color;
            }
        }

        $this->compiledOptions .= "&fill_bound=" . ($this->getOptions()["fill_bound"] ?? "false");

        $this->compiledOptions .= "&arrow_step=" . ($this->getOptions()["arrow_step"] ?? "32");

        $this->compiledOptions .= "&use_norm=" . ($this->getOptions()["use_norm"] ?? "false");
    }

    private function linkConstruct(): void
    {
        $link = $this->config->getMapEntrypoint();
        $link .= "/".$this->getOP();
        $link .= "/".$this->getZoom();
        $link .= "/".$this->getX();
        $link .= "/".$this->getY();
        $link .= "?appid=".$this->config->getApiKey();

        $link .= $this->getCompiledOptions();

        $this->setLink($link);
    }

    /* ---------------------------------------- Getters and setters ---------------------------------------- */

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX(int $x): void
    {
        $this->x = $x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y): void
    {
        $this->y = $y;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function setZoom(int $zoom): void
    {
        $this->zoom = $zoom;
    }

    public function getOP(): string
    {
        return $this->op;
    }

    public function setOP(string $op): void
    {
        $this->op = $op;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getCompiledOptions(): string
    {
        return $this->compiledOptions;
    }

    public function setCompiledOptions(string $options): void
    {
        $this->compiledOptions = $options;
    }
}

?>