<?php

namespace Viartfelix\Freather\enums;

/**
 * An enum of all map layers used for the 'Map' service.
 */
enum MapLayer
{
    /**  @var string Convective precipitation (mm) */
    case PAC0;

    /**  @var string Precipitation intensity (mm/s) */
    case PR0;

    /**  @var string Accumulated precipitation (mm) */
    case PA0;

    /**  @var string Accumulated precipitation - rain (mm) */
    case PAR0;

    /**  @var string Accumulated precipitation - snow (mm) */
    case PAS0;

    /**  @var string Depth of snow (m) */
    case SD0;

    /**  @var string Wind speed at an altitude of 10 meters (m/s) */
    case WS10;

    /**  @var string Joint display of speed wind (color) and wind direction (arrows), received by U and V components  (m/s) */
    case WND;

    /**  @var string Atmospheric pressure on mean sea level (hPa) */
    case APM;

    /**  @var string Air temperature at a height of 2 meters (°C) */
    case TA2;

    /**  @var string Temperature of a dew point (°C) */
    case TD2;

    /**  @var string Soil temperature 0-10 сm (K) */
    case TS0;

    /**  @var string Soil temperature >10 сm (K) */
    case TS10;

    /**  @var string Relative humidity (%) */
    case HRD0;

    /**  @var string Cloudiness (%) */
    case CL;
}

?>