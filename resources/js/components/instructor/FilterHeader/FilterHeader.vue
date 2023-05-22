<template>
  <div class="filter-header">
    <div class='d-flex justify-content-between'>
    <h1 class="filter-header__heading">{{ heading }}</h1>
    <div class="filter-header__col">
    </div>
      <a
        v-if="button.type == 'link'"
        class="filter-header__button"
        :href="button.href"
      >
        <img v-if="button.image" :src="button.image" :alt="button.image" />
        <span>{{ button.text }}</span>
      </a>
      <button
        v-else
        class="filter-header__button"
        @click.prevent="emitButtonPress()"
      >
        <img v-if="button.image" :src="button.image" :alt="button.image" />
        <span>{{ button.text }}</span>
      </button>
    </div>

    <div class="filter-header__filters">
      <div
        v-for="(filter, index) in filters"
        :key="index"
        :class="{
          'filter-header__col': true,
          'filter-header__col--wide': filter.length == 'wide',
          'filter-header__col--semiwide': filter.length == 'semiwide',
        }"
      >
        <span class="filter-header__col-title">{{ filter.title }}</span>
        <div v-if="filter.type == 'select'" class="filter-header__select">
          <select-with-search
            :options="filter.options"
            @value-changed="valueChanged($event, index,'select')"
            :placeholder="filter.placeholder"
          />
        </div>
        <div v-else-if="filter.type == 'multiselect'" class="filter-header__multiselect">
          <multiselect
            @input="valueChanged($event, index, 'multiselect')"
            v-model="filter.value"
            :options="filter.options"
            label="label"
            track-by="code"
            :preserve-search="true"
            :close-on-select="false"
            :clear-on-select="false"
            :multiple="true"
            :placeholder="filter.placeholder"
            deselectLabel=""
            selectLabel=""
            selectedLabel=""
          >
            <template slot="selection" slot-scope="{ values, isOpen }"
              ><span class="multiselect__single" v-if="values.length && !isOpen"
                >{{ values.length }} options selected</span
              ></template
            >
            <template slot="option" slot-scope="props"
              ><div class="option__checkbox">
                {{ props.option.label }}
              </div></template
            >
            <template slot="noResult">Sorry, no matching options.</template>
          </multiselect>
        </div>
        <div v-else-if="filter.type == 'search'" class="filter-header__search">
          <input
            class="filter-header__search-input"
            type="text"
            :placeholder="filter.placeholder"
            @change="valueChanged($event, index, 'search')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SelectWithSearch from "../../student/SelectWithSearch/SelectWithSearch.vue";

export default {
  name: "FilterHeader",
  components: {
    SelectWithSearch,
  },
  props: {
    heading: {
      type: String,
      default: "",
    },
    filters: {
      type: Array,
      default: () => {
        return [];
      },
    },
    button: {
      type: Object,
      default: () => {
        return {
          type: "button",
          text: "",
          image: "",
          href: "",
        };
      },
    },
  },
  methods: {
    emitButtonPress() {
      this.$emit("button-clicked");
    },
    valueChanged(event, filterIndex, type) {
      this.$emit("filter-changed", {
        value: event,
        filterIndex,
        type
      });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./FilterHeader.scss";

.filter-header__col{
  width: 50%;
}
.filter-header__button{
  width: auto;
  padding: 4px 12px;
}
</style>