<template>
    <div class="time-range">
      <vue-slider
        @change="changeTime(timeConvert[0], timeConvert[1])"
        :tooltip="'none'"
        :min="0"
        :max="1440"
        :min-range="1"
        :enable-cross="false"
        v-model="timeConvert"
        :interval="15"
        :height="2"
        :dotSize="16"
        :duration="0.25"
      ></vue-slider>
    </div>
</template>

<script>
export default {
    name: "TimeRange",
    props: {
        value: {
            type: Object,
            default: () => {
                return null;
            },
        },
    },
    beforeMount () {
        this.conver24ToMinutes(this.value.from, this.value.to);
    },
    data() {
        return {
            timeRange: this.value,
            timeConvert: [ 360, 720 ],
            timeConvertFrom: '6:00 AM',
            timeConvertTo: '12:00 PM',
        }
    },
    watch: {
        timeRange(newValue) {
            this.$emit("input", newValue);
        },
        timeConvertFrom() {
            this.emitValue();
        },
        timeConvertTo() {
            this.emitValue();
        },
    },
    methods: {
        emitValue() {
            this.timeRange = {
                from: this.convertMinsToHrsMins(this.timeConvert[0]),
                to: this.convertMinsToHrsMins(this.timeConvert[1]),
                text: this.timeConvertFrom + " - " + this.timeConvertTo,
            };
        },
        conver24ToMinutes(firstTime, secondTime) {
            var setTimeArryStart = firstTime.split(':');
            var setTimeArryEnd = secondTime.split(':');
            firstTime.split(':');
            var firstDate = new Date();
            firstDate.setHours(parseInt(setTimeArryStart[0]), parseInt(setTimeArryStart[1]), 0, 0);
            var secondDate = new Date();
            secondDate.setHours(0, 0, 0, 0);
            var diff = Math.abs(firstDate - secondDate);
            var minutes = Math.floor((diff/1000)/60);
            var firstDate2 = new Date();
            firstDate2.setHours(parseInt(setTimeArryEnd[0]), parseInt(setTimeArryEnd[1]), 0, 0);
            var secondDate2 = new Date();
            secondDate2.setHours(0, 0, 0, 0);
            var diff2 = Math.abs(firstDate2 - secondDate2);
            var minutes2 = Math.floor((diff2/1000)/60);
            this.timeConvert[0] = minutes;
            this.timeConvert[1] = minutes2;
            this.changeTime(this.timeConvert[0], this.timeConvert[1])
        },
        changeTime(timeStart, timeEnd) {
            var hours1 = Math.floor(this.timeConvert[0] / 60);
            var minutes1 = timeStart - (hours1 * 60);

            if (hours1.length == 1) hours1 = '0' + hours1;
            if (minutes1.length == 1) minutes1 = '0' + minutes1;
            if (minutes1 == 0) minutes1 = '00';
            if (hours1 >= 12) {
                if (hours1 == 12) {
                    hours1 = hours1;
                    minutes1 = minutes1 + " PM";
                } else {
                    hours1 = hours1 - 12;
                    minutes1 = minutes1 + " PM";
                }
            } else {
                hours1 = hours1;
                minutes1 = minutes1 + " AM";
            }
            if (hours1 == 0) {
                hours1 = 12;
                minutes1 = minutes1;
            }
            this.timeConvertFrom = hours1 + ':' + minutes1;
            var hours2 = Math.floor(this.timeConvert[1] / 60);
            var minutes2 = timeEnd - (hours2 * 60);

            if (hours2.length == 1) hours2 = '0' + hours2;
            if (minutes2.length == 1) minutes2 = '0' + minutes2;
            if (minutes2 == 0) minutes2 = '00';
            if (hours2 >= 12) {
                if (hours2 == 12) {
                    hours2 = hours2;
                    minutes2 = minutes2 + " PM";
                } else if (hours2 == 24) {
                    hours2 = 11;
                    minutes2 = "59 PM";
                } else {
                    hours2 = hours2 - 12;
                    minutes2 = minutes2 + " PM";
                }
            } else {
                hours2 = hours2;
                minutes2 = minutes2 + " AM";
            }

            this.timeConvertTo = hours2 + ':' + minutes2;
        },
        convertMinsToHrsMins: function (minutes) {
            var h = Math.floor(minutes / 60);
            var m = minutes % 60;
            h = h < 10 ? '0' + h : h;
            m = m < 10 ? '0' + m : m;
            return h + ':' + m;
        },
    },
};
</script>

<style lang="scss" scoped>
@import "./TimeRange.scss";
</style>
