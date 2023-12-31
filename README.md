# FreaTher

## Weather lib meats simplicity
<p style="text-align: center">Free + Weather = <b>Freather</b></p>

## Install
*Composer*
```sh
composer require 'FelixViart/freather'
```

*PHP extensions* <br/>
You need to enable the following PHP extensions to be able to use Freather:

| Name     | Reason                                                                                                                           |
|----------|----------------------------------------------------------------------------------------------------------------------------------|
| PHP Curl | Because Freather uses Symphony's HTTP client (and by extension, the PHP Curl extension), the Curl PHP extension must be enabled. | 

*API Key* <br/>
To be able to communicate and use Freather, an API key is required when creating the Freather instance. See [this link](https://home.openweathermap.org/api_keys) on how to get one.

**Beware**: when a key to openweathermap has been created, it will not be usable for several minutes / hours / days.

And that's it, you can now use Freather in all your projects, even for commercial use, I don't mind :).
## 3 distinct services:
All the services function as 'fetcher' and 'getter':

*First you explicitly have to tell Freather to Fetch the ressource, and only then you can get the ressource.*

When fetching data, Freather will store the response it and can be accessible when using the getter.

You can specify in the getter if Freather have to reset the stored responses array (default is yes: true).
### Syntax for all services:
#### Current:
Fetches the current forecast at a specified location.
```php
fetchCurrent()->getAllCurrent();
//OR if you directly want the result
fetchGetCurrent();
```

#### Forecast:
Fetches the future forecats on a period of 5 days, at a defined latitude and longitude
```php
fetchForecast()->getAllForecast();
//OR if you directly want the result
fetchGetForecast();
```

#### Map:
Construct a link to get the desired map, alongside layers and other filters.
```php
fetchMap()->getMap();
//OR if you directly want the result
fetchGetMap();
```

### Current
#### Description
Allows one to get the current weather in a specified longitude and latitude, or at a specified location (see the Addresses documentation).

They are 2 modes available for you:
1) Latitude (p1) and Longitude (p2). These two parameters are mandatory, or else a FreatherException will be thrown.
2) Addresses (p1). Only p1 is mandatory, so putting ``null`` to p2 is completely fine, Freather will just ignore it.

The response mode is optional, and will automatically default to "json".
#### Documentation
*How to get the current weather at position 0, 0 ?*
```php
use Viartfelix\Freather\Freather;

//Defining API key
$Freather = new Freather(
	"(your API key)",
);

//fetching from Openweathermap at lat 0 and lon 0
$Freather->fetchCurrent(
    //lat
    0,
    //lon
    0,
)->getAllCurrent();
```

With this, the constructed URL will be:
"https://api.openweathermap.org/data/2.5/weather?lat=0&lon=0&appid=(your_api_key)&units=standard&lang=en&cnt=1&mode=json&isRaw=false"

*How to get the current weather a position 0, 0 with the xml response mode, and with the raw, unfiltered response ?*
```php
use Viartfelix\Freather\Freather;

//Defining API key
$Freather = new Freather(
	"(your API key)",
);

$Freather->fetchCurrent(
    //lat
    0,
    //lon
    0,
    //raw ?
    true,
    [
	    //response mode
            "mode" => "xml"
    ]
)->getAllCurrent();
```

#### Modes
##### Latitude and Longitude
###### Structure
```php
//fetchCurrent: tels Freather to fetch the data to OpenWeatherMap
fetchCurrent(
	latitude,
	longitude,
	raw,
	options: [
		"mode" => //(...)
	]
)

//getAllCurrent: tels Freather to get back the stored results.
getAllCurrent()
```

###### Possible value types
| Property          | Name           | Description                                                                                             | Type                 | Range           | Default       | Required ? |
|-------------------|----------------|---------------------------------------------------------------------------------------------------------|----------------------|-----------------|---------------|------------|
| $latitude         | Latitude (p1)  | The latitude at which to get the current weather. Rounded at 8 digits                                   | string, int or float | -90 to 90       | None          | [x] yes    |
| $longitude        | Longitude (p2) | The longitude at which to get the current weather. Rounded at 8 digits                                  | string, int or float | -180 to 180     | None          | [x] yes    |
| $raw              | IsRaw          | If the response should be the raw response from Openweathermap or not (basic json or xml parse if true) | bool                 | true or false   | false         | [ ] no     |
| $options          | Options        | An array of options for the query                                                                       | array                | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode  | The response mode of OpenWeatherMap.                                                                    | string               | 'json' or 'xml' | 'json'        | [ ] no     |

##### Addresses
###### Structure
```php
//fetchCurrent: tels Freather to fetch the data to OpenWeatherMap
fetchCurrent(
	Addresses,
	null,
	raw,
	options: [
		"mode" => "json"|"xml"
	]
)

//getAllCurrent: tels Freather to get back the stored results.
getAllCurrent()
```
###### Possible value types
| Property          | Name           | Description                                                                                                                                                                            | Type      | Range           | Default       | Required ? |
|-------------------|----------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------|-----------------|---------------|------------|
| $Addresses        | Addresses (p1) | The Addresses instance. See Addresses documentation for more details                                                                                                                   | Addresses | none            | none          | [x] yes    |
| $p2               | P2             | With the Addresses system, no need to put anything in this param. Null is acceptable here.                                                                                             | none      | none            | null          | [ ] no     |
| $raw              | IsRaw          | If the response should be the raw response from Openweathermap or not (basic json or xml parse if true). *Note: if put true here, you will not get the FreatherInfos in the response.* | bool      | true or false   | false         | [ ] no     |
| $options          | Options        | An array of options for the query                                                                                                                                                      | array     | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode  | The response mode of OpenWeatherMap.                                                                                                                                                   | string    | 'json' or 'xml' | 'json'        | [ ] no     |


### Forecast
#### Description
Allows one to get the future forecasts in a specified longitude and latitude, or at a specified location (see the Addresses documentation).

They are 2 modes available for you:
1) Latitude (p1) and Longitude (p2). These two parameters are mandatory, or else a FreatherException will be thrown.
2) Addresses (p1). Only p1 is mandatory, so putting ``null`` to p2 is completely fine, Freather will just ignore it.

The response mode is optional, and will automatically default to "json".

**This service will make heavy use of the 'timestamps' (or cnt for OWM), defined and definable in the configuration. See configurations documentation for more informations.**

See below for the documentation and syntax of this service.
#### Documentation
*How to get forecasts at position 0, 0 with 15 timestamps ?*
```php
use Viartfelix\Freather\Freather;

$Freather=new Freather(
	"(your API key)",
	[
	  "timestamps" => 15,//The number of timestamps in the response.
	]
);

//fetch the forecast
$Freather->fetchForecast(
	//lat
    0,
    //lon
    0,
)->getAllForecast();
```

With this, the constructed URL will be:
"https://api.openweathermap.org/data/2.5/forecast?lat=0&lon=0&appid=(your_api_key)&units=standard&lang=en&cnt=1&mode=json&isRaw=false"

*Same thing but with the xml response mode, in raw, with the Japanese language and using the metric system:*
```php
use Viartfelix\Freather\Freather;

$Freather = new Freather(
	"(your API key)",
	[
		"lang" => "jp",
		"measurement" => "metric",
		"timestamps" => 15,
	]
);

$Freather->fetchForecast(
    0,
    0,
    true,
    [
	    "mode" => "xml"
    ]
)->getAllForecast();
```
With this, the constructed URL will be:
"https://api.openweathermap.org/data/2.5/forecast?lat=0&lon=0&appid=(your_api_key)&units=metric&lang=jp&cnt=15&mode=xml&isRaw=true"
#### Modes
##### Latitude and Longitude
###### Structure
```php
//fetchCurrent: tels Freather to fetch the data to OpenWeatherMap
fetchForecast(
	latitude,
	longitude,
	raw,
	options: [
		"mode" => "json"|"xml"
	]
)

//getAllCurrent: tels Freather to get back the stored results.
getAllForecast()
```

###### Possible value types
| Property          | Name           | Description                                                                                             | Type                 | Range           | Default       | Required ? |
|-------------------|----------------|---------------------------------------------------------------------------------------------------------|----------------------|-----------------|---------------|------------|
| $latitude         | Latitude (p1)  | The latitude at which to get the current weather. Rounded at 8 digits                                   | string, int or float | -90 to 90       | None          | [x] yes    |
| $longitude        | Longitude (p2) | The longitude at which to get the current weather. Rounded at 8 digits                                  | string, int or float | -180 to 180     | None          | [x] yes    |
| $raw              | IsRaw          | If the response should be the raw response from Openweathermap or not (basic json or xml parse if true) | bool                 | true or false   | false         | [ ] no     |
| $options          | Options        | An array of options for the query                                                                       | array                | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode  | The response mode of OpenWeatherMap.                                                                    | string               | 'json' or 'xml' | 'json'        | [ ] no     |

##### Addresses
###### Structure
```php
//fetchCurrent: tels Freather to fetch the data to OpenWeatherMap
fetchForecast(
	Addresses,
	null,
	raw,
	options: [
		"mode" => "json"|"xml"
	]
)

//getAllCurrent: tels Freather to get back the stored results.
getAllForecast()
```
###### Possible value types
| Property          | Name           | Description                                                                                                                                                                            | Type      | Range           | Default       | Required ? |
|-------------------|----------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------|-----------------|---------------|------------|
| $Addresses        | Addresses (p1) | The Addresses instance. See Addresses documentation for more details                                                                                                                   | Addresses | none            | none          | [x] yes    |
| $p2               | P2             | With the Addresses system, no need to put anything in this param. Null is acceptable here.                                                                                             | none      | none            | null          | [ ] no     |
| $raw              | IsRaw          | If the response should be the raw response from Openweathermap or not (basic json or xml parse if true). *Note: if put true here, you will not get the FreatherInfos in the response.* | bool      | true or false   | false         | [ ] no     |
| $options          | Options        | An array of options for the query                                                                                                                                                      | array     | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode  | The response mode of OpenWeatherMap.                                                                                                                                                   | string    | 'json' or 'xml' | 'json'        | [ ] no     |

### Map
#### Description
Allows one to get the link to a map with defined filters, and other definable options *(see structure for details)*

See below for the documentation and syntax of this service.
#### Documentation
<p style="text-align: center; font-weight: bold">BEWARE</p>
<p style="text-align: center">Please refer to <a href="https://openweathermap.org/faq#zoom_levels">the zoom levels</a> (https://openweathermap.org/faq#zoom_levels) and <a href="https://openweathermap.org/api/weather-map-2">the official documentation</a> (https://openweathermap.org/api/weather-map-2) to get meanings for the zoom, x, y and all the options, or you might get unwanted results from this method.</p>

*How to get the link to a map, X and Y at 255, zoom at 5 and the atmospheric pressure layer (APM) ?*
```php
use Viartfelix\Freather\Freather;  
use Viartfelix\Freather\enums\MapLayer;

$Freather = new Freather(  
    "(your api Key)",
);

$Freather->fetchMap(
	//zoom
    5,
    //x
    255,
    //y  
    255,
    //Map Layer (enum)
    MapLayer::APM,
)->getAllMap();
```
With this, the built link will be:
"https://maps.openweathermap.org/maps/2.0/weather/APM/5/255/255?appid=(your_api_key)&date=1703950619&opacity=0.8&fill_bound=false&arrow_step=32&use_norm=false"

*How to get the link to a map, X at 399, Y at 420, zoom at 1, the relative humidity layer (HRD0) and with a bunch of options ?*
```php
$Freather->fetchMap(  
    //zoom  
    1,  
    //x  
    399,  
    //y  
    420,  
    //Map Layer (enum)  
    MapLayer::HRD0,  
    //Options  
    [  
        //Opacity  
        "opacity" => 0.1,  
        //color palette, a pretty blue  
        "palette" => "#4c2deb",  
        //Values outside the map will not have a color  
        "fill_bound" => false,  
        //16 pixels between all arrows if wind  
        "arrow_step" => 16  
    ]  
)->getAllMap();
```

With this, the built link will be:
"https://maps.openweathermap.org/maps/2.0/weather/HRD0/1/399/420?appid=da12be2d8d525a3ef78aff509a1b0cad&date=1703949708&opacity=0.1&palette=#ff00ff&fill_bound=&arrow_step=16&use_norm=true"
#### Structure
```php
//fetchMap: tels Freather to build the map link
fetchMap(
	zoom,
	x,
	y,
	op,
	options: [],
)

//getAllMap: tels Freather to get back the stored results.
getAllMap()
```

#### Possible value types
| Property | Name     | Description                                                                                                                                             | Types                          | Range                             | Default     | Required ? |
|----------|----------|---------------------------------------------------------------------------------------------------------------------------------------------------------|--------------------------------|-----------------------------------|-------------|------------|
| $zoom    | Zoom     | The zoom value to use. Values must range between 0 and 9 (inclusive), or else a FreatherException will be thrown.                                       | string, float or int           | 0 (inclusive) to 9 (inclusive)    | none        | [x] yes    |
| $x       | X        | The X coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.                                   | string, float or int           | 0 (inclusive) and 511 (inclusive) | none        | [x] yes    |
| $y       | Y        | The Y coordinate to use. Values must range between 0 and 511 (inclusive), or else a FreatherException will be thrown.                                   | string, float or int           | 0 (inclusive) and 511 (inclusive) | none        | [x] yes    |
| $op      | MapLayer | The map Layer to use. Please refer to [this link](https://openweathermap.org/api/weather-map-2#layers), or the MapLayer enum to get the layer you want. | MapLayer<br>(enum in Freather) | none                              | none        | [x] yes    |
| $options | Options  | The optional parameters to use as an associative array. Possible parameters are found bellow.                                                           | array                          | none                              | empty array | [ ] no     |

#### Possible value types (options array)
*Note: All values bellow are optional*

| Property   | Name            | Description                                                                            | Types                      | Range                          | Default                          |
|------------|-----------------|----------------------------------------------------------------------------------------|----------------------------|--------------------------------|----------------------------------|
| date       | Date            | A unix timestamp (UTC). Defaults to the current Unix timestamp (UTC) if not specified. | string                     | none                           | Current unix timestamp           |
| opacity    | Opacity         | The opacity of the map layer.                                                          | string, float or int       | 0 (inclusive) to 1 (inclusive) | 0.8                              |
| palette    | Color palette   | The palette of HEX colors to use for the map.                                          | string or array of strings | none                           | Default OpenWeatherMap's palette |
| fill_bound | Fill bound      | If values outside ranges will be filled with the nearest colour of the palette.        | bool or string             | true or false                  | false                            |
| arrow_step | Arrow step      | Step of values for drawing wind arrows (in px)                                         | string or int              | none                           | 32                               |
| use_norm   | Use Normalizing | If arrows should be proportionate to the wind speed. **Only for WND layer**            | bool or string             | true or false                  | false                            |


## Addresses
If latitude and longitude is not your cup of tea (or it simply is not adapted to your needs), you can use Freather's Addresses system.
### Description
This service is a replacement for the latitude and longitude, that means the two modes are incompatible.

If the Addresses system is used on the first param, then the priority will go to the Addresses.

On the following services, the Addresses system is possible to be used in the first parameter:
- Current
- Forecast

*Note: If the Addresses system is used, then the parameter that originally served as the longitude (p2) will be ignored, so it's totally fine if you put null or any other accepted value inside: Freather will just ignore it :)*

This system uses the Geocoder API directly from Openweathermap, and thus, is free.

<p style="text-align: center; font-weight: bold">BEWARE</p>
<p style="text-align: center">The addresses system uses a hierarchy system, which might affect the awaited results from OWM. See bellow for more informations on the hierarchy:</p>

Here is the order of priority of the Addresses system:
1) zipCode
2) city and/or countryCode and/or stateCode
3) cityID

For exemple, if cityID was defined alongside the countryCode, then Freather will prioritise the city, countryCode and stateCode, as they are higher on the hierarchy than cityID. And thus, the first mode will be used.

Another exemple:
If the zipCode, countryCode and cityID is defined, Freather will prioritize the third mode, because zipCode is higher on the hierarchy than cityID and countryCode.

### Documentation
#### Structure
```php
new Addresses(
	city,
	countryCode,
	stateCode,
	cityID,
	zipCode,
)
```

#### Possible value types
The following params should be [ISO-3166](https://fr.wikipedia.org/wiki/ISO_3166) compatible:
- Country code
- State code

| Property    | Name         | Description                                                                                                                                                                                                                           | Used (and usable) with    | Types  | Range | Default | Required ?                         |
|-------------|--------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------|--------|-------|---------|------------------------------------|
| city        | City name    | The city name you want to get weather for.                                                                                                                                                                                            | countryCode & stateCode   | string | none  | none    | [ ] no on solo, [x] yes for mode 1 |
| countryCode | Country code | The country code.                                                                                                                                                                                                                     | city, zipCode & stateCode | string | none  | none    | [ ] no                             |
| stateCode   | State code   | The state code. Only for US.                                                                                                                                                                                                          | city & countryCode        | string | none  | none    | [ ] no                             |
| cityID      | City ID      | The city ID. A list of all city IDs can be found online [here](https://public.opendatasoft.com/explore/dataset/geonames-all-cities-with-a-population-1000/table) or can be downloaded [here](http://bulk.openweathermap.org/sample/). | none                      | string | none  | none    | [ ] no                             |
| zipCode     | Zip code     | The ZIP code (only for US). Country param is not important, as Openweathermap will assume and use the US is the chosen country.                                                                                                       | countryCode               | string | none  | none    | [ ] no                             |
| toArray     | To Array     | Will return an array of all 5 params and their values.                                                                                                                                                                                | none                      | none   | none  | none    |                                    |

#### Modes
Besides the Addresses constructor, you can choose to use Addresses 'modes' functions. Theses 'modes' combine several functions and properties that will be used to query Openweathermap.

They are 3 modes available for you:

| Function name | Name   | Description                                                                                                                  | Params                                                                       | Returns                         |
|---------------|--------|------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------|---------------------------------|
| mode1         | Mode 1 | The first mode out of the 3 available. Same as doing the following methods in one:<br>- city<br>- countryCode<br>- stateCode | $city (string\|null), $countryCode (string\|null), $stateCode (string\|null) | The current Addresses instance. |
| mode2         | Mode 2 | The second mode out of the 3 available. Exactly the same as the method 'cityID'.                                             | $cityID (string)                                                             | The current Addresses instance. |
| mode3         | Mode 3 | The second mode ou of the 3 available. Same as doing the following methods:<br>- zipCode<br>- countryCode                    | $zipCode (string\|null), $countryCode (string\|null)                         | The current Addresses instance. |
**All params in those modes are optional, as it lets you have the freedom to choose between options.**



#### Functions
If the constructor nor modes fit your needs (or simply don't want to use those), they are functions for each properties of Addresses. Here are those functions:

| Function name | Name         | Description                                                                                                         | Params            | Usable with             | Returns            |
|---------------|--------------|---------------------------------------------------------------------------------------------------------------------|-------------------|-------------------------|--------------------|
| city          | City name    | Defines a city to be used when using the Addresses system.                                                          | $city (string)    | countryCode & stateCode | Addresses instance |
| countryCode   | Country code | Defines a country code to be used when using the Addresses system.                                                  | $code (string)    | city & stateCode        | Addresses instance |
| stateCode     | State code   | Defines a state code to be used when using the Addresses system.  Beware, this option is only available for the US. | $code (string)    | city & countryCode      | Addresses instance |
| cityID        | City ID      | Defines the city ID to be used when fetching data.                                                                  | $cityID (string)  | none                    | Addresses instance |
| zipCode       | ZIP Code     | Defines the US ZIP code to be used when fetching data.                                                              | $zipCode (string) | countryCode             | Addresses instance |


### Exemples
*How to get the current weather at Paris, France ?*
```php
use Viartfelix\Freather\Freather;
//For Addresses system
use Viartfelix\Freather\weather\Addresses;

//Defining API key
$Freather = new Freather(
	"(your API key)",
	//(your other options...),
);

$Adr = new Addresses(
	"Paris", //The city name
	"fr", // Country code
);

$weatherParis = $Freather->fetchGetCurrent(
    $Adr, //The addresses
    null, //Will be ignored, or can even be not present
);
```

This code will result in the following URL being build and queried to Openweathermap:
https://api.openweathermap.org/data/2.5/weather?q=Paris,fr&appid=(API_KEY)&units=standard&lang=en&cnt=1&mode=json&isRaw=false



## Entirely configurable lib
Freather uses a configuration to run and query OWM. Here is a list of all configurable options:
### Syntax:
```php
$Freather->defineConfig([
	"configuration_name" => "value"
]);
```

| Configuration name | Description                                                                                                                                                               | Possible values                                                                    | Possible types | Default value                                      | Required ? |
|--------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------|----------------|----------------------------------------------------|------------|
| apiKey             | Your OpenWeatherMap API key                                                                                                                                               | A valid OpenWeatherMap API key                                                     | string         | none                                               | [ ] no \*  |
| lang               | The lang which will be used when OpenWeatherMap will give the city / location informations.                                                                               | A valid [ISO 631-1](https://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1) value | string         | 'en'                                               | [ ] no     |
| measurement        | The measurement to be used for temperatures                                                                                                                               | 'standard' (Kelvin), 'imperial' (Fahrenheit) or 'metric' (Celsius)                 | string         | 'standard'                                         | [ ] no     |
| timestamps         | The number of timestamps that will be present in the Previ (forecasts on several days) service.                                                                           | An integer between 1 and 2^32-1                                                    | int, string    | 1                                                  | [ ] no     |
| currentEntrypoint  | The link to the entrypoint for the service of Actu (current forecast)                                                                                                     | Any link that points to OpenWeatherMap's API                                       | string         | "https://api.openweathermap.org/data/2.5/weather"  | [ ] no     |
| mapEntrypoint      | The link to the entrypoint for the service of Map (get Link to interractive map)                                                                                          | Any link that points to OpenWeatherMap's API                                       | string         | "http://maps.openweathermap.org/maps/2.0/weather"  | [ ] no     |
| forecastEntrypoint | The link to the entrypoint for the service of Previ (all future forecats on a period of 5 days)                                                                           | Any link that points to OpenWeatherMap's API                                       | string         | "https://api.openweathermap.org/data/2.5/forecast" | [ ] no     |
| cacheDuration      | The duration (in seconds) for the data to be cached. If the value is 0 or -1, then no data will be cached and you will get the API response instead of a cached response. | An integer between 0 and 2^32-1                                                    | int            | -1                                                 | [ ] no     |

\* : No when using defineConfig, but yes then instantiating Freather.
If no key was defined the instanciation of Freather, a FreatherException will be thrown.

*Note: if you specify a parameter in the instanciation of Freather, then this param will not reset to the default, it is only overwritten if you explicitly tells it in defineConfig*

### The rollback system
A rollback system is available. When the method ``rollbackConfig()`` is called, then the configuration will rollback to the previous state, to allow you to not re-define and store the previous configuration

**BEWARE**: The rollback can only rollback of one configuration. This means Freather only stores the PREVIOUS configuration, and not all defined configuration in the instance. Example:

```php
$Freather = new Freather([
	"apiKey" => "(your API key)",
	//(other configurations)
]);

//(other shenanigans)

//GOOD:
//Define a new config to be used by Freather
$Freather->defineConfig([
	(configurations)
]);

//Rollback to the previous config: the one used in the instantiation of Freather.
$Freather->rollbackConfig();


//BAD:
//Define a new config to be used by Freather
$Freather->defineConfig([
	(configurations)
]);


//Re-define a new config to be used by Freather
$Freather->defineConfig([
	(configurations)
]);

//Rollback to the previous config: the last definedConfig up here
$Freather->rollbackConfig();

//Expected: rolling back to the config of the instantiation of Freather
//Got: ERROR: FreatherException: No previous config to rollback to.
$Freather->rollbackConfig();

```
## Cache system
To prevent wasting your API quota in half a second, a cache system is available.

This cache is based on [phpFastCache](https://www.phpfastcache.com/).
### How it works
It is very simple.
When instantiating Freather, there is a third param available: the cache duration in seconds.

If that duration is greater than 0, then when querying to OWM, the result is stored to a cache.

Then, if you re-query the exact same URL and the item is still stored, you will get what's in the cache: a cache response and an API call spared.

This cache duration is editable in the defineConfig function.
## PHP
- Developed in: 8.1.12 and 8.3.0
- PHP version recommended: 8.1.12 +

**Older versions have not yet been tested, it might come in the future. Please be comprehensive if Freather doesn't work on older PHP versions :)**
