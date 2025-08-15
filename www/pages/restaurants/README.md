# Eurofurence Featured Hotels Map

## Requirements

  * [mbtileserver](https://github.com/consbio/mbtileserver), a simple Go-based
    server for map tiles stored in mbtiles format. Docker image at consbio/mbtileserver:latest
  * Alternatively, [https://docs.protomaps.com/](Protomaps) 
    [https://github.com/protomaps/PMTiles/tree/main/js](PMTiles JS client)
  * Mapbox tiles for the region to be displayed, or create them with 
    [Tilemaker](https://github.com/systemed/tilemaker).
  * [Maplibre GL JS](https://maplibre.org/projects/maplibre-gl-js/), a free fork of Mapbox
  * [Klokantech Noto Glyphs](https://github.com/klokantech/klokantech-gl-fonts/)

## Optional: minify the Protomaps JavaScript library

  * npm install terser
  * curl -L https://unpkg.com/pmtiles@latest/dist/index.js -o pmtiles.src.js
  * node\_modules/.bin/terser pmtiles.src.js --source-map --compress -o pmtiles.js

## Create the Mapbox tiles

  * Download OSM data for the region, e.g. from [Geofabrik](https://download.geofabrik.de/),
    in PBF (Google protobuf) format, as well as the shape file
  * Optionally, download the coastline shape files from 
    [Natural Earth](https://www.naturalearthdata.com/downloads/10m-physical-vectors/) or
    [openstreetmap.de](https://osmdata.openstreetmap.de/download/water-polygons-split-4326.zip)
  * "Crop" the area with e.g. [Osmium](https://osmcode.org/osmium-tool/) if it is too large, e.g.:

	osmium extract --bbox=9.7,53.37,11,53.76 --set-bounds --strategy=smart \
	  ~/Downloads/hamburg-latest.osm.pbf --output=hamburg-stadtgebiet.pbf
  * Adjust Tilmaker's LUA and JSON resource files to your needs
  * Create the tileset with, for example:

        tilemaker --input hamburg-stadtgebiet.osm.pbf  \
        --output hamburg.mbtiles \
        --process tilemaker/resources/process-openmaptiles.lua \
        --config tilemaker/resources/config-openmaptiles.json

For more information, check the [tutorial by Wouter van Kleunen](https://web.archive.org/web/20230813125054/https://blog.kleunen.nl/blog/tilemaker-generate-map)

## Create the Protomaps tiles

  * build go-pmtiles by running

        git clone https://github.com/protomaps/go-pmtiles.git
        cd go-pmtiles
        go build

  * create pmtiles file from mbtiles by running

        go-pmtiles convert hamburg.mbtiles hamburg.pmtiles

## Serving the map

### Directory structure

  * _tilesets/_ — the location of the mptiles files (if using tileserver)
  * _static/_ — all static resources
  * _static/sprites/_ — Maptiler symbols, see Maplibre docs about sprite
  * _static/fonts/_ — compiled glyph fonts, see Maplibre docs about glyphs
  * _static/marker/_ — SVG map markers for POIs
  * _static/hamburg.pmtiles_ — the map (if using protomaps)
  * _static/ef-map.css_ — Stylesheet
  * _static/ef-map.js_ — Javascript code. No hardcoded map parameters!
  * _static/ef-map.json_ — [Maplibre render style](https://maplibre.org/maplibre-style-spec/)

For the hotel map:

  * _static/ef-hotels.html_ — HTML code. No Javascript!
  * _static/ef-hotels.json_ — Configuration, map parameters, and POI to CSS mappings
  * _static/ef29-hotels.geojson_ — GeoJSON file of the hotel map POIs

For the restaurant map:

  * _static/ef-dining.html_ — HTML code. No Javascript!
  * _static/ef-dining.json_ — Configuration, map parameters, and POI to CSS mappings
  * _static/ef29-dining.geojson_ — GeoJSON file of the hotel map POIs

You get the idea.

### How it works

The configuration is distributed over one HTML file, one CSS file and three JSON 
files for proper abstraction. The HTML file does not contain any JavaScript, but it
contains some configuration data nevertheless:

    <link href="ef-map.css" rel="stylesheet" />

spezifies the location of the CSS stylesheet,

    <div id="map" data-config="ef-hotels.json">
    </div>

is the container for the map, with the location of the configuration file. 
This is done to be able to serve multiple maps from the same directory.

Finally,

    <div id="attribution" style="display: none">
    </div>

is the container for the copyright message and other links.

The configuration file has the following top-level sections:

    {
      "geoJSON": "ef29-hotels.geojson",
      "options": { },
      "markerCSSClasses": { }
    }

__geoJSON__ is the location of the GeoJSON file with POIs, __options__ is the
options parameter to MapLibre's Map constructor (some may be generated
dynamically by JS), __markerCSSClasses__ is the mapping between the POI
types, CSS classes and JS popup (which will popup when a marker gets
clicked) classes. For example:

    "hotel-featured": {
      "popupClass": "hotel",
      "tipClass": "hotel",
      "types": ["hotel", "hostel"]
    }

Important: __tipClass__ _MUST_ be different from the general CSS class 
("__hotel-featured__" in this example), otherwise the mechanism that hides the
tooltip will hide the marker as well.

POIs of the __types__ "_hotel_" and "_hostel_" will get a marker of the CSS
class "__hotel-featured__":

    .hotel-featured {
      background-image: url("/marker/hotelmarker.svg");
      background-size: cover;
      width: 32px;
      height: 48px;
      z-index: 2;
      --offset: 0px -22px;
   }

(The "--offset" will be interpreted by the JavaScript). The __popupClass__ 
"_hotel_" is the prefix of the CSS classes for the popup, conveniently
named _hotel-popup-*_. Likewise, the __tipClass__ "_hotel_" is the prefix
for the tooltip CSS classes. The tooltips will appear when hovering the
mouse pointer over the POI marker.

The "__hotel-osm__" class is special: the JavaScript code collects all
OSM POIs from the layer "__poi-hotel__" and creates a transparent
marker at their location. Hovering over that area results in a tooltip
showing the name associated to the POI (i.e., hotel), but there is no
popup. Also, these markers and the hotel sprite will be removed if
a "featured hotel" exists at the same location.

The following marker classes currently exist: `[VenueMarker, HotelMarker,
PartnerHotelMarker, DiningMarker, DiningVeganMarker, DiningVegetarianMarker,
DiningGlutenFreeMarker, StationMarker, BusstopMarker, GenericMarker]` with a
corresponding __popupClass__ of "_venue_", "_hotel_", "_hotel\_partner_",
"_dining_", "_dining\_vegan_", "_dining\_glutenfree_", "_station_",
"_busstop_", or "_generic_".

They'll produce slightly different output, using the GeoJSON properties
object of a POI feature, for example:

    {
      "type": "Feature",
      "properties": {
         "type": "hotel",
         "title": "Radisson Blu",
         "name": "Radisson Blu Hamburg-Dammtor",
         "alert": "fully booked",
         "stars": 4.5,
         "rates": 3,
         "rooms": 556,
         "address": "Congressplatz 2, 20355 Hamburg",
         "phone": "+49 40 35 020",
         "email": "info.hamburg@radissonblue.com",
         "url": "https://www.radissonhotels.com/en-us/hotels/radisson-blu-hamburg"
      },
      "geometry": {
        "type": "Point",
        "coordinates": [9.987396, 53.561538]
      }
    }

Feature.properties.type needs to be one of the __types__ in the __markerCSSClasses__
elements. Everything else is just popup box content. Different types accept different
properties. The "_generic_" type (JS class `GenericMarker`) is special, as it adds the 
GPS cooredinates from feature.geometry.coordinates in D° M" S'' notation to the box.
Types "_station_" and "_busstop_" will also create some public transport line
designators in the style of the HVV, _stars_ and _rates_ will graphically show the
amount of stars and expensiveness of a "_hotel_".

