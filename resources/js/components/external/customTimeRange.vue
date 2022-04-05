<template>

    <div class="time-picker-wrapper"  v-click-outside="closeIt">
        <input @click="openIt" type="text" class="form-control" readonly v-model="timeApply" :placeholder="placeholder">
        <input type="hidden" v-model="timeStart" name="time_from">
        <input type="hidden" v-model="timeEnd" name="time_to">
        <div class="time-picker-popup" v-if="showIt">
            <p><strong>Time range</strong>{{inputText}}</p>
            <vue-slider
                    @change="changeTime(timeConvert[0],timeConvert[1])"
                    :tooltip="'none'"
                    :min="0"
                    :max="1440"
                    :min-range="1"
                    :enable-cross="false"
                    v-model="timeConvert"
                    :interval="15"
                    :height="2"
            ></vue-slider>

            <div class="time-picker-footer">
                <a href="#" @click.prevent="clearVal()" class="btn-clear">Clear</a>
                <a href="#" @click.prevent="applyVal()" class="btn-apply">Apply</a>
            </div>
        </div>

    </div>

</template>


<script>
	import _ from 'underscore'
	import $ from 'jquery'
    import ClickOutside from 'vue-click-outside'

	export default {
		name: 'time-range',

		props: ['timeFrom','timeTo','hidePlaceholder'],

        computed: {
            inputText: function () {
                return this.timeConvertFrom + " - " + this.timeConvertTo;
            }
        },
        directives: {
            ClickOutside
        },

		data () {
            return {
                timeApply: '',
                timeConvertFrom: '6:00 AM',
                timeConvertTo: '12:00 PM',
                timeConvert: [360, 720],
                timeStart: '',
                timeEnd: '',
                showIt: false,
                placeholder: 'Choose time range',
                triggerFlag: false,
            }
		},

		mounted () {
		    if(this.timeFrom !== '') {
                this.conver24ToMinutes(this.timeFrom, this.timeTo);
                this.timeApply =  this.timeConvertFrom + " - " + this.timeConvertTo;
                this.timeStart = this.timeFrom;
                this.timeEnd = this.timeTo;
            } else {
                this.conver24ToMinutes('6:00 AM','12:00 PM');
                this.timeApply =  '';
                this.timeStart = this.timeFrom;
                this.timeEnd = this.timeTo;
            }

            if(this.hidePlaceholder) {
                this.placeholder = '';
            }
		},

		methods: {
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

            clearVal: function () {
                this.timeApply = '';
                this.showIt = false;
                this.conver24ToMinutes('7:00','11:00');
                this.$emit('changeTimeModel', ['', '']);
                this.triggerFlag = false;
            },
            closeIt: function () {
               if(this.showIt && this.triggerFlag) {
                   this.showIt = false;
                   this.triggerFlag = false;
               }
            },
            openIt: function () {
                this.showIt = true;
                this.triggerFlag = true;
            },
            applyVal: function () {
                this.timeApply =  this.timeConvertFrom + " - " + this.timeConvertTo;
                this.showIt = false;
                this.timeStart = this.convertMinsToHrsMins(this.timeConvert[0]);
                this.timeEnd = this.convertMinsToHrsMins(this.timeConvert[1]);
                this.$emit('changeTimeModel', [this.timeStart, this.timeEnd]);
                this.triggerFlag = false;
            },

            convertMinsToHrsMins: function (minutes) {
                var h = Math.floor(minutes / 60);
                var m = minutes % 60;
                h = h < 10 ? '0' + h : h;
                m = m < 10 ? '0' + m : m;
                return h + ':' + m;
            }
		}
	}

</script>
