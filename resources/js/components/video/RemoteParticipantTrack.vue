<template>
  <div
    class="col-md-3 remote-participant-track"
    :id="'participant-' + participant.sid + '-container'"
  >
    <div class="participant-name" v-html="getIdentityName(participant.identity)"></div>
    <div class="remote-track" :id="'participant-' + participant.sid + '-track'"></div>
    <div class="small-participant-track-controls">
      <button @click="toggleMute($event)">
        <simple-svg :src="mutedIcon" />
      </button>
      <button @click="toggleExpandCollapse($event)">
          <simple-svg :src="expandIcon" />
      </button>
      <button v-if="roomSettings.meInstructor==true" @click="disconnectParticipantFromRoom()">
        <img src="/images/remove-user.svg" alt />
      </button>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../Event";
import siteAPI from "../../mixins/siteAPI.js";
import { SimpleSVG } from "vue-simple-svg";

export default {
  mixins: [siteAPI],
  data() {
    return {
      participant: null,
      participantIdentity: ""
    };
  },
  props: [
    "participantData",
    "roomSettings",
    "mutedParticipants",
    "isParticipantExpanded"
  ],
  created() {
    this.participant = this.participantData;
  },
  components: { "simple-svg": SimpleSVG },
  computed: {
    mutedIcon: function() {
      const isIamExistInMuted = this.mutedParticipants.find(
        item => item == this.participant.sid
      );

      if (isIamExistInMuted) {
        return '/images/user-unmute.svg';
      } else {
        return '/images/user-mute.svg';
      }
    },
    isExpanded: function() {
        if(this.isParticipantExpanded){
            return true
        }else{
            return false
        }
    },
    expandIcon: function(){
        if(this.isExpanded){
            return '/images/user-unexpand.svg'
        }else{
            return '/images/user-expand.svg'
        }
    }
  },
  methods: {
    toggleExpandCollapse(clickEvent) {
      EventBus.$emit(
        "toggleExpandCollapseRemoteParticipant",
        this.participant,
        clickEvent
      );
    },
    disconnectParticipantFromRoom() {
      EventBus.$emit("disconnectParticipantFromRoom", this.participant);
    },
    toggleMute(clickEvent) {
      EventBus.$emit(
        "toggleMuteRemoteParticipant",
        this.participant,
        clickEvent
      );
    },
    getIdentityName(identity) {
      return identity.substr(identity.indexOf(":") + 1);
    }
  }
};
</script>