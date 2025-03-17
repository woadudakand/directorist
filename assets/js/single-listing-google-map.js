/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 22);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./assets/src/js/global/map-scripts/single-listing/google-map.js":
/*!***********************************************************************!*\
  !*** ./assets/src/js/global/map-scripts/single-listing/google-map.js ***!
  \***********************************************************************/
/*! exports provided: initSingleMap */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "initSingleMap", function() { return initSingleMap; });
/* Single listing google map */
var $ = jQuery;

// Single Listing Map Initialize
function initSingleMap() {
  if (typeof google === "undefined" || !google.maps || !google.maps.Marker || !google.maps.OverlayView || !google.maps.marker.AdvancedMarkerElement) {
    return;
  }
  if ($('.directorist-single-map').length) {
    document.querySelectorAll('.directorist-single-map').forEach(function (mapElm) {
      var searchIcon = "<i class=\"directorist-icon-mask\"></i>";
      var markerShape = document.createElement("div");
      markerShape.className = "atbd_map_shape";
      markerShape.innerHTML = searchIcon;
      function Marker(options) {
        google.maps.Marker.apply(this, arguments); // Properly call parent constructor

        if (options.map_icon_label) {
          this.MarkerLabel = new MarkerLabel({
            map: this.getMap(),
            marker: this,
            text: options.map_icon_label
          });
          this.MarkerLabel.bindTo('position', this, 'position');
        }
      }

      // Ensure Marker extends google.maps.Marker
      Marker.prototype = Object.create(google.maps.Marker.prototype);
      Marker.prototype.constructor = Marker;

      // Custom Marker setMap method
      Marker.prototype.setMap = function (map) {
        google.maps.Marker.prototype.setMap.call(this, map);
        if (this.MarkerLabel) {
          this.MarkerLabel.setMap(map);
        }
      };

      // Marker Label Overlay
      function MarkerLabel(options) {
        this.setValues(options);
        this.div = document.createElement('div');
        this.div.className = 'map-icon-label';

        // Ensure marker click event works
        var self = this;
        google.maps.event.addDomListener(this.div, 'click', function (e) {
          if (e.stopPropagation) e.stopPropagation();
          google.maps.event.trigger(self.marker, 'click');
        });
      }

      // Ensure Google Maps API is loaded before extending OverlayView
      MarkerLabel.prototype = Object.create(google.maps.OverlayView.prototype);
      MarkerLabel.prototype.constructor = MarkerLabel;

      // onAdd method
      MarkerLabel.prototype.onAdd = function () {
        var pane = this.getPanes();
        if (pane) {
          pane.overlayImage.appendChild(this.div);
        }
        var self = this;
        this.listeners = [google.maps.event.addListener(this, 'position_changed', function () {
          self.draw();
        }), google.maps.event.addListener(this, 'text_changed', function () {
          self.draw();
        }), google.maps.event.addListener(this, 'zindex_changed', function () {
          self.draw();
        })];
      };

      // onRemove method
      MarkerLabel.prototype.onRemove = function () {
        if (this.div.parentNode) {
          this.div.parentNode.removeChild(this.div);
        }
        for (var i = 0; i < this.listeners.length; i++) {
          google.maps.event.removeListener(this.listeners[i]);
        }
      };

      // draw method
      MarkerLabel.prototype.draw = function () {
        var projection = this.getProjection();
        if (!projection) return; // Ensure projection is available

        var position = projection.fromLatLngToDivPixel(this.get('position'));
        if (!position) return;
        var div = this.div;
        div.innerHTML = this.get('text') || "";
        div.style.zIndex = this.get('zIndex') || "0";
        div.style.position = 'absolute';
        div.style.display = 'block';
        div.style.left = position.x - div.offsetWidth / 2 + 'px';
        div.style.top = position.y - div.offsetHeight + 'px';
      };

      // initialize all vars here to avoid hoisting related misunderstanding.
      var map, info_window, saved_lat_lng;

      // Localized Data
      var mapData = JSON.parse(mapElm.getAttribute('data-map'));
      var loc_default_latitude = parseFloat(mapData.default_latitude);
      var loc_default_longitude = parseFloat(mapData.default_longitude);
      var loc_manual_lat = parseFloat(mapData.manual_lat);
      var loc_manual_lng = parseFloat(mapData.manual_lng);
      var loc_map_zoom_level = parseInt(mapData.map_zoom_level);
      var display_map_info = mapData.display_map_info;
      var info_content = mapData.info_content;
      loc_manual_lat = isNaN(loc_manual_lat) ? loc_default_latitude : loc_manual_lat;
      loc_manual_lng = isNaN(loc_manual_lng) ? loc_default_longitude : loc_manual_lng;
      saved_lat_lng = {
        lat: loc_manual_lat,
        lng: loc_manual_lng
      };

      // create an info window for map
      if (display_map_info) {
        info_window = new google.maps.InfoWindow({
          content: info_content,
          maxWidth: 400 /*Add configuration for max width*/
        });
      }

      var marker = new google.maps.marker.AdvancedMarkerElement({
        map: map,
        position: saved_lat_lng,
        content: markerShape
      });

      // create an info window for map
      marker.addListener('click', function () {
        if (display_map_info) {
          display_map_info = false;
        } else {
          info_window.close();
          display_map_info = true;
        }
      });
      function initMap() {
        /* Create new map instance*/
        map = new google.maps.Map(mapElm, {
          zoom: loc_map_zoom_level,
          center: saved_lat_lng,
          mapId: "single_listing_map"
        });
        var marker = new google.maps.marker.AdvancedMarkerElement({
          map: map,
          position: saved_lat_lng,
          content: markerShape
        });
        if (display_map_info) {
          marker.addListener('click', function () {
            if (info_window.getMap()) {
              info_window.close(); // If already open, close it
            } else {
              info_window.open(map, marker); // Otherwise, open it
            }
          });
        }
      }

      initMap();
      //Convert address tags to google map links -
      $('address').each(function () {
        var link = "<a href='http://maps.google.com/maps?q=" + encodeURIComponent($(this).text()) + "' target='_blank'>" + $(this).text() + "</a>";
        $(this).html(link);
      });
    });
  }
}
$(document).ready(function () {
  initSingleMap();
});

// Single Listing Map on Elementor EditMode
$(window).on('elementor/frontend/init', function () {
  setTimeout(function () {
    if ($('body').hasClass('elementor-editor-active')) {
      initSingleMap();
    }
  }, 3000);
});
$('body').on('click', function (e) {
  if ($('body').hasClass('elementor-editor-active') && e.target.nodeName !== 'A' && e.target.nodeName !== 'BUTTON') {
    initSingleMap();
  }
});

/***/ }),

/***/ 22:
/*!*****************************************************************************!*\
  !*** multi ./assets/src/js/global/map-scripts/single-listing/google-map.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./assets/src/js/global/map-scripts/single-listing/google-map.js */"./assets/src/js/global/map-scripts/single-listing/google-map.js");


/***/ })

/******/ });
//# sourceMappingURL=single-listing-google-map.js.map