<template>
  <div
    class="cptm-form-builder-group-field-item"
    :class="expandState ? 'expanded' : ''"
    v-if="widget_fields && Object.keys(widget_fields).length > 0"
  >
    <!-- Widget Titlebar -->
    <draggable-list-item
      v-if="canMoveWidget"
      @drag-start="$emit('drag-start')"
      @drag-end="$emit('drag-end')"
    >
      <div class="cptm-form-builder-group-field-item-drag">
        <span aria-hidden="true" class="uil uil-draggabledots"></span>
      </div>
    </draggable-list-item>

    <form-builder-widget-titlebar-component
      :label="widgetTitle"
      :sublabel="widgetSubtitle"
      :icon="widgetIcon"
      :iconType="widgetIconType"
      :expanded="expandState"
      :alert="alert"
      @toggle-expand="toggleExpand"
    />

    <!-- Widget Body -->
    <slide-up-down :active="expandState" :duration="500">
      <div
        class="cptm-form-builder-group-field-item-body"
        v-if="widget_fields && typeof widget_fields === 'object'"
      >
        <field-list-component
          :root="activeWidgets"
          :section-id="widgetKey"
          :field-list="widget_fields"
          :value="activeWidgets[widgetKey] ? activeWidgets[widgetKey] : ''"
          @alert="updateAlert"
          @update="
            $emit('update-widget-field', {
              widget_key: widgetKey,
              payload: $event,
            })
          "
        />
      </div>
    </slide-up-down>

    <!-- Widget Actions -->
    <div
      class="cptm-form-builder-group-actions-dropdown cptm-form-builder-group-actions-dropdown--field"
      ref="dropdownContent"
    >
      <a
        href="#"
        class="cptm-form-builder-group-actions-dropdown-btn"
        v-if="canTrashWidget"
        @click.prevent="toggleExpandedDropdown"
      >
        <span aria-hidden="true" class="uil uil-ellipsis-h"></span>
      </a>

      <!-- Widget Action Dropdown -->
      <slide-up-down :active="expandedDropdown" :duration="500">
        <div
          class="cptm-form-builder-group-actions-dropdown-content"
          :class="expandedDropdown ? 'expanded' : ''"
        >
          <a
            href="#"
            class="cptm-form-builder-field-item-action-link"
            @click.prevent="handleTrashClick"
          >
            <span aria-hidden="true" class="uil uil-trash-alt"></span>
            Remove Field
          </a>
        </div>
      </slide-up-down>
    </div>

    <!-- Confirmation Modal -->
    <confirmation-modal
      :visible="showConfirmationModal"
      :widgetName="widgetName"
      @confirm="trashWidget"
      @cancel="closeConfirmationModal"
    />
  </div>
</template>

<script>
import Vue from "vue";
import { findObjectItem } from "../../../../../helper";
import ConfirmationModal from "./Form_Builder_Widget_Trash_Confirmation.vue";

export default {
  name: "form-builder-widget-component",
  components: {
    ConfirmationModal,
  },
  props: {
    widgetKey: {
      default: "",
    },
    activeWidgets: {
      default: "",
    },
    avilableWidgets: {
      default: "",
    },
    groupData: {
      default: "",
    },
    isEnabledGroupDragging: {
      default: false,
    },
    untrashableWidgets: {
      default: "",
    },
  },

  created() {
    this.sync();
  },

  watch: {
    widgetKey() {
      if (this.activeWidgetsIsUpdating) {
        return;
      }
      this.sync();
    },

    activeWidgets() {
      this.activeWidgetsIsUpdating = true;
      this.sync();
      this.activeWidgetsIsUpdating = false;
    },

    groupDataFields() {
      if (this.activeWidgetsIsUpdating) {
        return;
      }
      this.sync();
    },
  },

  computed: {
    isPresetOrCustomGroup() {
      return (
        this.widget_fields?.widget_group?.value === "preset" ||
        this.widget_fields?.widget_group?.value === "custom"
      );
    },

    groupDataFields() {
      return this.groupData.fields;
    },

    widgetTitle() {
      let label = "";
      if (
        this.activeWidgets[this.widgetKey] &&
        this.activeWidgets[this.widgetKey].label
      ) {
        label = this.activeWidgets[this.widgetKey].label;
      }

      if (!label.length && this.current_widget && this.current_widget.label) {
        label = this.current_widget.label;
      }

      return label;
    },

    widgetSubtitle() {
      let label = "";

      if (
        !(
          this.activeWidgets[this.widgetKey] &&
          this.activeWidgets[this.widgetKey].label
        )
      ) {
        return "";
      }

      if (this.current_widget && this.current_widget.label) {
        label = this.current_widget.label;
      }

      return label;
    },

    widgetIcon() {
      let icon = "";

      if (this.current_widget && this.current_widget.icon) {
        icon = this.current_widget.icon;
      }

      return icon;
    },

    widgetIconType() {
      let iconType = "";

      if (this.current_widget && this.current_widget.iconType) {
        iconType = this.current_widget.iconType;
      }

      return iconType;
    },

    expandState() {
      let state = this.expanded;

      if (!this.isEnabledGroupDragging) {
        state = false;
      }

      return state;
    },

    canTrashWidget() {
      if (typeof this.current_widget.canTrash === "undefined") {
        return true;
      }
      return this.current_widget.canTrash;
    },

    canMoveWidget() {
      if (typeof this.current_widget.canMove === "undefined") {
        return true;
      }
      return this.current_widget.canMove;
    },

    emptySlideUpDownClass() {
      return !this.widget_fields || Object.keys(this.widget_fields).length === 0
        ? "cptm-empty-slide-up-down"
        : "";
    },

    alert() {
      const widgetKeys = Object.keys(this.alerts);

      if (widgetKeys.length < 1) {
        return null;
      }

      const widgetKey = widgetKeys[0];

      if (!this.alerts[widgetKey] || this.alerts.widgetKey !== this.widgetKey) {
        return null;
      }

      const alertKeys = Object.keys(this.alerts[widgetKey]);

      if (alertKeys.length < 1) {
        return null;
      }

      const alertKey = alertKeys[0];

      return this.alerts[widgetKey][alertKey];
    },
  },

  data() {
    return {
      current_widget: "",
      widget_fields: "",
      expanded: false,
      widgetIsDragging: false,

      activeWidgetsIsUpdating: false,
      showConfirmationModal: false,
      widgetName: "",
      expandedDropdown: false,
      alerts: {},
    };
  },

  mounted() {
    document.addEventListener("mousedown", this.handleClickOutside);
  },

  beforeDestroy() {
    document.removeEventListener("mousedown", this.handleClickOutside);
  },

  methods: {
    updateAlert(payload) {
      if (!payload.data) {
        this.removeAlert(payload.key);
        return;
      }

      this.addAlert(payload.key, payload.data);
    },

    addAlert(key, data) {
      this.alerts = {
        ...this.alerts,
        [key]: data,
        widgetKey: this.widgetKey,
      };
    },

    removeAlert(key) {
      if (this.alerts.hasOwnProperty(key)) {
        Vue.delete(this.alerts, key);
      }

      // If only one key remains and it's "widgetKey", remove that too
      const remainingKeys = Object.keys(this.alerts);
      if (remainingKeys.length === 1 && remainingKeys[0] === "widgetKey") {
        Vue.delete(this.alerts, "widgetKey");
      }
    },

    toggleExpandedDropdown() {
      this.expandedDropdown = !this.expandedDropdown;
    },

    handleClickOutside(event) {
      if (
        this.expandedDropdown &&
        !this.$refs.dropdownContent.contains(event.target)
      ) {
        this.expandedDropdown = false;
      }
    },

    handleTrashClick() {
      this.expandedDropdown = !this.expandedDropdown;
      this.openConfirmationModal();
    },

    sync() {
      this.syncCurrentWidget();
      this.syncWidgetFields();
    },

    openConfirmationModal() {
      this.widgetName = this.widgetTitle;
      this.showConfirmationModal = true;

      // Add class to parent with class 'atbdp-cpt-manager'
      const parentElement = this.$el.closest(".atbdp-cpt-manager");
      if (parentElement) {
        parentElement.classList.add("directorist-overlay-visible");
      }
    },

    closeConfirmationModal() {
      this.showConfirmationModal = false;

      // Remove class from parent with class 'atbdp-cpt-manager'
      const parentElement = this.$el.closest(".atbdp-cpt-manager");
      if (parentElement) {
        parentElement.classList.remove("directorist-overlay-visible");
      }
    },

    trashWidget() {
      this.$emit("trash-widget");
      this.closeConfirmationModal();
    },

    syncCurrentWidget() {
      const current_widget = findObjectItem(
        `${this.widgetKey}`,
        this.activeWidgets,
      );

      if (!current_widget) {
        return;
      }

      const widget_group = current_widget.widget_group
        ? current_widget.widget_group
        : "";

      const widget_name = current_widget.original_widget_key
        ? current_widget.original_widget_key
        : current_widget.widget_name
          ? current_widget.widget_name
          : "";

      const widget_child_name = current_widget.widget_child_name
        ? current_widget.widget_child_name
        : "";

      if (!this.avilableWidgets[widget_group]) {
        return;
      }

      let the_current_widget = null;
      let current_widget_name = "";
      let current_widget_child_name = "";

      if (this.avilableWidgets[widget_group][widget_name]) {
        the_current_widget = this.avilableWidgets[widget_group][widget_name];
        current_widget_name = widget_name;
      }

      if (
        the_current_widget &&
        the_current_widget.widgets &&
        the_current_widget.widgets[widget_child_name]
      ) {
        the_current_widget = the_current_widget.widgets[widget_child_name];
        current_widget_child_name = widget_child_name;
      }

      if (!the_current_widget) {
        return;
      }

      this.checkIfHasUntrashableWidget(
        widget_group,
        current_widget_name,
        current_widget_child_name,
      );

      this.current_widget = the_current_widget;
    },

    syncWidgetFields() {
      if (!this.current_widget) {
        return "";
      }
      if (typeof this.current_widget !== "object") {
        return "";
      }
      if (!this.current_widget.options) {
        return "";
      }

      this.widget_fields = this.current_widget.options;
    },

    toggleExpand() {
      this.expanded = !this.expanded;
    },

    checkIfHasUntrashableWidget(widget_group, widget_name, widget_child_name) {
      if (!this.untrashableWidgets) {
        return;
      }
      if (typeof this.untrashableWidgets !== "object") {
        return;
      }

      for (let widget in this.untrashableWidgets) {
        if (this.untrashableWidgets[widget].widget_group !== widget_group) {
          continue;
        }

        if (this.untrashableWidgets[widget].widget_name !== widget_name) {
          continue;
        }

        if (
          widget_child_name &&
          this.untrashableWidgets[widget].widget_child_name !==
            widget_child_name
        ) {
          continue;
        }

        this.$emit("found-untrashable-widget");
        return;
      }
    },
  },
};
</script>
