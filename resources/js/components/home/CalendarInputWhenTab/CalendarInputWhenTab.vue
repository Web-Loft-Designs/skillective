<template>
  <div class="calendar-input-when-tab">

    <div class="calendar-input-when-tab__row">
      <div class="calendar-input-when-tab__tabs">
        <button 
          :class="{
            'calendar-input-when-tab__tab': true,
            'calendar-input-when-tab__tab--active': selectedTab == 'calendar',
          }"
          @click.prevent="selectedTab = 'calendar'"
        >Calendar</button>
        <button 
          :class="{
            'calendar-input-when-tab__tab': true,
            'calendar-input-when-tab__tab--active': selectedTab == 'flexible',
          }"
          @click.prevent="selectedTab = 'flexible'"
        >I’m flexible</button>
      </div>
    </div>

    <div class="calenar-input-whe-tab__tab-content" v-if="selectedTab == 'calendar'">
      <div class="calendar-input-when-tab__row">
        <vc-date-picker 
          v-model="range" 
          is-range
          :columns="2"
          color="green"
          class="calendar-input-when-tab__datepicker"
          transition="slide-h"
        />
      </div>
    </div>

    <div class="calenar-input-whe-tab__tab-content" v-if="selectedTab == 'flexible'">
      <div class="calendar-input-when-tab__row">
        <div class="calendar-input-when-tab__flexible-days">
          <h2>Learn on only {{ computedFlexibleDays }}</h2>
          <input type="radio" v-model="choosedFlexibleDays" value="weekends" id="flexible-days--weekends" />
          <label for="flexible-days--weekends">Weekends</label>
          <input type="radio" v-model="choosedFlexibleDays" value="during-the-week" id="flexible-days--during-the-week" />
          <label for="flexible-days--during-the-week">During the Week</label>
          <input type="radio" v-model="choosedFlexibleDays" value="any-day" id="flexible-days--any-day" />
          <label for="flexible-days--any-day">Any Day</label>
        </div>
      </div>
      <div class="calendar-input-when-tab__row">
        <div class="calendar-input-when-tab__flexible-months">
          <h3>Learn in <b>{{ computedFlexibleMonths }}</b></h3>
          <button v-for="(month, index) in flexibleMonths" :key="index" @click.prevent="toggleMonth(month)" :class="{
            'calendar-input-when-tab__flexible-month': true,
            'calendar-input-when-tab__flexible-month--active': month.active,
          }">
            <img v-if="month.active" src="/images/calendar-icon-white.svg" />
            <img v-else src="/images/calendar-icon-dark.svg" />
            <span>{{ getMonthName(month.number) }}</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import Vue from 'vue';
import dateHelper from "../../../helpers/dateHelper";

export default {
  name: "CalendarInputWhenTab",
  props: {
    value: Object,
  },
  data() {
    return {
      range: {
        start: null,
        end: null,
      },
      selectedTab: 'calendar',
      choosedFlexibleDays: 'weekends',
      flexibleMonths: [],
    }
  },
  beforeMount() {
    this.range.start = this.value.from;
    this.range.end = this.value.to;
    if(this.value.choosedFlexibleDays) {
      this.choosedFlexibleDays = this.flexibleDaysTitleToName(this.value.choosedFlexibleDays);
    }
    
    const today = (new Date()).getMonth();
    for (let i = 0; i < 6; i++) {
      let month = {
        number: today + i <= 11 ? today + i : (today + i) - 11,
        active: false,
      };
      if (this.value.choosedFlexibleMonths && this.value.choosedFlexibleMonths.length > 0) {
        this.value.choosedFlexibleMonths.forEach((value) => {
          if (month.number == value.number) {
            month.active = value.active;
          }
        });
      } else if (i < 2) {
        month.active = true;
      }
      this.flexibleMonths.push(month);
    }
    
    if (this.value.flexibleMode) {
      this.selectedTab = 'flexible';
    }
  },
  computed: {
    computedFlexibleDays() {
      return this.flexibleDaysTitleToName(this.choosedFlexibleDays);
    },
    computedActiveMonths() {
      return this.flexibleMonths.filter(value => value.active)
    },
    computedFlexibleMonths() {
      let str = "";
      this.computedActiveMonths.forEach((value, index) => {
        str += dateHelper.monthNameByNumber(value.number);
        if (index < this.computedActiveMonths.length - 1) {
          str += ", ";
        }
      });
      return str;
    },
  },
  methods: {
    flexibleDaysTitleToName(title) {
      switch (title) {
        case "weekends":
          return "Weekends";
          break;
        case "during-the-week":
          return "During the Week";
          break;
        case "any-day":
          return "Any Day";
          break;
        case "Weekends":
          return "weekends";
          break;
        case "During the Week":
          return "during-the-week";
          break;
        case "Any Day":
          return "any-day";
          break;
      }
    },
    toggleMonth(month) {
      if (month.active) {
        if (this.computedActiveMonths.length > 1) {
          month.active = !month.active;
        }
      } else {
        month.active = !month.active;
      }
    },
    getMonthName(num) {
      return dateHelper.monthNameByNumber(num);
    },
    clearValue() {
      this.range = {
        from: null,
        to: null,
      };
    },
    emitValueChanged() {
      this.$emit('value-changed', {
        from: this.range.start,
        to: this.range.end,
        flexibleMode: this.selectedTab == 'flexible',
        choosedFlexibleDays: this.computedFlexibleDays,
        choosedFlexibleMonths: this.computedActiveMonths,
      });
    },
  },
  watch: {
    range(newValue) {
      this.emitValueChanged();
    },
    selectedTab(newValue) {
      this.emitValueChanged();
    },
    choosedFlexibleDays(newValue) {
      this.emitValueChanged();
    },
    computedActiveMonths(newValue) {
      this.emitValueChanged();
    }
  },
}
</script>

<style lang="scss" scoped>
@import "./CalendarInputWhenTab.scss";
</style>