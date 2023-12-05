<?php

namespace Viartfelix\Freather\meteo;

use Viartfelix\Freather\Config\Config;
use Symfony\Component\HttpClient\HttpClient;

class Carte {
  private Config $config;
  private int $x;
  private int $y;
  private int $zoom;

  private string $link;

  function __construct(Config &$config)
  {
    $this->config = &$config;
  }

  public function fetchMap(int $zoom, int $x, int $y)
  {
    $this->setX($x);
    $this->setY($y);
    $this->setZoom($zoom);

    $this->linkConstruct();
  }

  public function linkConstruct(): void
  {
    //TODO: Lien dans config vers baselink de la carte, actu et prévisions.
    $link = "http://maps.openweathermap.org/maps/2.0/weather/(OP)";
    $link .= "/".$this->getZoom();
    $link .= "/".$this->getX();
    $link .= "/".$this->getY();
    $link .= "?appid=".$this->config->getApiKey();

    $this->setLink($link);
  }

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
}

?>