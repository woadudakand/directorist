/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/js/public/components/directoristAlert.js":
/*!*************************************************************!*\
  !*** ./assets/src/js/public/components/directoristAlert.js ***!
  \*************************************************************/
/***/ (function() {

;
(function ($) {
  // Make sure the codes in this file runs only once, even if enqueued twice
  if (typeof window.directorist_alert_executed === 'undefined') {
    window.directorist_alert_executed = true;
  } else {
    return;
  }
  window.addEventListener('load', function () {
    /* Directorist alert dismiss */
    var getUrl = window.location.href;
    var newUrl = getUrl.replace('notice=1', '');
    if ($('.directorist-alert__close') !== null) {
      $('.directorist-alert__close').each(function (i, e) {
        $(e).on('click', function (e) {
          e.preventDefault();
          history.pushState({}, null, newUrl);
          $(this).closest('.directorist-alert').remove();
        });
      });
    }
  });
})(jQuery);

/***/ }),

/***/ "./assets/src/js/public/components/directoristDropdown.js":
/*!****************************************************************!*\
  !*** ./assets/src/js/public/components/directoristDropdown.js ***!
  \****************************************************************/
/***/ (function() {

;
(function ($) {
  // Make sure the codes in this file runs only once, even if enqueued twice
  if (typeof window.directorist_dropdown_executed === 'undefined') {
    window.directorist_dropdown_executed = true;
  } else {
    return;
  }
  window.addEventListener('load', function () {
    /* custom dropdown */
    var atbdDropdown = document.querySelectorAll('.directorist-dropdown-select');

    // toggle dropdown
    var clickCount = 0;
    if (atbdDropdown !== null) {
      atbdDropdown.forEach(function (el) {
        el.querySelector('.directorist-dropdown-select-toggle').addEventListener('click', function (e) {
          e.preventDefault();
          clickCount++;
          if (clickCount % 2 === 1) {
            document.querySelectorAll('.directorist-dropdown-select-items').forEach(function (elem) {
              elem.classList.remove('directorist-dropdown-select-show');
            });
            el.querySelector('.directorist-dropdown-select-items').classList.add('directorist-dropdown-select-show');
          } else {
            document.querySelectorAll('.directorist-dropdown-select-items').forEach(function (elem) {
              elem.classList.remove('directorist-dropdown-select-show');
            });
          }
        });
      });
    }

    // remvoe toggle when click outside
    document.body.addEventListener('click', function (e) {
      if (e.target.getAttribute('data-drop-toggle') !== 'directorist-dropdown-select-toggle') {
        clickCount = 0;
        document.querySelectorAll('.directorist-dropdown-select-items').forEach(function (el) {
          el.classList.remove('directorist-dropdown-select-show');
        });
      }
    });

    //custom select
    var atbdSelect = document.querySelectorAll('.atbd-drop-select');
    if (atbdSelect !== null) {
      atbdSelect.forEach(function (el) {
        el.querySelectorAll('.directorist-dropdown-select-items').forEach(function (item) {
          item.addEventListener('click', function (e) {
            e.preventDefault();
            el.querySelector('.directorist-dropdown-select-toggle').textContent = e.target.textContent;
            el.querySelectorAll('.directorist-dropdown-select-items').forEach(function (elm) {
              elm.classList.remove('atbd-active');
            });
            item.classList.add('atbd-active');
          });
        });
      });
    }

    // Dropdown
    $('body').on('click', '.directorist-dropdown .directorist-dropdown-toggle', function (e) {
      e.preventDefault();
      $(this).siblings('.directorist-dropdown-option').toggle();
    });

    // Select Option after click
    $('body').on('click', '.directorist-dropdown .directorist-dropdown-option ul li a', function (e) {
      e.preventDefault();
      var optionText = $(this).html();
      $(this).children('.directorist-dropdown-toggle__text').html(optionText);
      $(this).closest('.directorist-dropdown-option').siblings('.directorist-dropdown-toggle').children('.directorist-dropdown-toggle__text').html(optionText);
      $('.directorist-dropdown-option').hide();
    });

    // Hide Clicked Anywhere
    $(document).bind('click', function (e) {
      var clickedDOM = $(e.target);
      if (!clickedDOM.parents().hasClass('directorist-dropdown')) $('.directorist-dropdown-option').hide();
    });

    //atbd_dropdown
    $(document).on("click", '.atbd_dropdown', function (e) {
      if ($(this).attr("class") === "atbd_dropdown") {
        e.preventDefault();
        $(this).siblings(".atbd_dropdown").removeClass("atbd_drop--active");
        $(this).toggleClass("atbd_drop--active");
        e.stopPropagation();
      }
    });
    $(document).on("click", function (e) {
      if ($(e.target).is(".atbd_dropdown, .atbd_drop--active") === false) {
        $(".atbd_dropdown").removeClass("atbd_drop--active");
      }
    });
    $('body').on('click', '.atbd_dropdown-toggle', function (e) {
      e.preventDefault();
    });

    // Directorist Dropdown
    $('body').on('click', '.directorist-dropdown-js .directorist-dropdown__toggle-js', function (e) {
      e.preventDefault();
      if (!$(this).siblings('.directorist-dropdown__links-js').is(':visible')) {
        $('.directorist-dropdown__links').hide();
      }
      $(this).siblings('.directorist-dropdown__links-js').toggle();
    });
    $('body').on('click', function (e) {
      if (!e.target.closest('.directorist-dropdown-js')) {
        $('.directorist-dropdown__links-js').hide();
      }
    });
  });
})(jQuery);

/***/ }),

/***/ "./assets/src/js/public/components/directoristFavorite.js":
/*!****************************************************************!*\
  !*** ./assets/src/js/public/components/directoristFavorite.js ***!
  \****************************************************************/
/***/ (function() {

;
(function ($) {
  // Make sure the codes in this file runs only once, even if enqueued twice
  if (typeof window.directorist_favorite_executed === 'undefined') {
    window.directorist_favorite_executed = true;
  } else {
    return;
  }
  window.addEventListener('load', function () {
    // Add or Remove from favourites
    $('.directorist-action-bookmark').on('click', function (e) {
      e.preventDefault();
      var data = {
        'action': 'atbdp_public_add_remove_favorites',
        'directorist_nonce': directorist.directorist_nonce,
        'post_id': $(this).data('listing_id')
      };
      $.post(directorist.ajaxurl, data, function (response) {
        if (response) {
          $('.directorist-action-bookmark').html(response);
        }
      });
    });
    $('.directorist-favourite-remove-btn').each(function () {
      $(this).on('click', function (event) {
        event.preventDefault();
        var data = {
          'action': 'atbdp-favourites-all-listing',
          'directorist_nonce': directorist.directorist_nonce,
          'post_id': $(this).data('listing_id')
        };
        $(".directorist-favorite-tooltip").hide();
        $.post(directorist.ajaxurl, data, function (response) {
          var post_id = data['post_id'].toString();
          var staElement = $('.directorist_favourite_' + post_id);
          if ('false' === response) {
            staElement.remove();
          }
        });
      });
    });
    $('body').on("click", '.directorist-mark-as-favorite__btn', function (event) {
      event.preventDefault();
      var data = {
        'action': 'atbdp-favourites-all-listing',
        'directorist_nonce': directorist.directorist_nonce,
        'post_id': $(this).data('listing_id')
      };
      var fav_tooltip_success = '<span>' + directorist.i18n_text.added_favourite + '</span>';
      var fav_tooltip_warning = '<span>' + directorist.i18n_text.please_login + '</span>';
      $(".directorist-favorite-tooltip").hide();
      $.post(directorist.ajax_url, data, function (response) {
        var post_id = data['post_id'].toString();
        var staElement = $('.directorist-fav_' + post_id);
        var data_id = staElement.attr('data-listing_id');
        if (response === "login_required") {
          staElement.children(".directorist-favorite-tooltip").append(fav_tooltip_warning);
          staElement.children(".directorist-favorite-tooltip").fadeIn();
          setTimeout(function () {
            staElement.children(".directorist-favorite-tooltip").children("span").remove();
          }, 3000);
        } else if ('false' === response) {
          staElement.removeClass('directorist-added-to-favorite');
          $(".directorist-favorite-tooltip span").remove();
        } else {
          if (data_id === post_id) {
            staElement.addClass('directorist-added-to-favorite');
            staElement.children(".directorist-favorite-tooltip").append(fav_tooltip_success);
            staElement.children(".directorist-favorite-tooltip").fadeIn();
            setTimeout(function () {
              staElement.children(".directorist-favorite-tooltip").children("span").remove();
            }, 3000);
          }
        }
      });
    });
  });
})(jQuery);

/***/ }),

/***/ "./assets/src/js/public/components/directoristSorting.js":
/*!***************************************************************!*\
  !*** ./assets/src/js/public/components/directoristSorting.js ***!
  \***************************************************************/
/***/ (function() {

;
(function ($) {
  // Make sure the codes in this file runs only once, even if enqueued twice
  if (typeof window.directorist_sorting_executed === 'undefined') {
    window.directorist_sorting_executed = true;
  } else {
    return;
  }
  window.addEventListener('load', function () {
    // Sorting Js
    if (!$('.directorist-instant-search').length) {
      $('.directorist-dropdown__links__single-js').click(function (e) {
        e.preventDefault();
        var href = $(this).attr('data-link');
        $('#directorsit-listing-sort').attr('action', href);
        $('#directorsit-listing-sort').submit();
      });
    }

    //sorting toggle
    $('.sorting span').on('click', function () {
      $(this).toggleClass('fa-sort-amount-asc fa-sort-amount-desc');
    });
  });
})(jQuery);

/***/ }),

/***/ "./assets/src/js/public/components/general.js":
/*!****************************************************!*\
  !*** ./assets/src/js/public/components/general.js ***!
  \****************************************************/
/***/ (function() {

// Fix listing with no thumb if card width is less than 220px
(function ($) {
  window.addEventListener('load', function () {
    if ($('.directorist-listing-no-thumb').innerWidth() <= 220) {
      $('.directorist-listing-no-thumb').addClass('directorist-listing-no-thumb--fix');
    }
    // Auhtor Profile Listing responsive fix
    if ($('.directorist-author-listing-content').innerWidth() <= 750) {
      $('.directorist-author-listing-content').addClass('directorist-author-listing-grid--fix');
    }
    // Directorist Archive responsive fix
    if ($('.directorist-archive-grid-view').innerWidth() <= 500) {
      $('.directorist-archive-grid-view').addClass('directorist-archive-grid--fix');
    }

    // Back Button to go back to the previous page
    $('body').on('click', '.directorist-btn__back', function (e) {
      window.history.back();
    });
  });
})(jQuery);

/***/ }),

/***/ "./assets/src/js/public/components/review/starRating.js":
/*!**************************************************************!*\
  !*** ./assets/src/js/public/components/review/starRating.js ***!
  \**************************************************************/
/***/ (function() {

window.addEventListener('load', function () {
  ;
  (function ($) {
    //Star rating
    if ($('.directorist-review-criteria-select').length) {
      $('.directorist-review-criteria-select').barrating({
        theme: 'fontawesome-stars'
      });
    }
  })(jQuery);
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";
/*!********************************************************!*\
  !*** ./assets/src/js/public/modules/author-profile.js ***!
  \********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_general__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../components/general */ "./assets/src/js/public/components/general.js");
/* harmony import */ var _components_general__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_components_general__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _components_review_starRating__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/review/starRating */ "./assets/src/js/public/components/review/starRating.js");
/* harmony import */ var _components_review_starRating__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_components_review_starRating__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_directoristSorting__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/directoristSorting */ "./assets/src/js/public/components/directoristSorting.js");
/* harmony import */ var _components_directoristSorting__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_components_directoristSorting__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _components_directoristAlert__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../components/directoristAlert */ "./assets/src/js/public/components/directoristAlert.js");
/* harmony import */ var _components_directoristAlert__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_components_directoristAlert__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _components_directoristFavorite__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../components/directoristFavorite */ "./assets/src/js/public/components/directoristFavorite.js");
/* harmony import */ var _components_directoristFavorite__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_components_directoristFavorite__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_directoristDropdown__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../components/directoristDropdown */ "./assets/src/js/public/components/directoristDropdown.js");
/* harmony import */ var _components_directoristDropdown__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_components_directoristDropdown__WEBPACK_IMPORTED_MODULE_5__);
// General Components






}();
/******/ })()
;
//# sourceMappingURL=author-profile.js.map