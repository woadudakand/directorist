import debounce from "../../global/components/debounce";
(function ($) {
  let full_url = window.location.href;
  // Globally accessible form_data
  let form_data = {};

  // Update or retain existing keys in form_data
  const updateFormData = (newData = {}) => {
    Object.entries(newData).forEach(([key, value]) => {
      form_data[key] = value;
    });
  };

  // Update search URL with form data
  function update_instant_search_url(form_data) {
    console.log("update_instant_search_url", { form_data });
    if (!history.pushState) return;

    let newurl =
      window.location.protocol +
      "//" +
      window.location.host +
      window.location.pathname;
    let query = "";

    const appendQuery = (key, value) => {
      if (
        value !== undefined &&
        value !== null &&
        value !== "" &&
        (!Array.isArray(value) || value.length)
      ) {
        if (Array.isArray(value) && value.length) {
          query += (query.length ? "&" : "?") + `${key}[]=${value}`;
        } else {
          query +=
            (query.length ? "&" : "?") + `${key}=${encodeURIComponent(value)}`;
        }
      }
    };

    const ignoreKeys = [
      "data_atts",
      "custom_field",
      "current_page_id",
      "action",
      "_nonce",
    ];

    // Handle all form_data keys dynamically
    Object.entries(form_data).forEach(([key, value]) => {
      if (ignoreKeys.includes(key)) return;

      if (key === "price" && Array.isArray(value)) {
        appendQuery("price[0]", value[0] > 0 ? value[0] : "");
        appendQuery("price[1]", value[1] > 0 ? value[1] : "");
      } else if (
        (key === "cityLat" || key === "cityLng") &&
        !form_data.address
      ) {
        return; // Skip cityLat/cityLng if no address
      } else {
        appendQuery(key, value);
      }
    });

    // Handle custom_field
    if (form_data.custom_field && typeof form_data.custom_field === "object") {
      Object.entries(form_data.custom_field).forEach(([key, val]) => {
        appendQuery(key, val);
      });
    }

    const finalUrl = query ? newurl + query : newurl;
    window.history.pushState({ path: finalUrl }, "", finalUrl);
  }

  // Get URL Parameter
  function getURLParameter(url, name) {
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
    var results = regex.exec(url);
    if (!results || !results[2]) {
      return "";
    }

    return decodeURIComponent(results[2]);
  }

  // Close Search Modal
  function closeAllSearchModal() {
    var searchModalElement = document.querySelectorAll(
      ".directorist-search-modal"
    );

    searchModalElement.forEach((modal) => {
      var modalOverlay = modal.querySelector(
        ".directorist-search-modal__overlay"
      );
      var modalContent = modal.querySelector(
        ".directorist-search-modal__contents"
      );
      var modalBodyOverlay = document.querySelector(
        ".directorist-content-active"
      );

      // Overlay Style
      if (modalOverlay) {
        modalOverlay.style.cssText =
          "opacity: 0; visibility: hidden; transition: 0.5s ease";
        // remove overlay class on body
        modalBodyOverlay.classList.remove("directorist-overlay-active");
      }

      // Modal Content Style
      if (modalContent) {
        modalContent.style.cssText =
          "opacity: 0; visibility: hidden; bottom: -200px;";
      }
    });
  }

  // Scrolling Pagination
  let page = 1;
  let infinitePaginationIsLoading = false;
  let infinitePaginationCompleted = false;

  function handleScroll() {
    const container = $(
      ".directorist-infinite-scroll .directorist-container-fluid .directorist-row"
    );
    if (!container.length || infinitePaginationIsLoading) return;

    const containerBottom = container.offset().top + container.outerHeight();
    const scrollBottom = window.scrollY + window.innerHeight;

    if (scrollBottom >= containerBottom) {
      infinitePaginationIsLoading = true;
      page++;

      const instantSearchElement = $(".directorist-instant-search");
      const activeForm = getActiveForm(instantSearchElement);
      const formData = buildFormData(activeForm, instantSearchElement);

      loadMoreListings(formData);
    }
  }

  window.addEventListener("scroll", function () {
    if (infinitePaginationCompleted) return;
    handleScroll();
  });

  /* Directorist instant search submit */
  $("body").on("submit", ".directorist-instant-search form", function (e) {
    e.preventDefault();

    let _this = $(this);

    // Filter Listing
    filterListing(_this);
  });

  /* Directorist instant search reset */
  $("body").on(
    "click",
    ".directorist-instant-search .directorist-btn-reset-js",
    function (e) {
      console.log("reset button clicked");
      e.preventDefault();
      let instant_search_element = $(this).closest(
        ".directorist-instant-search"
      );

      const activeForm = getActiveForm(instant_search_element);

      // Filter Listing
      filterListing(activeForm);
    }
  );

  $("body").on(
    "submit",
    ".widget .default-ad-search:not(.directorist_single) .directorist-advanced-filter__form",
    function (e) {
      if ($(".directorist-instant-search").length) {
        e.preventDefault();
        let _this = $(this);

        // Filter Listing
        filterListing(_this);
      }
    }
  );

  // Directorist type changes
  $("body").on(
    "click",
    ".directorist-instant-search .directorist-type-nav__link",
    function (e) {
      e.preventDefault();

      const $parentLi = $(this).closest("li");
      $parentLi
        .addClass("directorist-type-nav__list__current")
        .siblings("li")
        .removeClass("directorist-type-nav__list__current");

      const typeMatch = $(this)
        .attr("href")
        ?.match(/type=([^&]+)/);
      const directory_type = typeMatch ? typeMatch[1] : "";

      updateFormData({ directory_type }); // ✅ only update `directory_type`, preserve others

      update_instant_search_url(form_data);

      let instant_search_element = $(this).closest(
        ".directorist-instant-search"
      );

      const activeForm = getActiveForm(instant_search_element);

      // Filter Listing
      filterListing(activeForm);
    }
  );

  $("body").on("click", ".disabled-link", function (e) {
    e.preventDefault();
  });

  // Directorist view as changes
  $("body").on(
    "click",
    ".directorist-instant-search .directorist-viewas .directorist-viewas__item",
    function (e) {
      e.preventDefault();

      const viewMatch = $(this)
        .attr("href")
        ?.match(/view=([^&]+)/);
      const view = viewMatch ? viewMatch[1] : "";

      updateFormData({ view }); // ✅ only update `view`, preserve others

      update_instant_search_url(form_data);

      let instant_search_element = $(this).closest(
        ".directorist-instant-search"
      );

      const activeForm = getActiveForm(instant_search_element);

      // Filter Listing
      filterListing(activeForm);
    }
  );

  $(".directorist-instant-search .directorist-dropdown__links__single-js").off(
    "click"
  );

  // Directorist sort by changes
  $("body").on(
    "click",
    ".directorist-instant-search .directorist-sortby-dropdown .directorist-dropdown__links__single-js",
    function (e) {
      e.preventDefault();

      $(this)
        .addClass("active")
        .siblings(".directorist-dropdown__links__single-js")
        .removeClass("active");

      let sort_href = $(this).attr("data-link");
      let sort_by =
        sort_href && sort_href.length ? sort_href.match(/sort=.+/) : "";
      let sort =
        sort_by && sort_by.length ? sort_by[0].replace(/sort=/, "") : "";

      console.log("CHK", { sort, sort_by });

      updateFormData({ sort }); // ✅ only update `sort`, preserve others

      update_instant_search_url(form_data);

      let instant_search_element = $(this).closest(
        ".directorist-instant-search"
      );

      const activeForm = getActiveForm(instant_search_element);

      // Filter Listing
      filterListing(activeForm);
    }
  );

  // Directorist pagination
  $("body").on(
    "click",
    ".directorist-instant-search .directorist-pagination .page-numbers",
    function (e) {
      e.preventDefault();
      const currentPage = $(this).text();
      if (currentPage) {
        page = parseInt(currentPage);
      } else if ($(this).hasClass("next")) {
        page = parseInt(page) + 1;
      } else if ($(this).hasClass("prev")) {
        page = parseInt(page) - 1;
      }

      let instant_search_element = $(this).closest(
        ".directorist-instant-search"
      );

      const activeForm = getActiveForm(instant_search_element);

      // Filter Listing
      filterListing(activeForm);
    }
  );

  // Helper function to determine the active form
  function getActiveForm(instantSearchElement) {
    const sidebarListing = instantSearchElement.find(".listing-with-sidebar");
    const advancedForm = instantSearchElement.find(
      ".directorist-advanced-filter__form"
    );
    const searchForm = instantSearchElement.find(".directorist-search-form");
    return sidebarListing.length
      ? instantSearchElement
      : screen.width > 575
      ? advancedForm
      : searchForm;
  }

  // Helper function for form data validation
  const formDataValidation = (key, value) => {
    return value !== undefined && value !== null && value !== ""
      ? value
      : undefined;
  };

  // Helper function to build form data
  function buildFormData(searchElm) {
    console.log("buildFormData", { searchElm, form_data });

    let tag = [];
    let price = [];
    let custom_field = {};
    let search_by_rating = [];

    // Collect selected tags
    searchElm.find('input[name^="in_tag["]:checked').each(function () {
      tag.push($(this).val());
    });

    // Collect selected search by rating
    searchElm
      .find('input[name^="search_by_rating["]:checked')
      .each(function () {
        search_by_rating.push($(this).val());
      });

    // Collect selected price values
    searchElm.find('input[name^="price["]').each(function () {
      price.push($(this).val());
    });

    // Collect custom field values
    searchElm.find('[name^="custom_field"]').each(function (_, el) {
      const $el = $(el);
      const name = $el.attr("name");
      const type = $el.attr("type");
      const match = name.match(/^custom_field\[(.+?)\]/);
      const post_id = match ? match[1] : "";

      if (type === "radio") {
        const checked = searchElm
          .find(`input[name="custom_field[${post_id}]"]:checked`)
          .val();
        if (checked) custom_field[post_id] = checked;
      } else if (type === "checkbox") {
        const values = [];
        searchElm
          .find(`input[name="custom_field[${post_id}][]"]:checked`)
          .each(function () {
            const val = $(this).val();
            if (val) values.push(val);
          });
        if (values.length) custom_field[post_id] = values;
      } else {
        const value = $el.val();
        if (value) custom_field[post_id] = value;
      }
    });

    // Collect form values
    const q = searchElm.find('input[name="q"]').val();
    const in_cat = searchElm.find(".directorist-category-select").val();
    const in_loc = searchElm.find(".directorist-location-select").val();
    const price_range = searchElm
      .find("input[name='price_range']:checked")
      .val();
    const address = searchElm.find('input[name="address"]').val();
    const zip = searchElm.find('input[name="zip"]').val();
    const fax = searchElm.find('input[name="fax"]').val();
    const email = searchElm.find('input[name="email"]').val();
    const website = searchElm.find('input[name="website"]').val();
    const phone = searchElm.find('input[name="phone"]').val();

    const isQueryRequired = searchElm.find('input[name="q"]').prop("required");
    const isCategoryRequired = searchElm
      .find(".directorist-category-select")
      .prop("required");
    const isLocationRequired = searchElm
      .find(".directorist-location-select")
      .prop("required");

    let requiredFieldsAreValid = true;
    if (isQueryRequired && !q) requiredFieldsAreValid = false;
    if (isCategoryRequired && (!in_cat || in_cat.length === 0))
      requiredFieldsAreValid = false;
    if (isLocationRequired && (!in_loc || in_loc.length === 0))
      requiredFieldsAreValid = false;

    // const page_no = searchElm.find(".page-numbers.current").text();

    // ✅ Update global form_data
    updateFormData({
      q: formDataValidation("q", q),
      in_cat: formDataValidation("in_cat", in_cat),
      in_loc: formDataValidation("in_loc", in_loc),
      in_tag: formDataValidation("in_tag", tag),
      price: formDataValidation("price", price),
      price_range: formDataValidation("price_range", price_range),
      search_by_rating: formDataValidation(
        "search_by_rating",
        search_by_rating
      ),
      address: formDataValidation("address", address),
      zip: formDataValidation("zip", zip),
      fax: formDataValidation("fax", fax),
      email: formDataValidation("email", email),
      website: formDataValidation("website", website),
      phone: formDataValidation("phone", phone),
      custom_field: formDataValidation("custom_field", custom_field),
    });

    // Check if "open_now" checkbox is checked and update form_data
    if (searchElm.find('input[name="open_now"]').is(":checked")) {
      updateFormData({
        open_now: formDataValidation(
          "open_now",
          searchElm.find('input[name="open_now"]').val()
        ),
      });
    }

    // Check if address is available and update cityLat, cityLng, and miles
    if (formDataValidation("address", form_data.address)) {
      updateFormData({
        cityLat: formDataValidation(
          "cityLat",
          searchElm.find("#cityLat").val()
        ),
        cityLng: formDataValidation(
          "cityLng",
          searchElm.find("#cityLng").val()
        ),
        miles: formDataValidation(
          "miles",
          searchElm.find('input[name="miles"]').val()
        ),
      });
    } else {
      updateFormData({
        cityLat: formDataValidation("cityLat", ""),
        cityLng: formDataValidation("cityLng", ""),
        miles: formDataValidation("miles", ""),
      });
    }

    // Check if zip is available and update zip_cityLat, zip_cityLng, and miles
    if (formDataValidation("zip", form_data.zip)) {
      updateFormData({
        zip_cityLat: formDataValidation(
          "zip_cityLat",
          searchElm.find(".zip-cityLat").val()
        ),
        zip_cityLng: formDataValidation(
          "zip_cityLng",
          searchElm.find(".zip-cityLng").val()
        ),
      });
    } else {
      updateFormData({
        zip_cityLat: formDataValidation("zip_cityLat", ""),
        zip_cityLng: formDataValidation("zip_cityLng", ""),
      });
    }

    // If page is available, update the paged field
    if (page) {
      updateFormData({ paged: page });
    }
  }

  // Helper function to get sort value
  function getSortValue(instantSearchElement) {
    const sortHref = instantSearchElement
      .find(
        ".directorist-sortby-dropdown .directorist-dropdown__links__single.active"
      )
      .data("link");
    return sortHref ? sortHref.split("sort=")[1] : "";
  }

  // Helper function to get directory type
  function getDirectoryType(instantSearchElement) {
    const typeHref = instantSearchElement
      .find(
        ".directorist-type-nav__list .directorist-type-nav__list__current a"
      )
      .attr("href");
    return typeHref ? getURLParameter(typeHref, "directory_type") : "";
  }

  // AJAX call to load more listings
  function loadMoreListings(formData) {
    let loadingDiv;
    const container = $(
      ".directorist-infinite-scroll .directorist-container-fluid .directorist-row"
    );

    $.ajax({
      url: directorist.ajaxurl,
      type: "POST",
      data: formData,
      beforeSend: function () {
        loadingDiv = $("<div>", {
          class: "directorist-on-scroll-loading",
        }).append(
          $("<div>", { class: "directorist-spinner" }),
          $("<span>").text("Loading more...")
        );
        container.append(loadingDiv);
      },
      success: function (html) {
        if (loadingDiv) loadingDiv.remove();

        if (html.count > 0) {
          container.append(html.render_listings);
        } else {
          infinitePaginationCompleted = true;
        }

        triggerCustomEvents();
      },
      complete: function () {
        infinitePaginationIsLoading = false;
        if (loadingDiv) loadingDiv.remove();
      },
    });
  }

  // Helper function to trigger custom events
  function triggerCustomEvents() {
    window.dispatchEvent(new Event("directorist-instant-search-reloaded"));
    window.dispatchEvent(new Event("directorist-reload-listings-map-archive"));
  }

  // Filter on AJAX Search
  function filterListing(searchElm) {
    buildFormData(searchElm);

    // Passing form_data to update the URL
    update_instant_search_url(form_data);

    console.log("@filterListing", { searchElm, form_data });

    // Get the parent element
    const instant_search_element = searchElm.closest(
      ".directorist-instant-search"
    );
    // Get the data attributes from the parent element
    const dataAtts = JSON.parse(instant_search_element.attr("data-atts"));

    // Prepare final payload for search
    const ajaxData = {
      ...form_data,
      action: "directorist_instant_search",
      _nonce: directorist.ajax_nonce,
      current_page_id: directorist.current_page_id,
      data_atts: dataAtts,
    };

    // 🔍 Run the search with the updated data
    performInstantSearch(ajaxData, instant_search_element);
  }

  // Perform Instant Search
  function performInstantSearch(ajaxData, contextElm) {
    console.log("performInstantSearch", {
      ajaxData,
      contextElm,
    });

    $.ajax({
      url: directorist.ajaxurl,
      type: "POST",
      data: ajaxData,
      beforeSend: function () {
        contextElm
          .find(".directorist-advanced-filter__form .directorist-btn-sm")
          .attr("disabled", true);
        contextElm
          .find(".directorist-archive-items")
          .addClass("atbdp-form-fade");
        contextElm
          .find(".directorist-header-bar .directorist-advanced-filter")
          .removeClass("directorist-advanced-filter--show")
          .hide();

        if (contextElm.offset()?.top > 0) {
          $(document).scrollTop(contextElm.offset().top);
        }

        closeAllSearchModal();
      },
      success: function (html) {
        console.log("AJAX succeed", {
          html,
          search_result: html.search_result,
        });
        if (html.search_result) {
          contextElm
            .find(".directorist-header-found-title, .dsa-save-search-container")
            .remove();
          if (html.header_title) {
            contextElm
              .find(".directorist-listings-header__left")
              .append(html.header_title);
            contextElm
              .find(".directorist-header-found-title span")
              .text(html.count);
          }
          contextElm
            .find(".directorist-archive-items")
            .replaceWith(html.search_result)
            .removeClass("atbdp-form-fade");
          contextElm
            .find(".directorist-advanced-filter__form .directorist-btn-sm")
            .attr("disabled", false);

          window.dispatchEvent(
            new CustomEvent("directorist-instant-search-reloaded")
          );
          window.dispatchEvent(
            new CustomEvent("directorist-reload-listings-map-archive")
          );

          // Optional: Update meta title
          let new_meta_title = "";
          if (html.category_name) new_meta_title += html.category_name;
          if (html.location_name)
            new_meta_title +=
              (new_meta_title ? " within " : "") + html.location_name;
          if (form_data.address)
            new_meta_title +=
              (form_data.in_cat || form_data.in_loc ? " near " : "") +
              form_data.address;
          document.title = new_meta_title
            ? `${new_meta_title} | ${directorist.site_name}`
            : directorist.site_name;
        }
      },
    });
  }

  // Range Slider searching observer
  function initObserver() {
    let targetNodes = document.querySelectorAll(
      ".directorist-instant-search .directorist-custom-range-slider__value input"
    );

    targetNodes.forEach((targetNode) => {
      let searchElm = $(targetNode.closest("form"));

      if (targetNode) {
        let timeout;
        const observerCallback = (mutationList, observer) => {
          for (const mutation of mutationList) {
            if (mutation.attributeName == "value") {
              clearTimeout(timeout);
              timeout = setTimeout(() => {
                filterListing(searchElm);
              }, 250);
            }
          }
        };

        const observer = new MutationObserver(observerCallback);
        observer.observe(targetNode, {
          attributes: true,
          childList: true,
          subtree: true,
        });
      }
    });
  }

  // Single Location Category Page Search Form Item Disable
  function singleCategoryLocationInit() {
    const directoristArchiveContents = document.querySelector(
      ".directorist-archive-contents"
    );
    if (!directoristArchiveContents) {
      return;
    }

    const directoristDataAttributes = directoristArchiveContents.getAttribute(
      "data-atts"
    );
    const { shortcode, location, category } = JSON.parse(
      directoristDataAttributes
    );

    if (shortcode === "directorist_category" && category.trim() !== "") {
      const categorySelect = document.querySelector(
        ".directorist-search-form .directorist-category-select"
      );
      if (categorySelect) {
        categorySelect
          .closest(".directorist-search-category")
          .classList.add("directorist-search-form__single-category");
      }
    }

    if (shortcode === "directorist_location" && location.trim() !== "") {
      const locationSelect = document.querySelector(
        ".directorist-search-form .directorist-location-select"
      );
      if (locationSelect) {
        locationSelect
          .closest(".directorist-search-location")
          .classList.add("directorist-search-form__single-location");
      }
    }
  }

  // sidebar on keyup searching
  $("body").on(
    "keyup",
    ".directorist-instant-search .listing-with-sidebar form",
    debounce(function (e) {
      console.log("keyup event triggered");
      if (
        $(e.target).closest(".directorist-custom-range-slider__value").length >
        0
      ) {
        return; // Skip calling `filterListing` for this element
      }

      e.preventDefault();
      var searchElm = $(this).closest(".listing-with-sidebar");
      filterListing(searchElm);
    }, 250)
  );

  // sidebar on change searching
  $("body").on(
    "change",
    ".directorist-instant-search .listing-with-sidebar input[type='checkbox'],.directorist-instant-search .listing-with-sidebar input[type='radio'], .directorist-instant-search .listing-with-sidebar input[type='time'], .directorist-instant-search .listing-with-sidebar input[type='date'], .directorist-custom-range-slider__wrap .directorist-custom-range-slider__range, .directorist-search-location .location-name",
    debounce(function (e) {
      console.log("change event triggered(radio/checkbox/location/range)");
      e.preventDefault();
      var searchElm = $(this).closest(".listing-with-sidebar");
      filterListing(searchElm);
    }, 250)
  );

  // sidebar on change location, zipcode changing
  $("body").on(
    "change",
    ".directorist-instant-search .listing-with-sidebar .directorist-search-location, .directorist-instant-search .listing-with-sidebar .directorist-zipcode-search",
    debounce(function (e) {
      console.log("change event triggered (zipcode/location)");
      e.preventDefault();

      const searchElm = $(this).closest(".listing-with-sidebar");

      // If it's a location field, ensure it has a value before triggering the filter
      if ($(this).hasClass("directorist-search-location")) {
        const locationField = $(this).find('input[name="address"]');
        if (!locationField.val()) {
          return;
        }
      }

      filterListing(searchElm);
    }, 250)
  );

  // select on change with value - searching
  $("body").on(
    "change",
    ".directorist-instant-search .listing-with-sidebar select",
    debounce(function (e) {
      if (!$(this).val()) {
        return; // Skip calling `filterListing` if the value is empty
      }

      console.log("change event triggered (select)", { value: $(this).val() });
      e.preventDefault();
      var searchElm = $(this).val() && $(this).closest(".listing-with-sidebar");
      filterListing(searchElm);
    }, 250)
  );

  // select on change with value - searching
  $("body").on(
    "click",
    ".directorist-instant-search .listing-with-sidebar .directorist-filter-location-icon",
    debounce(function (e) {
      console.log("click event triggered (location icon)");
      e.preventDefault();
      var searchElm = $(this).closest(".listing-with-sidebar");
      filterListing(searchElm);
    }, 1000)
  );

  // Clear Input Value
  $("body").on(
    "click",
    ".directorist-instant-search .directorist-search-field__btn--clear",
    function (e) {
      let inputValue = $(this)
        .closest(".directorist-search-field")
        .find('input:not([type="checkbox"]):not([type="radio"]), select')
        .val("");

      console.log("clear input value triggered", inputValue);

      if (inputValue) {
        let searchElm = $(
          document.querySelector(
            ".directorist-instant-search .listing-with-sidebar form"
          )
        );
        if (searchElm) {
          filterListing(searchElm);
        }
      }
    }
  );

  if ($(".directorist-instant-search").length === 0) {
    $("body").on(
      "submit",
      ".listing-with-sidebar .directorist-basic-search, .listing-with-sidebar .directorist-advanced-search",
      function (e) {
        e.preventDefault();
        let basic_data = $(
          ".listing-with-sidebar .directorist-basic-search"
        ).serialize();
        let advanced_data = $(
          ".listing-with-sidebar .directorist-advanced-search"
        ).serialize();
        let action_value = $(".directorist-advanced-search").attr("action");
        let url = action_value + "?" + basic_data + "&" + advanced_data;

        window.location.href = url;
      }
    );
  }

  window.addEventListener("load", function () {
    debounce(initObserver(), 250);

    singleCategoryLocationInit();
  });
})(jQuery);
