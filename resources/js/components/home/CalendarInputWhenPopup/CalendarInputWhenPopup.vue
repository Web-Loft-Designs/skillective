<template>
  <div v-if="show" class="caldendar-input-when-popup">
    <div class="caldendar-input-when-popup__inner">
      <calendar-input-when-tab 
        @value-changed="whenTabValueChanged($event)"
        ref="calendarInputWhenTab"
        :value="whenValue"
      />
    </div>
  </div>
</template>

<script>
import CalendarInputWhenTab from "../CalendarInputWhenTab/CalendarInputWhenTab.vue";
import dateHelper from "../../../helpers/dateHelper";

export default {
  name: "CalendarInputWhenPopup",
  components: {
    CalendarInputWhenTab,
  },
  data() {
    return {
      show: false,
      whenValue: {
        from: null,
        to: null,
        flexibleMode: false,
        choosedFlexibleDays: null,
        choosedFlexibleMonths: null,
      },
      whenText: "Do you want to learn?",
    }
  },
  methods: {
    whenTabValueChanged(newValue) {
      this.whenValue = newValue;
    },
    setShow(show) {
      this.show = show;
    },
  },
  watch: {
    whenValue: {
      handler(newValue) {
        if (newValue.flexibleMode) {

          this.whenText = newValue.choosedFlexibleDays + ", ";
          newValue.choosedFlexibleMonths.forEach((value, index) => {
            if (value.active) {
              this.whenText += dateHelper.monthNameByNumber(value.number);
              if (index < newValue.choosedFlexibleMonths.length - 1) {
                this.whenText += ", ";
              }
            }
          });

        } else {

          if (!newValue.from) {
            this.whenText = "Do you want to learn?";
          } else {
            this.whenText = dateHelper.threeLetMonthNameByNumber(newValue.from.getMonth()) +
              " " + newValue.from.getDate() +  " - " + 
              dateHelper.threeLetMonthNameByNumber(newValue.to.getMonth()) + " " + newValue.to.getDate();
          }

        }

        let flexMonths = "";
        newValue.choosedFlexibleMonths.map((month, index) => {
          flexMonths += (month.number + 1);
          if (index < newValue.choosedFlexibleMonths.length - 1) {
             flexMonths += ",";
          }
        });

        this.$emit('value-changed', {
          from: newValue.from ? dateHelper.dateToFilter(newValue.from) : null,
          to: newValue.to ? dateHelper.dateToFilter(newValue.to) : null,
          choosedFlexibleDays: newValue.flexibleMode ? newValue.choosedFlexibleDays : null,
          choosedFlexibleMonths: newValue.flexibleMode ? flexMonths : null,
          whenText: this.whenText,
          flexibleMode: newValue.flexibleMode,
        });
      },
      deep: true,
    },
  },
}
</script>

<style lang="scss" scoped>
@import "./CalendarInputWhenPopup.scss";
</style>