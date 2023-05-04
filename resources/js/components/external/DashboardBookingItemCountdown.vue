<template>
  <div class="cd--control-inner">
    <a
      href="#"
      @click.prevent
      :id="'count-down-' + lesson.id"
      class="b-dashboard-cd"
      v-if="!showJoinButton"
    >
      <countdown
        :time="
          getCountDownFrom(
            lesson.start,
            lesson.extra_time_before_start,
            lesson.timezone_id_name
          )
        "
        :emit-events="true"
        @end="handleCountdownEnd(lesson)"
        :transform="transformCountdown"
      >
        <template slot-scope="props">{{
          (props.days != undefined
            ? props.days + (props.hours != undefined ? ", " : "")
            : "") +
          (props.hours != undefined
            ? props.hours + (props.minutes != undefined ? ", " : "")
            : "") +
          (props.minutes != undefined
            ? props.minutes + (props.seconds != undefined ? ", " : "")
            : "") +
          (props.seconds != undefined ? props.seconds : "")
        }}</template>
      </countdown>
    </a>
    <a
      href="#"
      @click.prevent="
        joinLesson(lesson.id, lesson.instructor.full_name, lesson.genre.title)
      "
      class="b-dashboar-join"
      v-if="showJoinButton"
      >Until event</a>
    <div v-else class="cd--control-btn-disabled">Until event</div>
  </div>
</template>

<script>
import skillectiveHelper from "../../mixins/skillectiveHelper.js";
import manageVideoLesson from "../../mixins/manageVideoLesson.js";
import siteAPI from "../../mixins/siteAPI.js";
import moment from "moment";

export default {
  mixins: [siteAPI, skillectiveHelper, manageVideoLesson],
  props: ["lesson"],
  data() {
    return {
      showJoinButton: false,
    };
  },
  methods: {
    getCountDownFrom(datetime, extra_time_before_start, timezoneId) {
      let startDate = moment(datetime, "YYYY-MM-DD HH:mm:ss").toDate();

      startDate.setMinutes(startDate.getMinutes() - extra_time_before_start);

      let nowDate = new Date();
      let diff =
        startDate.getTime() -
        new Date(
          nowDate.toLocaleString("en-us", { timeZone: timezoneId })
        ).getTime();
      if (diff < 0) return 0;

      return diff;
    },
    handleCountdownEnd(lesson) {
      if (document.getElementById("count-down-" + lesson.id) != undefined)
        document.getElementById("count-down-" + lesson.id).remove();
      this.showJoinButton = true;
    },
    transformCountdown(props) {
      if (props["days"] == 0) {
        delete props["days"];
        if (props["hours"] == 0) {
          delete props["hours"];
          if (props["minutes"] == 0) {
            delete props["minutes"];
          } else {
            delete props["seconds"];
          }
        } else {
          //                        delete props['minutes'];
          delete props["seconds"];
        }
      } else {
        //                    delete props['hours'];
        delete props["minutes"];
        delete props["seconds"];
      }

      Object.entries(props).forEach(([key, value]) => {
        // uses singular form when the value is less than 2
        let word = value < 2 ? key.replace(/s$/, "") : key;

        if (word == "day" || word == "days") {
          word = "Days";
        }

        if (word == "hour" || word == "hours") {
          word = "Hours";
        }

        if (word == "minutes" || word == "minute") {
          word = "Minutes";
        }

        props[key] = `${value} ${word}`;
        //                    if (value==0)
        //                        delete props[key];
      });

      return props;
    },
  },
  created() {
    let startDate = moment(this.lesson.start, "YYYY-MM-DD HH:mm:ss").toDate();

    startDate.setMinutes(
      startDate.getMinutes() - this.lesson.extra_time_before_start
    );

    let endDate = moment(this.lesson.end, "YYYY-MM-DD HH:mm:ss").toDate();

    endDate.setMinutes(endDate.getMinutes() + this.lesson.extra_time_after_end);


    let nowDate = new Date();
    let nowTimeInLessonTimezone = new Date(
      nowDate.toLocaleString("en-US", {
        timeZone: this.lesson.timezone_id_name,
      })
    ).getTime();



    this.showJoinButton =
      this.lesson.room_completed != true &&
      startDate.getTime() - nowTimeInLessonTimezone < 0 &&
      endDate.getTime() - nowTimeInLessonTimezone > 0;
  
  },
};
</script>
<style>
.bookings-table .dashboard-bookings-item--content .cd--control-inner{
  flex-direction: column-reverse;
}
.bookings-table .dashboard-bookings-item--content .cd--control-inner .b-dashboard-cd{
  border: 0;
  cursor: auto;
}
.bookings-table .dashboard-bookings-item--content .cd--control-btn{
  background-color: #fff;
  border: 1px solid rgba(10, 171, 21, 0.4);
  border-radius: 3px;
  width: 100%;
  padding: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  text-decoration: none;
  color: #0aab14;
  font-family: Hind Vadodara;
  font-weight: 600;
  font-size: 16px;
  line-height: 24px;
}
.bookings-table .dashboard-bookings-item--content .cd--control-btn-disabled{
  border: 1px solid #999999;
  background-color: #cccccc;
  color: #666666;
  border-radius: 3px;
  width: 100%;
  padding: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: not-allowed;
  text-decoration: none;
  font-family: Hind Vadodara;
  font-weight: 600;
  font-size: 16px;
  line-height: 24px;
}
.bookings-table .dashboard-bookings-item--content .right-side .more-wrap {
  margin-left: 20px;
}
.bookings-table .dashboard-bookings-item--content .cd--control-inner {
  margin-right: 0 !important;
}
</style>