/* 
 * ef-map.js — javascript to present a MapLibre GL slippy map with POIs
 * Copyright (c) 2023, Eurofurence e.V.
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
 * REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
 * INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
 * LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
 * OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
 * PERFORMANCE OF THIS SOFTWARE.
 *
 */

/** @global
 * the map */
var map;

/** @global
 * marker and popup CSS class names */
var MPcssCN;

/** @global
 * List of the coordinates of the featured hotels */
var HotelCoords;

/** @global
 * data of hotel POIs according to OSM */
var osmPOIs;

/** @global
 * Map of POI handling objects */
var poiHandlers;

/** @global
 * Show OSM hotel marker coordinates in popups */
var showHotelMarkerCoords=false;

/** 
 * Convert Coordinate from float to DMS string
 */
class Coordinate {
  /**
   * Constructor
   *
   * @param {float} deg - value to convert
   */
  constructor(deg) {
    this.val = deg;
  }

  /**
   * Calculate DMS tupel from coordinate
   *
   * @eturn {Array} array with the value split into deg, min, sec
   */
  dms() {
    let d = Math.trunc(this.val);
    let r = Math.abs(this.val - d)*60.0;

    let m = Math.trunc(r);
    let s = (r - m)*60.0;
    return [d, m, s];
  }

  /**
   *  Convert DMS tupel to string
   *
   *  @param {Integer} decimals - number of fractional digits of seconds
   *  @return {str} string representation
   */
  toString(decimals=0) {
    let dms = this.dms();
    let s = new Intl.NumberFormat('en-EN', {maximumFractionDigits: decimals});
    return `${dms[0]}° ${dms[1]}' ${s.format(dms[2])}"`;
  }
}

/**
 *  Convert longitude / latitude pair to DMS representation
 */
class Coordinates {
  /**
   * Constructor
   *
   * Either:
   * @param {float} arg1 - longitude
   * @param {float} arg2 - latitude
   * Or:
   * @param {Array[float, float]} arg1 - tupel of longitude, latitude
   */
  constructor(arg1, arg2=undefined) {
    if (arg1 instanceof Array) {
      this.lon = new Coordinate(arg1[0]);
      this.lat = new Coordinate(arg1[1]);
    } else {
      this.lon = new Coordinate(arg1);
      this.lat = new Coordinate(arg2);
    }
  }

  /**
   * Create <lat> N <lon> E style string
   *
   * @param {float} decimals - number of fractional digits of seconds
   */
  toString(decimals=0) {
    return `${this.lat.toString(decimals)} N ${this.lon.toString(decimals)} E`;
  }
}

/**
 * POI marker common code and static functions
 */
class MarkerWithPopup {
  marker = null;
  popup = null;
  markerIcon = null;
  popupBoxOffset = null;
  popupText = "";
 
  /**
   * Constructor
   *
   * @param {Object} feature - GeoJSON feature
   */
  constructor(feature) {
    this.feature = feature;
    this.props = feature.properties;
    this.symbol = null;

    if (feature.properties.hasOwnProperty("type"))
      this.symbol = MPcssCN.poiName(feature.properties.type);

    if (this.symbol != null) {
       this.hoverClass = MPcssCN.tipClass(this.symbol);
       this.popupClass = MPcssCN.popupClass(this.symbol);
    } else {
      this.symbol = "generic";
      this.hoverClass = "";
    }
  }

  /**
   * Read a GeoJSON file with POIs and make the appropriate markers and popups
   */
  static readPOI(poiURL) {
    // If I cannot do it with layers, I'm doing it manually.
    fetch(poiURL).then(response => response.json()).then(pois => {
      pois.features.forEach((feature, i) => {
        if (feature.properties.hasOwnProperty('type')) {
          const poi = MPcssCN.poiName(feature.properties.type);
          let popupClass = null;

          if (poi != null) {
            popupClass = MPcssCN.popupClass(poi);
            if (!poiHandlers.hasOwnProperty(popupClass))
                  popupClass = null;
          }
          if (popupClass != null) {
            new poiHandlers[popupClass](feature).createMarker();
          } else {
            new MarkerWithPopup(feature).createMarker();
          }
        }
      })
    });
  }

  /**
   * Register a handler (class) for a certain POI type
   */
  static registerHandler() {
    poiHandlers[this.cssClass] = this;
  }

  /**
   * Create a marker, hover text and popup
   */
  createMarker() {
    this.symbol != "default"? this.createPoiMarker() : this.createDefaultMarker();
    this.popup = new maplibregl.Popup({
        closeButton: false,
        focusAfterOpen: false,
        offset: this.popupBoxOffset,
        className: `${this.popupClass}-popup`
    }).setHTML(this.html());

    const me = this;
    this.popup.on("open", function(ev, myself=me) {
        if (myself.hoverClass != "") 
          myself.markerIcon.classList.remove(myself.hoverClass);
    }).on("close", function(ev, myself=me) {
        if (myself.hoverClass != "")
          myself.markerIcon.classList.add(myself.hoverClass);
    });

    this.marker.setLngLat(this.feature.geometry.coordinates).setPopup(this.popup).addTo(map);
  }

  /**
   * Create a POI marker and hover text, attributes depending on the type
   */
  createPoiMarker() {
    const mc = MPcssCN.classes[this.symbol];
    const w = mc.width;
    const h = mc.height;
    const dx = mc.offset[0];
    const dy = mc.offset[1];

    // TODO: we currently only have icons with dx = 0, cannot test horiz. asymetrical icons
    if (dy != 0) {
      // pointer shifted vertically: "pin" style, POI is below icon
      this.popupBoxOffset = {
        'bottom': [0, -h],
        'bottom-right': [-w / 2.8, -h / 1.1],
        'bottom-left': [w / 2.8, -h / 1.1],
        'top': [0, 0],
        'right': [-w / 2, -h / 2],
        'left': [w / 2, -h / 2]
      }
    } else {
      // no vertical shift: "circle" style, POI is centered
      const hs = h / 1.4;
      const ws = w / 1.4;
      this.popupBoxOffset = {
        'bottom': [0, -h / 2],
        'bottom-right': [-ws / 2, -hs / 2],
        'bottom-left': [ws / 2, -hs / 2],
        'top': [0, h / 2],
        'top-right': [-ws / 2, hs / 2],
        'top-left': [ws / 2, hs / 2],
        'right': [-w / 2, 0],
        'left': [w / 2, 0]
      }
    }

    this.markerIcon = document.createElement("div");
    this.markerIcon.className = `${this.symbol} ${this.hoverClass}`;
    this.markerIcon.setAttribute("data-name", this.props["title"]);

    this.marker = new maplibregl.Marker({
      offset: mc.offset,
      element: this.markerIcon
    });
  }

  /**
   * Create a generic POI marker
   */
  createDefaultMarker() {
    // console.log(feature);
    if (this.feature.properties.hasOwnProperty("description")) {
      this.popupText = this.feature.properties.description;
    }
    this.marker = new maplibregl.Marker();
  }

  /**
   * Return a function that creates a text from a template string
   * see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals#tagged_templates
   * @example 
   * // returns "Address: Congressplatz 1, 20355 Hamburg"
   * const fn = this.fmt`Address: ${"address"}`;
   * fn({address: "Congressplatz 1, 20355 Hamburg"};
   *
   * @param {String[]} strings - the fixed strings of the template
   * @param {Object[]} ...keys - the keys of the variables for the templat
   * @return {function} - the functio to make the actual string
   */
  fmt(strings, ...keys) {
    return (...args) => {
      const dict = args.length > 0? args[0] : {};
      const res = [strings[0]];
      keys.forEach((k, idx) => {
         res.push(dict[k], strings[idx+1]); 
      });
      return res.join("");
    };
  }

  /**
   * Add a line to a popup box
   * @example
   * this.line(popup, "address", "venue-popup-text", this.fmt`Address: {"value"});
   *
   * @param {Element} parent - the parent element to append the new one to
   * @param {String} attribute - the GeoJSON property to apply
   * @param {String} cssClass - the CSS classe(s) for the new line
   * @param {function} fmtPtr - the pointer returned by fmt()
   * @param {boolean} hr - prepend a horizontal line if true and attribute != "link"
   * @return {boolean} created an element
   */
  line(parent, attribute, cssClass, fmtPtr, hr=false) {
    if (attribute === "link") {
      const link = this.makeLink(this.props);
      if (link == null) return false;
      const element = document.createElement("div");
      element.className = cssClass;
      element.innerHTML = fmtPtr({value: link});
      parent.append(element);
      return true;
    }

    if (attribute === "email") {
      const mailto = this.makeMailTo(this.props);
      if (mailto == null) return false;
      const element = document.createElement("div");
      element.className = cssClass;
      element.innerHTML = fmtPtr({value: mailto});
      parent.append(element);
      return true;
    }
        
    if (this.props.hasOwnProperty(attribute)) {
      if (hr) parent.append(document.createElement("hr"));

      const element = document.createElement("div");
      element.className = cssClass;
      element.innerHTML = fmtPtr({value: this.props[attribute]});
      parent.append(element);
      return true;
    }
          
    return false;
  }

  /**
   * Makes an HTML link based on GeoJSON feature.properties.url and .domain
   * 
   * @return {String} a string represention of the hyplink html code
   */
  makeLink() {
    const haveWS = this.props.hasOwnProperty("url");
    const haveBWS = this.props.hasOwnProperty("domain");
  
    if (haveWS || haveBWS) {
      let url, baseurl;

      if (!haveWS && haveBWS) {
        url = `https://${this.props.domain}/`;
        baseurl = this.props.domain;
      } else if (haveWS && !haveBWS) {
        url = this.props.url;
        const b = /[^/]*\/\/([^/]*)/.exec(url);
        if (b.length >= 2)
          baseurl = b[1];
        else if (b.length == 1)
          baseurl = b[0]
        else
          baseurl = url;
      } else {
        url = this.props.url;
        baseurl = this.props.domain;
      }
      return `<a href="${url}" target="_blank">${baseurl}</a>`;
    }
  
    return null;
  }

  /**
   * Makes an HTML mailto link based on GeoJSON feature.properties.email
   * 
   * @return {String} a string represention of the hyplink html code
   */
  makeMailTo() {
    if (this.props.hasOwnProperty("email"))
      return `<a href="mailto:${this.props.email}">${this.props.email}</a>`;
    return null;
  }

  /**
   * Creates the text of the popup for a generic marker
   *
   * @return {String} The HTML string representation of the text
   */
  html() {
    return `<div>${this.popupText}</div>`;
  }
}

/**
 * A marker class for a venue POI
 */
class VenueMarker extends MarkerWithPopup {
  static cssClass = "venue";

  /**
   * Creates the text of the popup connected to a "venue" marker
   *
   * @return {String} The HTML string representation of the generated DOM
   */
  html() {
    let hr;
    const popup = document.createElement("div");

    // name of the venue
    if (!this.line(popup, "name", "venue-popup-heading", this.fmt`${"value"}`))
      if (!this.line(popup, "title", "venue-popup-heading", this.fmt`${"value"}`));
 
    // notes / alerts
    this.line(popup, "alert", "venue-popup-alert", this.fmt`\u26A0\uFE0F ${"value"}`);
    // address
    hr = this.line(popup, "address", "venue-popup-text venue-popup-address", this.fmt`Address:<br>${"value"}`);
    // website
    hr = this.line(popup, "link", "venue-popup-text venue-popup-url", this.fmt`Website: ${"value"}`)? true : hr;
    // description
    this.line(popup, "description", "venue-popup-text venue-popup-description", this.fmt`${"value"}`, hr);

    return popup.innerHTML;
  }
}

/**
 * A marker class for a featured hotel POI
 */
class HotelMarker extends MarkerWithPopup {
  static cssClass = "hotel";
  static hotelStar = "★";
  static Currency = "€";

  constructor(feature) {
     super(feature);
     this.is_partner = false;
     HotelCoords.push(new maplibregl.LngLat(feature.geometry.coordinates[0], feature.geometry.coordinates[1]));
  }

  /**
   * Creates the text of the popup connected to a "hotel" marker
   *
   * @return {String} The HTML string representation of the generated DOM
   */
  html() {
    let hr, element;
    const popup = document.createElement("div");
    popup.className = "hotel-popup";

    // name of the hotel
    if (!this.line(popup, "name", "hotel-popup-heading", this.fmt`${"value"}`))
      this.line(popup, "title", "hotel-popup-heading", this.fmt`${"value"}`);

    // is it a partner hotel?
    if (this.is_partner) {
       element = document.createElement("div");
       element.className = "hotel-popup-partner";
       element.innerHTML = "<span class='logo-partner'></span> <span class='text-partner'>Eurofurence Partner Hotel</span>";
       popup.append(element);
    }

    // notes / alerts
    this.line(popup, "alert", "hotel-popup-alert", this.fmt`\u26A0\uFE0F ${"value"}`);

    // HOTREC Hotelstars Union rating
    if (this.props.hasOwnProperty("stars")) {
      element = document.createElement("div");
      element.className = "hotel-popup-stars";
      const stars = Math.floor(this.props.stars);
      const superior = stars < this.props.stars ? `<span class='hotrec_s'>S</span>` : "";
      element.innerHTML = `Classification: ${HotelMarker.hotelStar.repeat(stars)}${superior}`;
      popup.append(element);
    }
  
    // price category. <50 EUR: €, <130 EUR: €€, <230 EUR: €€€, >230 EUR: €€€€ (regular rates)
    if (this.props.hasOwnProperty("rates")) {
      element = document.createElement("div");
      element.className = "hotel-popup-rates";
      element.innerHTML = `Price: ${HotelMarker.Currency.repeat(this.props.rates)}`;
      popup.append(element);
    }
  
    // amount of rooms
    this.line(popup, "rooms", "hotel-popup-rooms", this.fmt`Rooms: ${"value"}`);
    // street address
    hr = this.line(popup, "address", "hotel-popup-text hotel-popup-address", this.fmt`Address:<br>${"value"}`);
    // phone number
    hr = this.line(popup, "phone", "hotel-popup-text hotel-popup-phone", this.fmt`Phone: ${"value"}`)? true : hr;
    // email address
    hr = this.line(popup, "email", "hotel-popup-text hotel-popup-email", this.fmt`Email: ${"value"}`)? true : hr;
    // website
    hr = this.line(popup, "link", "hotel-popup-text hotel-popup-url", this.fmt`Website: ${"value"}`)? true : hr;
    // description
    this.line(popup, "description", "venue-popup-text venue-popup-description", this.fmt`${"value"}`, hr);

    return popup.innerHTML;
  }
}

class PartnerHotelMarker extends HotelMarker {
  static cssClass = "hotel-partner";
  constructor(feature) {
     super(feature);
     this.is_partner = true;
  }
}

/**
  * A helper class to translate the attribute (food type) array to an HTML string of unicode glyphs
  */
class FoodType {
  static foodSymbols = {
    "vegetarian": "🧀",
    "vegan": "🌱",
    "glutenfree": "🌾",
  }

  static toHTML(listOfFoodTypes) {
    if (!Array.isArray(listOfFoodTypes))
       return "";

    let s = "";
    listOfFoodTypes.forEach((ft) => {
      if (FoodType.foodSymbols.hasOwnProperty(ft))
        s += `<span title="${ft}">${FoodType.foodSymbols[ft]}</span>`;
    });

    return s;
  }

  static toString(listOfFoodTypes) {
    if (!Array.isArray(listOfFoodTypes))
       return "";

    let s = "";
    listOfFoodTypes.forEach((ft) => {
      if (FoodType.foodSymbols.hasOwnProperty(ft))
        s += `${FoodType.foodSymbols[ft]}`;
    });

    return s;
  }
}

/**
 * A marker class for a dining place POI
 */
class DiningMarker extends MarkerWithPopup {
  static cssClass = "dining";

  createPoiMarker() {
    super.createPoiMarker();
    let ft = FoodType.toString(this.props["attributes"]);

    if (ft.length > 0) {
      this.markerIcon.setAttribute("data-name", this.props["title"]+" "+ft);
    }
  }

  /**
   * Creates the text of the popup connected to a "dining" marker
   *
   * @return {String} The HTML string representation of the generated DOM
   */
  html() {
    let hr, element;
    const popup = document.createElement("div");
    popup.className = "dining-popup";

    // name of the dining
    if (!this.line(popup, "name", "dining-popup-heading", this.fmt`${"value"}`))
      this.line(popup, "title", "dining-popup-heading", this.fmt`${"value"}`);

    let st = "";

    // style of cuisine, types of food, price category
    if (this.props.hasOwnProperty("style")) {
      st = `${this.props.style}`;
    }

    if (this.props.hasOwnProperty("attributes") && Array.isArray(this.props.attributes)) {
      let ft = FoodType.toHTML(this.props.attributes);
      if (ft.length > 0) {
        if (st.length > 0) {
           st += " · ";
        }
        st += ft;
      }
    }

    if (this.props.hasOwnProperty("price")) {
      if (st.length > 0) {
        st += " · ";
      }
      // referencing HotelMarker here to keep the currency glyph in one place
      st += HotelMarker.Currency.repeat(this.props.price);
    }

    element = document.createElement("div");
    element.className = "dining-popup-price";
    element.innerHTML = st;
    popup.append(element);

    element = document.createElement("br");
    popup.append(element);
 
    // distance
    hr =  this.line(popup, "distance", "dining-popup-text dining-popup-distance", this.fmt`Distance: ${"value"} m`);
    // street address
    hr = this.line(popup, "address", "dining-popup-text dining-popup-address", this.fmt`Address:<br>${"value"}`);
    // phone number
    hr = this.line(popup, "phone", "dining-popup-text dining-popup-phone", this.fmt`Phone: ${"value"}`)? true : hr;
    // email address (rather not)
    // hr = this.line(popup, "email", "dining-popup-text dining-popup-email", this.fmt`Email: ${"value"}`)? true : hr;
    // website
    hr = this.line(popup, "link", "dining-popup-text dining-popup-url", this.fmt`Website: ${"value"}`)? true : hr;
    // description
    this.line(popup, "description", "venue-popup-text venue-popup-description", this.fmt`${"value"}`, hr);

    return popup.innerHTML;
  }
}

class DiningVeganMarker extends DiningMarker {
  static cssClass = "dining-vegan";
}

class DiningVegetarianMarker extends DiningMarker {
  static cssClass = "dining-vegetarian";
}

class DiningGlutenFreeMarker extends DiningMarker {
  static cssClass = "dining-glutenfree";
}

/**
 * A marker class for a railway or subway station POI
 */
class StationMarker extends MarkerWithPopup {
  static cssClass = "station";

  /**
   * Creates the text of the popup connected to a "rail" or "subway" marker
   *
   * @return {String} The HTML string representation of the generated DOM
   */
  html() {
    let element;
    const popup = document.createElement("div");
  
    // name of the station
     if (!this.line(popup, "name", "station-popup-heading", this.fmt`${"value"}`))
      if (!this.line(popup, "title", "station-popup-heading", this.fmt`${"value"}`));

    // notes / alerts
    this.line(popup, "alert", "station-popup-alert", this.fmt`\u26A0\uFE0F ${"value"}`);

    // lines serviced
    if (this.props.hasOwnProperty("lines")) {
      const div = document.createElement("div");
      div.className = "station-popup-lines";
      popup.append(div);
  
      this.props.lines.forEach((line) => {
        element = document.createElement("span");
        element.className = `station-popup-line-${line}`
        element.innerHTML = `${line}`;
        div.append(element);
      });
    }
 
    // link to provider
    this.line(popup, "link", "station-popup-url", this.fmt`Website: ${"value"}`);
    // remarks
    this.line(popup, "description", "station-popup-text station-popup-description", this.fmt`${"value"}`);

    return popup.innerHTML;
  }
}

/**
 * A marker class for a busstop POI
 */
class BusstopMarker extends MarkerWithPopup {
  static cssClass = "busstop";

  /**
   * Creates the text of the popup connected to a "busstop" marker
   *
   * @return {String} The HTML string representation of the generated DOM
   */
  html() {
    let element;
    const popup = document.createElement("div");

    // name of the stop
    if (!this.line(popup, "name", "busstop-popup-heading", this.fmt`${"value"}`))
      if (!this.line(popup, "title", "busstop-popup-heading", this.fmt`${"value"}`));

    // notes / alerts
    this.line(popup, "alert", "busstop-popup-alert", this.fmt`\u26A0\uFE0F ${"value"}`);

    // lines serviced
    if (this.props.hasOwnProperty("lines")) {
      const div = document.createElement("div");
      div.className = "busstop-popup-lines";
      popup.append(div);
  
      let count = 1;
      this.props.lines.forEach((line) => {
        element = document.createElement("span");
        if ((line.length == 3) && (line[0] == "6")) {
          element.className = "busstop-popup-nightbus";
        } else {
          element.className = "busstop-popup-bus";
        }
        element.innerHTML = `${line}`;
        div.append(element);
  
        if ((this.props.lines.length > 4) && (count % 3 == 0)) {
          div.append(document.createElement("br"));
        }
        count += 1;
      });
    }
  
    // link to HVV
    this.line(popup, "link", "busstop-popup-url", this.fmt`Website: ${"value"}`);
  
    return popup.innerHTML;
  }
}

/**
 * A marker class for a generic POI
 */
class GenericMarker extends MarkerWithPopup {
  static cssClass = "generic";

  /**
   * Creates the text of the popup connected to a "generic" marker
   *
   * @return {String} The HTML string representation of the generated DOM
   */
  html() {
    let hr = true;
    const popup = document.createElement("div");

    // name of the POI
    if (!this.line(popup, "name", "generic-popup-heading", this.fmt`${"value"}`))
      if (!this.line(popup, "title", "generic-popup-heading", this.fmt`${"value"}`));
 
    // notes / alerts
    this.line(popup, "alert", "generic-popup-alert", this.fmt`\u26A0\uFE0F ${"value"}`);
    // address
    this.line(popup, "address", "generic-popup-text generic-popup-address", this.fmt`Address:<br>${"value"}`);

    // GPS
    const coords = new Coordinates(this.feature.geometry.coordinates);
    const div = document.createElement("div");
    div.className = "generic-popup-text generic-popup-coords";
    div.innerHTML = `Location: ${coords.toString()}`
    popup.append(div);
         
    // website
    this.line(popup, "link", "generic-popup-text generic-popup-url", this.fmt`Website: ${"value"}`);
    // description
    this.line(popup, "description", "generic-popup-text generic-popup-description", this.fmt`${"value"}`, hr);

    return popup.innerHTML;
  }
}


/**
 * Mappings between GeoJSON type properties and CSS classes / class prefixes
 */
class MarkerClasses {
  constructor(classes) {
    this.classes = classes;

    // iterate of the list of CSS style sheets
    for (let si = 0; si < document.styleSheets.length; si++) {
      const rules = document.styleSheets[si].cssRules;
      // iterate of the list of rules of a style sheet
      for (let ri = 0; ri < rules.length; ri++) {
        const rule = rules[ri];
        // needs to be an actual style rule
        if (!(rule instanceof CSSStyleRule)) continue;
        // a class, even 
        if (rule.selectorText[0] != ".") continue;
        const mc = rule.selectorText.slice(1);
        // if it is a class of our markers, parse the geometry and the offset as we're
        // going to need it later to calculate the positions of the popup
        if (mc in this.classes) {
          this.classes[mc].width = parseInt(rule.style.getPropertyValue("width"));
          this.classes[mc].height = parseInt(rule.style.getPropertyValue("height"));
          const offset = rule.style.getPropertyValue("--offset")
          if (offset != "") {
            const so = offset.trim().split(/\s+/);
            this.classes[mc].offset = [parseInt(so[0]), parseInt(so[1])];
          } else {
            this.classes[mc].offset = [0, 0];
          }
        }
      }
    }
          
    this.type2poi = new Object();
    Object.keys(this.classes).forEach((mc) => {
      this.classes[mc].types.forEach((pt) => {
        this.type2poi[pt] = mc;
      });
    });
  }

  poiName(type) {
    return this.type2poi.hasOwnProperty(type)? this.type2poi[type] : null;
  }

  tipClass(name) {
     return this.classes[name].hasOwnProperty("tipClass")? this.classes[name]["tipClass"] : "";
  }

  popupClass(name) {
      return this.classes[name].hasOwnProperty("popupClass")?  this.classes[name]["popupClass"] : name;
  }
}

/**
 * Create the Map, load the GeoJSON with the POIs, create the markers and the popups
 * @param event "onload" event 
 */
window.onload = (event) => {
  // no decorators in 2023 :-(
  poiHandlers = new Object();
  const markerClasses = [VenueMarker, PartnerHotelMarker, HotelMarker, DiningMarker, DiningVeganMarker, DiningVegetarianMarker, DiningGlutenFreeMarker, StationMarker, BusstopMarker, GenericMarker];
  markerClasses.forEach(mc => mc.registerHandler());

  HotelCoords = new Array();
  osmPOIs = new Map();

  if (typeof(pmtiles) != "undefined") {
    let pmtproto = new pmtiles.Protocol();
    maplibregl.addProtocol("pmtiles", pmtproto.tile);
  }

  const configURL = document.getElementById("map").getAttribute("data-config");
  fetch(configURL).then(response => response.json()).then(config => {
    MPcssCN = new MarkerClasses(config.markerCSSClasses);

    config.options["container"] = "map";
    config.options["customAttribution"] = document.getElementById("attribution").innerHTML;
    map = new maplibregl.Map(options=config.options);
    map.on("load", () => { MarkerWithPopup.readPOI(config.geoJSON) });

    map.on('render', function() {
      const pois = map.queryRenderedFeatures({
            layers: config.osmTipLayers
         });

      pois.forEach((poi) => {
        if (poi.hasOwnProperty("_vectorTileFeature")) {
          if (poi._vectorTileFeature.hasOwnProperty("_geometry") && !osmPOIs.has(poi._vectorTileFeature._geometry)) {
            markerIcon = document.createElement("div");
            markerIcon.className = "hotel-osm hotel";
	    markerIcon.setAttribute("data-name", `${poi.properties["name:latin"]}`);
	    if (showHotelMarkerCoords) {
                      lon = Math.round((poi.geometry.coordinates[0]+Number.EPSILON)*1e6)/1e6;
                      lat = Math.round((poi.geometry.coordinates[1]+Number.EPSILON)*1e6)/1e6;
                      markerIcon.setAttribute("data-name", `${poi.properties["name:latin"]}\n[${lon}, ${lat}]`);
            }

            marker = new maplibregl.Marker({
                element: markerIcon
              })
              .setLngLat(poi.geometry.coordinates)
              .addTo(map);
  
            osmPOIs.set(poi._vectorTileFeature._geometry, {
              coordinates: marker.getLngLat(),
              marker: marker,
              icon: markerIcon,
              enabled: true
            });
          }
        }
      });
  
      HotelCoords.forEach((poi) => {
        osmPOIs.forEach((entry) => {
          if (entry.enabled && poi.distanceTo(entry.coordinates) < 2) {
            entry.marker.remove();
            entry.enabled = false;
          }
        });
      });
    });
  
    map.on("zoomend", function() {
      osmPOIs.forEach((poi) => {
        if (poi.enabled) {
          poi.icon.style.visibility = (map.getZoom() < 14) ? "hidden" : "visible";
        }
      });
    });
  });
}
