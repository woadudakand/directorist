import { mapState } from "vuex";
import helpers from "./../helpers";
import props from "./input-field-props.js";

export default {
  mixins: [props, helpers],
  model: {
    prop: "value",
    event: "update",
  },

  created() {
    this.setup();
  },

  watch: {
    theOptions() {
      if ( ! this.valueIsValid( this.value ) ) {
        this.$emit( "update", "" );
      }
    },
  },

  computed: {
    ...mapState({
      fields: "fields",
    }),

    theDefaultOption() {
      if (this.defaultOption && typeof this.defaultOption === "object") {
        return this.defaultOption;
      }

      return { value: "", label: "Select..." };
    },

    theCurrentOptionLabel() {
      if (!this.optionsInObject) {
        return "";
      }
      if (typeof this.optionsInObject[this.value] === "undefined") {
        return this.theDefaultOption.value == this.value &&
          this.theDefaultOption.label
          ? this.theDefaultOption.label
          : "";
      }

      return this.optionsInObject[this.value];
    },

    theOptions() {
      if (this.hasOptionsSource) {
        return this.parseOptions( this.hasOptionsSource );
      }

      if (!this.options || typeof this.options !== "object") {
        return this.defaultOption ? [this.defaultOption] : [];
      }

      return this.parseOptions( this.options );
    },

    hasOptionsSource() {
      if (!this.optionsSource || typeof this.optionsSource !== "object") {
        return false;
      }

      if (typeof this.optionsSource.where !== "string") {
        return false;
      }

      let terget_fields = this.getTergetFields({
        path: this.optionsSource.where,
      });

      if (!terget_fields || typeof terget_fields !== "object") {
        return false;
      }

      let filter_by = null;
      if (
        typeof this.optionsSource.filter_by === "string" &&
        this.optionsSource.filter_by.length
      ) {
        filter_by = this.optionsSource.filter_by;
      }

      if (filter_by) {
        filter_by = this.getTergetFields({
          path: this.optionsSource.filter_by,
        });
      }

      let has_sourcemap = false;

      if (
        this.optionsSource.source_map &&
        typeof this.optionsSource.source_map === "object"
      ) {
        has_sourcemap = true;
      }

      if (!has_sourcemap && !filter_by) {
        return terget_fields;
      }

      if (has_sourcemap) {
        terget_fields = this.mapDataByMap(
          terget_fields,
          this.optionsSource.source_map,
        );
      }

      if (filter_by) {
        terget_fields = this.filterDataByValue(terget_fields, filter_by);
      }

      if (!terget_fields && typeof terget_fields !== "object") {
        return false;
      }

      return terget_fields;
    },

    formGroupClass() {
      var validation_classes = this.validationLog.inputErrorClasses
        ? this.validationLog.inputErrorClasses
        : {};

      return {
        ...validation_classes,
      };
    },
  },

  data() {
    return {
      local_value_ms: [],
      optionsInObject: {},
      show_option_modal: false,
      clickEvent: null,
      validationLog: {},
    };
  },

  methods: {
    setup() {
      if (this.defaultOption || typeof this.defaultOption === "object") {
        this.default_option = this.defaultOption;
      }

      this.optionsInObject = this.convertOptionsToObject();

      if ( ! this.valueIsValid( this.value ) ) {
        this.$emit( "update", "" );
      }

      const self = this;
      document.addEventListener("click", function () {
        self.show_option_modal = false;
      });
    },

    update_value( value ) {
      this.$emit( "update", value );
    },

    updateOption(value) {
      this.update_value(value);
      this.show_option_modal = false;
    },

    toggleTheOptionModal() {
      let self = this;

      if (this.show_option_modal) {
        this.show_option_modal = false;
      } else {
        this.show_option_modal = true;

        setTimeout(function () {
          self.show_option_modal = true;
        }, 0);
      }
    },

    valueIsValid( value ) {
      return this.theOptions.map( item => item.value ).includes( `${value}` );
    },

    parseOptions( options ) {
      return options.map( item => ( {
        ...item,
        value: typeof item.value !== 'undefined' ? `${item.value}` : '',
      } ) );
    },

    convertOptionsToObject() {
      if (!(this.theOptions && Array.isArray(this.theOptions))) {
        return null;
      }

      let option_object = {};
      for (let option in this.theOptions) {
        if (typeof this.theOptions[option].value === "undefined") {
          continue;
        }

        let label = this.theOptions[option].label
          ? this.theOptions[option].label
          : "";
        option_object[this.theOptions[option].value] = label;
      }

      return option_object;
    },

    /* syncValidationWithLocalState( validation_log ) {
            return validation_log;
        } */
  },
};
