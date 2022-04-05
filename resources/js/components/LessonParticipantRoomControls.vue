<template>
  <div class='cd--control-inner'>
    <a
      href="#"
      @click.prevent
      :id="'count-down-' + booking.id"
      class="btn-countdown btn-approve"
      v-if="!showJoinButton"
    >
      <countdown
        :time="getCountDownFrom(booking.lesson.start, booking.lesson.extra_time_before_start, booking.lesson.timezone_id_name)"
        :emit-events="true"
        @end="handleCountdownEnd(booking)"
        :transform="transformCountdown"
      >
        <template
          slot-scope="props"
        >{{ (props.days!=undefined?props.days+(props.hours!=undefined?', ':''):'') + (props.hours!=undefined?props.hours+(props.minutes!=undefined?', ':''):'') + (props.minutes!=undefined?props.minutes+(props.seconds!=undefined?', ':''):'') + (props.seconds!=undefined?props.seconds:'') }}</template>
      </countdown>
    </a>
    <a
      href="#"
      @click.prevent="joinLesson(booking.lesson.id, booking.lesson.instructor.full_name, booking.lesson.genre.title)"
      class="btn-approve"
      v-if="showJoinButton && !booking.disconnected"
    >Join Lesson</a>
  </div>
</template>

<script>
import skillectiveHelper from "../mixins/skillectiveHelper.js";
import manageVideoLesson from "../mixins/manageVideoLesson.js";
import siteAPI from "../mixins/siteAPI.js";
import moment from 'moment'

export default {
  mixins: [siteAPI, skillectiveHelper, manageVideoLesson],
  props: ["booking"],
  data() {
    return {
      showJoinButton: false
    };
  },
  methods: {
    getCountDownFrom(datetime, extra_time_before_start, timezoneId) {
      let startDate = moment(datetime, 'YYYY-MM-DD HH:mm:ss').toDate();

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
    handleCountdownEnd(booking) {
      if (document.getElementById("count-down-" + booking.id) != undefined)
        document.getElementById("count-down-" + booking.id).remove();
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
          word = "d";
        }

        if (word == "hour" || word == "hours") {
          word = "h";
        }

        if (word == "minutes" || word == "minute") {
          word = "m";
        }

        props[key] = `${value} ${word}`;
        //                    if (value==0)
        //                        delete props[key];
      });

      return props;
    }
  },
  created() {
    let startDate = moment(this.booking.lesson.start, 'YYYY-MM-DD HH:mm:ss').toDate();

    startDate.setMinutes(
      startDate.getMinutes() - this.booking.lesson.extra_time_before_start
    );

    let endDate = moment(this.booking.lesson.end, 'YYYY-MM-DD HH:mm:ss').toDate();

    endDate.setMinutes(
      endDate.getMinutes() + this.booking.lesson.extra_time_after_end
    );

    let nowDate = new Date();
    let nowTimeInLessonTimezone = new Date(
      nowDate.toLocaleString("en-US", {
        timeZone: this.booking.lesson.timezone_id_name
      })
    ).getTime();

    this.showJoinButton =
      this.booking.lesson.room_completed != true &&
      startDate.getTime() - nowTimeInLessonTimezone < 0 &&
      endDate.getTime() - nowTimeInLessonTimezone > 0;
  }
};
</script>