<template>
  <div class="cptm-form-group" :class="formGroupClass">
    <div class="atbdp-row">
      <div class="atbdp-col atbdp-col-4">
        <label
          :class="{ 'atbdp-label-icon-wrapper': icon.length }"
          v-if="label.length"
        >
          <div class="atbdp-label-icon" v-if="icon.length" v-html="icon"></div>
          <component :is="labelType" v-html="label"></component>
        </label>
        <p
          class="cptm-form-group-info"
          v-if="description.length"
          v-html="description"
        ></p>
      </div>

      <div class="atbdp-col atbdp-col-8">
        <div
          class="directorist_dropdown"
          :class="{ ['--open']: show_option_modal }"
        >
          <a
            href="#"
            class="directorist_dropdown-toggle"
            @click.prevent="toggleTheOptionModal()"
          >
            <span class="directorist_dropdown-toggle__text">{{
              theCurrentOptionLabel
            }}</span>
          </a>

          <div
            class="directorist_dropdown-option"
            v-if="theOptions"
            :class="{ ['--show']: show_option_modal }"
          >
            <ul>
              <li v-if="showDefaultOption && theDefaultOption">
                <a
                  href="#"
                  v-html="theDefaultOption.label ? theDefaultOption.label : ''"
                  @click.prevent="updateOption(theDefaultOption.value)"
                >
                </a>
              </li>
              <li v-for="(option, option_key) in theOptions" :key="option_key">
                <a
                  href="#"
                  :class="{ active: option.value == value ? true : false }"
                  v-html="option.label ? option.label : ''"
                  @click.prevent="updateOption(option.value)"
                >
                </a>
              </li>
            </ul>
          </div>
        </div>

        <select
          @change="update_value($event.target.value)"
          :value="value"
          class="cptm-d-none"
        >
          <option
            v-if="showDefaultOption && theDefaultOption"
            :value="theDefaultOption.value"
          >
            {{ theDefaultOption.label }}
          </option>
          <option
            v-for="(option, option_key) in theOptions"
            :key="option_key"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>

        <form-field-validatior
          :section-id="sectionId"
          :field-id="fieldId"
          :root="root"
          :value="value"
          :rules="rules"
          v-model="validationLog"
          @validate="$emit('validate', $event)"
        />
      </div>
    </div>
  </div>
</template>

<script>
import select_field from "../../../../mixins/form-fields/select-field";
export default {
  name: "select-field-theme-butterfly",
  mixins: [select_field],
  mounted() {},
};
</script>
