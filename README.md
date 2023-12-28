# FreaTher

## Weather lib meats simplicity
<p style="text-align: center">Free + Weather = <b>Freather</b></p>

## Install
*Composer*
```sh
composer require 'FelixViart/freather'
```

*PHP extensions*
You need to enable the following PHP extensions to be able to use Freather:

| Name     | Reason                                                                                                                           |
|----------|----------------------------------------------------------------------------------------------------------------------------------|
| PHP Curl | Because Freather uses Symphony's HTTP client (and by extension, the PHP Curl extension), the Curl PHP extension must be enabled. | 

*API Key*
To be able to communicate and use Freather, an API key is required when creating the Freather instance. See [this link](https://home.openweathermap.org/api_keys) on how to get one.

**Beware**: when a key to openweathermap has been created, it will not be usable for several minutes / hours / days.

And that's it, you can now use Freather in all your projects, even for commercial use, I don't mind :).
## 3 distinct services:
All the services function as 'fetcher' and 'getter':

*First you explicitly have to tell Freather to Fetch the ressource, and only then you can get the ressource.*

When fetching data, Freather will store the response it and can be accessible when using the getter.

You can specify in the getter if Freather have to reset the stored responses array.


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

### Actu
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
	//(your other options...),
);

//fetching from Openweathermap at lat 0 and lon 0
$Freather->fetchCurrent(
    0,
    0,
)->getAllCurrent();
```

With this, the constructed URL will be:
https://api.openweathermap.org/data/2.5/weather?appid=(API_key)&lang=en&units=standard&cnt=1&mode=json&lat=0&lon=0

*How to get the current weather a position 0, 0 with the xml response mode, and with the raw, unfiltered response ?*
```php
use Viartfelix\Freather\Freather;

//Defining API key
$Freather = new Freather(
	"(your API key)",
	//(your other options...),
);

$Freather->fetchCurrent(
    0,
    0,
    true,
    [
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
		"mode" => //(...)
	]
)

//getAllCurrent: tels Freather to get back the stored results.
getAllCurrent()
```
###### Possible value types
| Property          | Name           | Description                                                                                             | Type      | Range           | Default       | Required ? |
|-------------------|----------------|---------------------------------------------------------------------------------------------------------|-----------|-----------------|---------------|------------|
| $Addresses        | Addresses (p1) | The Addresses instance. See Addresses documentation for more details                                    | Addresses | none            | none          | [x] yes    | 
| $p2               | P2             | With the Addresses system, no need to put anything in this param. Null is acceptable here.              | none      | none            | null          | [ ] no     |
| $raw              | IsRaw          | If the response should be the raw response from Openweathermap or not (basic json or xml parse if true) | bool      | true or false   | false         | [ ] no     |
| $options          | Options        | An array of options for the query                                                                       | array     | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode  | The response mode of OpenWeatherMap.                                                                    | string    | 'json' or 'xml' | 'json'        | [ ] no     |
### Previ
#### Description
Allows one to get the future forecasts in a specified longitude and latitude, or at a specified location (see the Addresses documentation).

They are 2 modes available for you:
1) Latitude (p1) and Longitude (p2). These two parameters are mandatory, or else a FreatherException will be thrown.
2) Addresses (p1). Only p1 is mandatory, so putting ``null`` to p2 is completely fine, Freather will just ignore it.

The response mode is optional, and will automatically default to "json".

**This service will make heavy use of the 'timestamps' (or cnt for OWM), defined and definable in the configuration (but can be not defined. See configurations documentation for more infos.**

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
$Freather->fetchPrevisions(
    0,
    0,
)->getPrevisions();
```

With this, the constructed URL will be:
https://api.openweathermap.org/data/2.5/forecast?appid=(Your_api_key)&lang=en&units=standard&cnt=15&mode=json&lat=37.714108&lon=41.413166

*Same thing, but with xml response mode, in raw, with the Japanese language and using the metric system:*
```php
use Viartfelix\Freather\Freather;

$Freather = new Freather([
	"apiKey" => "(your API key)",
	"lang" => "jp",
	"measurement" => "metric"
]);

$Freather->fetchPrevisions(
    37.714108,
    41.413166,
    [
	    "mode" => "xml"
    ]
)->getPrevisions(true);
```

#### Structure
```php
//fetchPrevisions: tels Freather to fetch the data to OpenWeatherMap for forecasts
fetchPrevisions(
	latitude,
	longitude,
	options: [
		"mode" => //The response mode: 'json' or 'xml'
	]
)

//getPrevisions: tels Freather to get back the results.
getPrevisions(
	isRaw
)
```

#### Possible value types
| Property          | Name            | Description                                                                                                                                                                                  | Type                 | Range           | Default       | Required ? |
|-------------------|-----------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------|-----------------|---------------|------------|
| $latitude         | Latitude        | The latitude at which to get the current weather. Rounded at 8 digits                                                                                                                        | string, int or float | -90 to 90       | None          | [x] yes    |
| $longitude        | Longitude       | The longitude at which to get the current weather. Rounded at 8 digits                                                                                                                       | string, int or float | -180 to 180     | None          | [x] yes    |
| $options          | Options         | An array of options for the query                                                                                                                                                            | array                | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode   | The response mode of OpenWeatherMap.                                                                                                                                                         | string               | 'json' or 'xml' | 'json'        | [ ] no     |
| $isRaw            | Response method | If what will be returned should be the raw, unfiltered response of the API or if what will be returned should be decoded, with basic decocde syntax. (json_decode and simplexml_load_string) | bool                 | true or false   | false         | [ ] no     |

### Carte
#### Description
Allows one to get the link to a map with defined filters, and other options.

You have to specify the latitude, alongside the longitude. The timestamps are optional, and are defined in the configuration.

See below for the documentation and syntax of this service.
#### Documentation
*How to get forecasts at position 37.714108, 41.413166, aka the city of Batman, in Turkey with 15 timestamps ?*
```php
use Viartfelix\Freather\Freather;

$Freather=new Freather([
  "apiKey" => "(your API key)",
  "timestamps" => 15, //The number of timestamps in the response.
]);

$Freather->fetchPrevisions(
    37.714108, //Latitude
    41.413166, //Longitude
)->getPrevisions();
```

With this, the constructed URL will be:
https://api.openweathermap.org/data/2.5/forecast?appid=(Your_api_key)&lang=en&units=standard&cnt=15&mode=json&lat=37.714108&lon=41.413166

*Same thing, but with xml response mode, in raw, with the Japanese language and using the metric system:*
```php
use Viartfelix\Freather\Freather;

$Freather = new Freather([
	"apiKey" => "(your API key)",
	"lang" => "jp", //the language that'l be used for city infos, such as name.
	"measurement" => "metric" //the measurement to use
]);

$Freather->fetchPrevisions(
    37.714108, //latitude
    41.413166, //longitude
    [
	    "mode" => "xml" //OpenWeatherMap response mode
    ]
)->getPrevisions(true);
```

#### Structure
```php
//fetchPrevisions: tels Freather to fetch the data to OpenWeatherMap for forecasts
fetchPrevisions(
	latitude,
	longitude,
	options: [
		"mode" => //The response mode: 'json' or 'xml'
	]
)

//getPrevisions: tels Freather to get back the results.
getPrevisions(
	isRaw
)
```

#### Possible value types
| Property          | Name            | Description                                                                                                                                                                                  | Type                 | Range           | Default       | Required ? |
|-------------------|-----------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------|-----------------|---------------|------------|
| $latitude         | Latitude        | The latitude at which to get the current weather. Rounded at 8 digits                                                                                                                        | string, int or float | -90 to 90       | None          | [x] yes    |
| $longitude        | Longitude       | The longitude at which to get the current weather. Rounded at 8 digits                                                                                                                       | string, int or float | -180 to 180     | None          | [x] yes    |
| $options          | Options         | An array of options for the query                                                                                                                                                            | array                | None            | (empty array) | [ ] no     |
| $options\["mode"] | Response mode   | The response mode of OpenWeatherMap.                                                                                                                                                         | string               | 'json' or 'xml' | 'json'        | [ ] no     |
| $isRaw            | Response method | If what will be returned should be the raw, unfiltered response of the API or if what will be returned should be decoded, with basic decocde syntax. (json_decode and simplexml_load_string) | bool                 | true or false   | false         | [ ] no     |

Allows one to get the map URL, with multiple layers and params.


## Addresses
If latitude and longitude is not your cup of tea (or it simply is not adapted to your needs), you can use Freather's Addresses system:

### Description
This service is a replacement for the latitude and longitude, that means the two modes are incompatible.

If the Addresses system is used on the first param, then the priority will go to the Addresses.

On the following services, the Addresses system is possible to be used in the first parameter:
- Current
- Forecast

*Note: If the Addresses system is used, then the parameter that originally served as the longitude (p2) will be ignored, so it's totally fine if you put null or any other accepted value inside: Freather will just ignore it :)*

This system is using the Geocoder API directly from Openweathermap, and thus, is free.


### Documentation
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
### Syntax:
```php
$Freather->defineConfig([
	"configuration_name" => "value"
]);
```

| Configuration name | Description                                                                                                                                                               | Possible values                                                                    | Possible types | Default value                                      | Required ? |
|--------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------|----------------|----------------------------------------------------|------------|
| apiKey             | Your OpenWeatherMap API key                                                                                                                                               | A valid OpenWeatherMap API key                                                     | string         | none                                               | [ ] no     |
| lang               | The lang which will be used when OpenWeatherMap will give the city / location informations.                                                                               | A valid [ISO 631-1](https://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1) value | string         | 'en'                                               | [ ] no     |
| measurement        | The measurement to be used for temperatures                                                                                                                               | 'standard' (Kelvin), 'imperial' (Fahrenheit) or 'metric' (Celsius)                 | string         | 'standard'                                         | [ ] no     |
| timestamps         | The number of timestamps that will be present in the Previ (forecasts on several days) service.                                                                           | An integer between 1 and 2^32-1                                                    | int, string    | 1                                                  | [ ] no     |
| actuEntrypoint     | The link to the entrypoint for the service of Actu (current forecast)                                                                                                     | Any link that points to OpenWeatherMap's API                                       | string         | "https://api.openweathermap.org/data/2.5/weather"  | [ ] no     |
| mapEntrypoint      | The link to the entrypoint for the service of Map (get Link to interractive map)                                                                                          | Any link that points to OpenWeatherMap's API                                       | string         | "http://maps.openweathermap.org/maps/2.0/weather"  | [ ] no     |
| previEntrypoint    | The link to the entrypoint for the service of Previ (all future forecats on a period of 5 days)                                                                           | Any link that points to OpenWeatherMap's API                                       | string         | "https://api.openweathermap.org/data/2.5/forecast" | [ ] no     |
| cacheDuration      | The duration (in seconds) for the data to be cached. If the value is 0 or -1, then no data will be cached and you will get the API response instead of a cached response. | An integer between 0 and 2^32-1                                                    | int            | -1                                                 | [ ] no     |

### The rollback system
A rollback system is available. When the method ``rollbackConfig()`` is called, then the configuration will rollback to the previous state, to allow you to not re-define and store the previous configuration

**BEWARE**: The rollback can only rollback of one configuration. This means Freather only stores the PREVIOUS configuration, and not all defined configuration in the instance. Example:

```php
$Freather = new Freather([
	"apiKey" => "(your API key)",
	(other configurations)
]);

(...)

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

## Based on Open Weather Map API


## PHP
- Developed in: 8.1.12
- PHP version recommended: 8.1.12

**Older versions have not yet been tested, it might come in the future. Please be comprehensive if this Freather doesn't work on older PHP versions :)**
