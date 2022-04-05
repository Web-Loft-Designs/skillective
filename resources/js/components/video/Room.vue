<template>
  <div id="online-conference-container">
    <noscript>
      <strong
        >We're sorry but video-chat doesn't work properly without JavaScript
        enabled. Please enable it to continue.</strong
      >
    </noscript>

    <div v-if="errorText" class="err-message">{{ errorText }}</div>

    <div v-if="selectedDevices == null">
      <SelectMediaDevices></SelectMediaDevices>
    </div>

    <div
      v-bind:class="{
        'expanded-mode': expandedParticipant != null,
        usersExpandedWrapper: isUsersExpanded,
      }"
      v-if="roomSettings != null"
    >
      <div class="col-md-12 room-outer-wrapper">
        <div class="room-wrapper">
          <div class="roomTitle">
            <span v-if="loading">Loading... {{ roomName }}</span>
            <span class="loaded-room-header" v-else-if="!loading && roomName">
              <div class="loaded-room-header--left">
                <span class="room-header--contected"
                  >Connected to {{ roomName }}</span
                >
                <!--<span class="lesson-duration">DURATION: {{ lessonDurationTimeString }}</span>-->
                <span>{{ lessonTimeLeftString }} left</span>
                <span
                  class="participants-joined"
                  v-if="activeRoom != null && roomSettings.meInstructor"
                  >Count participants joined {{ countParticipants }}</span
                >
                <div
                  class="instructor-name-title"
                  v-if="
                    instructorParticipantHere == true && !expandedParticipant
                  "
                >
                  {{ roomSettings.instructorParticipantIdentity }}
                </div>
                <div
                  class="instructor-name-title"
                  v-if="expandedParticipant != null"
                >
                  {{ expandedParticipantIdentity }}
                </div>
              </div>
              <div class="loaded-room-header--right">
                <button
                  class="finish-lesson"
                  @click="closeRoom()"
                  v-if="roomSettings.meInstructor == true"
                >
                  Finish Lesson
                </button>
              </div>
            </span>
            <span v-else>Room wasn't selected</span>
          </div>

          <div class="videos--wrapper">
            <div class="overlay-background">
              <div class="overlay-inner"></div>
            </div>

            <div
              class="row"
              id="expanded-track-container"
              v-if="expandedParticipant != null"
            >
              <div class="participant-name">
                {{ expandedParticipantIdentity }}
              </div>
              <div
                :class="{ fullScreen: isFullScreen }"
                id="expanded-video-container"
              ></div>

              <div class="participant-track-controls">
                <button
                  @click="toggleExpandCollapse(expandedParticipant, $event)"
                >
                  <simple-svg src="/images/user-unexpand.svg" />
                  <span>Collapse</span>
                </button>
              </div>
            </div>

            <div class="row" id="instructor-track-container">
              <div
                id="waiting-for-instructor"
                v-if="
                  !loading && roomName && instructorParticipantHere == false
                "
              >
                Waiting for lesson instructor
              </div>

              <div
                class="participant-name"
                v-if="instructorParticipantHere == true"
              >
                {{ roomSettings.instructorParticipantIdentity }}
              </div>
              <div
                v-bind:class="{
                  fullScreen: isFullScreen,
                  videoMirrored: isMirrored,
                }"
                id="instructorTrack"
              ></div>
              <div id="screenpreview-container"></div>

              <!-- PARTICIPANT CONTROL PANNEL START HERE -->

              <div
                v-if="roomSettings.meInstructor == false && localTrack == true"
                class="participant-track-controls"
              >
                <button @click="leaveRoom()">
                  <simple-svg src="/images/leave-room.svg" />
                  <span>Leave room</span>
                </button>
                <button
                  v-bind:class="{ active: isLocalVideoStopped }"
                  @click="toggleStopLocalVideoStream($event)"
                  v-if="localScreenTrack == null"
                >
                  <span
                    v-text="
                      isLocalVideoStopped
                        ? 'Start video stream'
                        : 'Stop video stream'
                    "
                  ></span>
                  <simple-svg
                    v-if="!isLocalVideoStopped"
                    src="/images/user-unexpand.svg"
                  />
                  <simple-svg
                    v-if="isLocalVideoStopped"
                    src="/images/user-expand.svg"
                  />
                </button>
                <button
                  v-bind:class="{ active: isMuted }"
                  @click="toggleMute(null, $event, null)"
                >
                  <span v-text="isMuted ? 'Unmute' : 'Mute'"></span>
                  <simple-svg src="/images/unmute.svg" />
                </button>
                <button
                  @click="toggleScreensharing()"
                  id="capturescreen"
                  v-bind:class="{ active: localScreenTrack ? true : false }"
                >
                  <span
                    v-text="
                      localScreenTrack ? 'Stop screen sharing' : 'Share screen'
                    "
                  ></span>
                  <simple-svg src="/images/share-screen.svg" />
                </button>
                <button
                  v-bind:class="{ active: isUsersExpanded }"
                  @click="toggleExpandUsersWrap()"
                >
                  <span
                    v-text="isUsersExpanded ? 'Hide users' : 'Show users'"
                  ></span>
                  <simple-svg src="/images/users.svg" alt />
                </button>
                <button
                  v-bind:class="{ active: chatIsOpened }"
                  @click="openChat()"
                >
                  <span
                    v-if="Number(countUnreadMessages) > 0"
                    class="messages-counter"
                    >{{ countUnreadMessages }}</span
                  >
                  <span
                    v-text="chatIsOpened ? 'Close chat' : 'Open chat'"
                  ></span>
                  <simple-svg src="/images/messages.svg" />
                </button>
              </div>

              <!-- PARTICIPANT CONTROL PANNEL END HERE -->

              <!-- INSTRUCTOR CONTROL PANNEL START HERE -->

              <div
                class="participant-track-controls"
                id="instructor-track-controls"
                v-if="roomSettings.meInstructor == true && localTrack == true"
              >
                <button @click="leaveRoom()">
                  <simple-svg src="/images/leave-room.svg" />
                  <span>Leave room</span>
                </button>
                <button
                  v-bind:class="{ active: isLocalVideoStopped }"
                  @click="toggleStopLocalVideoStream($event)"
                  v-if="localScreenTrack == null"
                >
                  <span
                    v-text="
                      isLocalVideoStopped
                        ? 'Start video stream'
                        : 'Stop video stream'
                    "
                  ></span>
                  <simple-svg
                    v-if="!isLocalVideoStopped"
                    src="/images/user-unexpand.svg"
                  />
                  <simple-svg
                    v-if="isLocalVideoStopped"
                    src="/images/user-expand.svg"
                  />
                </button>
                <button
                  v-bind:class="{ active: isMuted }"
                  @click="toggleMute(null, $event, null)"
                >
                  <span v-text="isMuted ? 'Unmute' : 'Mute'"></span>
                  <simple-svg src="/images/unmute.svg" />
                </button>
                <button
                  v-bind:class="{ active: allMuted }"
                  @click="toggleMuteAllRemote()"
                >
                  <span v-text="allMuted ? 'Unmute all' : 'Mute all'"></span>
                  <simple-svg src="/images/unmute.svg" />
                </button>
                <button
                  @click="toggleScreensharing()"
                  id="capturescreen"
                  v-bind:class="{ active: localScreenTrack ? true : false }"
                >
                  <span
                    v-text="
                      localScreenTrack ? 'Stop screen sharing' : 'Share screen'
                    "
                  ></span>
                  <simple-svg src="/images/share-screen.svg" />
                </button>
                <button
                  v-bind:class="{ active: isUsersExpanded }"
                  @click="toggleExpandUsersWrap()"
                >
                  <span
                    v-text="isUsersExpanded ? 'Hide users' : 'Show users'"
                  ></span>
                  <simple-svg src="/images/users.svg" alt />
                </button>
                <button
                  v-bind:class="{ active: isMirrored }"
                  @click="toggleCameraMirror()"
                >
                  <span
                    v-text="isMirrored ? 'Mirror camera' : 'Mirror camera'"
                  ></span>
                  <simple-svg src="/images/flip-icon.svg" alt />
                </button>
                <button
                  :class="{
                    'message-button-outer': true,
                    active: chatIsOpened,
                  }"
                  @click="openChat()"
                >
                  <span
                    v-if="Number(countUnreadMessages) > 0"
                    class="messages-counter"
                    >{{ countUnreadMessages }}</span
                  >
                  <span
                    v-text="chatIsOpened ? 'Close chat' : 'Open chat'"
                  ></span>
                  <simple-svg src="/images/messages.svg" />
                </button>
                <button
                  @click="toggleFullScreen()"
                  :class="{ active: isFullScreen }"
                >
                  <span
                    v-text="
                      isFullScreen ? 'Exit fullscreen' : 'Enable fullscreen'
                    "
                  ></span>
                  <simple-svg
                    width="30"
                    height="30"
                    src="/images/fullscreen.svg"
                  />
                </button>
                <!--<button @click="toggleRecording()">Start Recording</button> &lt;!&ndash; Start / Stop recording &ndash;&gt;-->
              </div>

              <!-- INSTRUCTOR CONTROL PANNEL END HERE -->
            </div>
          </div>
          <div
            v-bind:class="{
              'row remote_video_container': true,
              'is-student': !roomSettings.meInstructor,
            }"
            id="participantsTracks"
          >
            <div
              class="col-md-3 participantsTracks-inner"
              v-if="roomSettings.meInstructor == false && localTrack == true"
            >
              <!-- if not instructor show my local track here / don't show for instructors -->
              <div
                class="participant-name"
                v-html="getIdentityName(activeRoom.localParticipant.identity)"
              ></div>
              <div id="localTrack"></div>
            </div>

            <!-- other participants except lesson instructor -->
            <!--<div id="remote-participants-container"></div>-->
          </div>
        </div>
        <div
          id="roomChat"
          :class="{ 'sidebar-chat': true, 'chat-opened': chatIsOpened }"
          v-if="activeRoom != null"
        >
          <div class="chat-inner--wrapper">
            <div @click="openChat()" class="close-wrap">
              <img src="/images/close-chat.svg" alt />
            </div>
            <div class="row" id="chat-messages-container">
              <ul>
                <li v-for="message in chatMessages">
                  <div class="message-content">
                    <span class="chat-username">
                      <span>{{ getIdentityName(message.sender) }}</span>
                      <time>{{ message.receivedAt }}</time>
                    </span>
                    <span v-html="message.text" class="message"></span>
                  </div>
                  <img
                    width="40px"
                    :src="getParticipantImage(message.sender)"
                  />
                </li>
              </ul>
            </div>
            <div class="chat-controls">
              <div class="col-md-12">
                <label>Message</label>
              </div>
              <div class="col-md-12">
                <textarea
                  class="message-area"
                  v-html="currentMessage"
                  @input="textAreaHandler"
                ></textarea>
              </div>
              <div class="col-md-12">
                <button @click="sendTextMessage">Send Message</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3" v-if="activeRoom != null">
        <!--<div class="row">-->
        <!--<div class="col-md-12">-->
        <!--<Logs />-->
        <!--</div>-->
        <!--</div>-->
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../Event";
import TwilioVideo, {
  connect,
  createLocalTracks,
  createLocalVideoTrack,
  LocalDataTrack,
} from "twilio-video";
import axios from "axios";
import siteAPI from "../../mixins/siteAPI.js";
import twilioVideoHelper from "./mixins/twilioVideoHelper.js";
import { SimpleSVG } from "vue-simple-svg";

export default {
  mixins: [siteAPI, twilioVideoHelper],
  data() {
    return {
      roomSettings: null,
      loading: false,
      data: {},
      activeRoom: null,
      roomName: null,
      instructorParticipantHere: false,
      lessonDuration: 0,
      lessonTimeLeft: 0,
      lessonDurationIntervalId: null,
      lessonTimeLeftIntervalId: null,
      dataTrack: null,
      dataTrackPublished: {},
      currentMessage: "",
      chatMessages: [],
      countUnreadMessages: 0,
      chatIsOpened: false,
      countParticipants: 1,
      selectedDevices: null,
      isUsersExpanded: false,
      isMirrored: false,
      isFullScreen: false,
    };
  },
  props: ["lessonId"],
  components: { "simple-svg": SimpleSVG },
  computed: {
    isMuted: function () {
      const isIamExistInMuted = this.mutedParticipants.find(
        (item) => item == this.activeRoom.localParticipant.sid
      );

      if (isIamExistInMuted) {
        return true;
      } else {
        return false;
      }
    },
    lessonDurationTimeString: function () {
      if (this.lessonDuration) {
        return new Date(1000 * this.lessonDuration).toISOString().substr(11, 8);
      } else {
        return 0;
      }
    },
    lessonTimeLeftString: function () {
      if (this.lessonTimeLeft) {
        return new Date(1000 * this.lessonTimeLeft).toISOString().substr(11, 8);
      } else {
        return 0;
      }
    },
  },
  created() {
    EventBus.$on("deviceSelected", (selectedDevices) => {
      EventBus.$off("deviceSelected");
      this.selectedDevices = selectedDevices;
      this.getRoomConnectOptions();
    });
    EventBus.$on("toggleMuteRemoteParticipant", (participant, clickEvent) => {
      this.toggleMute(participant, clickEvent, null);
    });
    EventBus.$on(
      "toggleExpandCollapseRemoteParticipant",
      (participant, clickEvent) => {
        this.toggleExpandCollapse(participant, clickEvent);
      }
    );
    EventBus.$on("disconnectParticipantFromRoom", (participant) => {
      let really = confirm(
        "Do you really want to remove this client from lesson?"
      );
      if (really) {
        this.apiPreSend();
        let i = _.findIndex(
          this.roomSettings.participantsDetails,
          function (student) {
            return participant.identity == student.identity;
          }
        );
        if (this.roomSettings.participantsDetails[i] != undefined) {
          axios
            .delete(
              "/api/instructor/virtual-lessons/" +
                this.roomSettings.lessonId +
                "/room/participant/" +
                this.roomSettings.participantsDetails[i].id
            )
            .then((response) => {
              this.apiHandleResponse(response);
            })
            .catch((error) => this.apiHandleError(error));
        }
      }
    });

    // When a user is about to transition away from this page,
    // disconnect from the room, if joined.
    window.addEventListener("beforeunload", this.disconnectRoom);
  },
  mounted() {},
  methods: {
    // Generate access token
    //            async getAccessToken() {
    //                let queryParams = {};
    //                queryParams.identity = this.username;
    //                return await axios.get('/api/virtual-lessons/twilio-access-token', {params : queryParams});
    //            },
    toggleFullScreen() {
      this.isFullScreen = !this.isFullScreen;
    },
    textAreaHandler(e) {
      this.currentMessage = e.target.value.replace(/\n/g, "<br>");
    },
    async getRoomConnectOptions() {
      this.apiPreSend();
      await axios
        .get("/api/virtual-lessons/" + this.lessonId + "/room")
        .then((response) => {
          this.apiHandleResponse(response);
          this.roomSettings = response.data;
          this.showRoom();
        })
        .catch((error) => this.apiHandleError(error));
    },

    // Leave Room. (for local participant)
    disconnectRoom() {
      //                this.dispatchLog('DISCONNECTING  Room: ');
      //                console.log('disconnectRoom');
      if (this.activeRoom) {
        this.activeRoom.disconnect();
      }
    },

    async showRoom() {
      //                var nowDate     = new Date();
      //                var startDate   = new Date(this.roomSettings.room.date_created);
      //                var endDate     = new Date(this.roomSettings.room.lessonEnd);

      //                let startDate = new Date(this.booking.lesson.start);
      //                startDate.setMinutes( startDate.getMinutes() - this.booking.lesson.extra_time_before_start );

      //                console.log(this.roomSettings.room.lessonEnd);
      //                console.log(this.roomSettings.room.extra_time_after_end);
      //                console.log(this.roomSettings.room.timezone_id_name);

      let endDate = moment(
        this.roomSettings.room.lessonEnd,
        "YYYY-MM-DD HH:mm:ss"
      ).toDate();

      endDate.setMinutes(
        endDate.getMinutes() + this.roomSettings.room.extra_time_after_end
      );

      let nowDate = new Date();
      let nowTimeInLessonTimezone = new Date(
        nowDate.toLocaleString("en-US", {
          timeZone: this.roomSettings.room.timezone_id_name,
        })
      ).getTime();

      //                console.log(nowDate.toLocaleString('en-US'));
      //                console.log(endDate.toLocaleString('en-US'));

      //                this.lessonDuration = (nowDate.getTime() - startDate.getTime()) / 1000;
      //                this.lessonDurationIntervalId = setInterval(() => {
      //                    this.lessonDuration++;
      //                },1000);

      this.lessonTimeLeft =
        (endDate.getTime() - nowTimeInLessonTimezone) / 1000;
      this.lessonTimeLeftIntervalId = setInterval(() => {
        this.lessonTimeLeft--;
        if (this.lessonTimeLeft <= 0) {
          clearInterval(VueThis.lessonTimeLeftIntervalId);
          if (this.roomSettings.meInstructor) {
            this.closeRoom();
          } else {
            this.leaveRoom();
          }
        }
      }, 1000);

      this.loading = true;
      let VueThis = this;

      // before a user enters a new room,
      // disconnect the user from they joined already
      //                this.leaveRoomIfJoined();

      // remove any remote track when joining a new room
      let remoteParticipantTrack = document.getElementsByClassName(
        "remote-participant-track"
      );
      while (remoteParticipantTrack.length > 0) {
        remoteParticipantTrack[0].parentNode.removeChild(
          remoteParticipantTrack[0]
        );
      }

      const token = this.roomSettings.token;
      this.dataTrack = new TwilioVideo.LocalDataTrack();

      this.localTracks = await createLocalTracks({
        video: {
          deviceId: { exact: this.selectedDevices.video },
          width: 1920,
          height: 1280,
        },
        audio: { deviceId: { exact: this.selectedDevices.audio } },
      });
      this.localTracks.forEach((track) => {
        if (track.kind == "video") this.localVideoTrack = track;
      });
      this.localTracks.push(this.dataTrack);

      let roomConnectOptions = {
        name: this.roomSettings.roomName,
        logLevel: "warn", //TODO: debug
        audio: true,
        dominantSpeaker: true,
      };

      let participantConnectOptions = _.extend(
        _.cloneDeep(roomConnectOptions),
        {
          video: { width: 400 },
          dominantSpeaker: false,
        }
      );

      let dominantParticipantConnectOptions = _.extend(
        _.cloneDeep(roomConnectOptions),
        {
          video: { height: 720, frameRate: 24, width: 1280 },
          bandwidthProfile: {
            video: {
              mode: "presentation",
              //                            maxTracks: 10,
              dominantSpeakerPriority: "standard",
              renderDimensions: {
                high: { height: 1080, width: 1980 },
                standard: { height: 720, width: 1280 },
                low: { height: 176, width: 144 },
              },
            },
          },
          maxAudioBitrate: 16000, //For music remove this line
          preferredVideoCodecs: [{ codec: "VP8", simulcast: true }],
          networkQuality: { local: 1, remote: 1 },
        }
      );

      let myConnectOptions = this.roomSettings.meInstructor
        ? dominantParticipantConnectOptions
        : participantConnectOptions;
      myConnectOptions.tracks = this.localTracks;

      console.log(this.localTracks);

      // if (this.selectedDevices != null) {
      //   myConnectOptions.audio = {
      //     deviceId: { exact: this.selectedDevices.audio }
      //   };
      //   myConnectOptions.video = {
      //     deviceId: { exact: this.selectedDevices.video }
      //   };
      // }

      TwilioVideo.connect(token, {
        ...myConnectOptions,
        video: { width: 1920, height: 1080 },
      }).then(
        function (room) {
          VueThis.dispatchLog(
            "Successfully joined a Room: " + roomConnectOptions.name
          );
          // set active toom
          VueThis.activeRoom = room;
          //                    console.log('activeRoom set up');
          //                    console.log(room.localParticipant.identity);
          VueThis.roomName = VueThis.roomSettings.roomName;
          VueThis.loading = false;
          VueThis.localTrack = true;

          VueThis.dataTrackPublished.promise = new Promise(
            (resolve, reject) => {
              VueThis.dataTrackPublished.resolve = resolve;
              VueThis.dataTrackPublished.reject = reject;
            }
          );

          setTimeout(() => {
            //                        console.log('try attach local tracks', VueThis.localTrack, VueThis.roomSettings.meInstructor);

            let localMediaContainer = null;
            if (VueThis.roomSettings.meInstructor) {
              //                            console.log('localMediaContainer should be instructorTrack');
              localMediaContainer = document.getElementById("instructorTrack");

              let overlay = document.querySelector(".overlay-background");
              VueThis.attachTracks(VueThis.localTracks, overlay);

              VueThis.instructorParticipantHere = true;
            } else {
              //                            console.log('localMediaContainer should be localTrack');
              localMediaContainer = document.getElementById("localTrack");
            }

            //                        console.log( document.getElementById('localTrack') );

            //                        console.log(localMediaContainer.getAttribute('id'));
            //                        console.log('attach local tracks');
            VueThis.attachTracks(VueThis.localTracks, localMediaContainer);

            room.localParticipant.on("trackAdded", (track) => {
              //                            console.log(`EVT room.localParticipant.trackAdded Participant "${VueThis.activeRoom.localParticipant.identity}" added ${track.kind} Track ${track.sid}`);
              if (track.kind === "data") {
                track.on("message", (data) => {
                  //                                    console.log('LOCAL MESSAGE SENT', data);
                });
              }
            });

            // Attach the Tracks of all the remote Participants.
            room.participants.forEach(function (participant) {
              VueThis.participantConnected(participant, VueThis);
            });

            // When a Participant adds a Track, attach it to the DOM.
            room.on("trackAdded", function (track, participant) {
              VueThis.dispatchLog(
                participant.identity + " added track: " + track.kind
              );
              //                        console.log('EVT trackAdded ' + participant.identity);
            });

            // When a Participant removes a Track, detach it from the DOM.
            room.on("trackRemoved", function (track, participant) {
              //                        VueThis.dispatchLog(participant.identity + " removed track: " + track.kind);
              //                            console.log(participant.identity + " removed track: " + track.kind);
              VueThis.detachTracks([track]);
            });

            // When a Participant joins the Room, log the event.
            room.on("participantConnected", function (participant) {
              //                            console.log('EVT participantConnected ' + participant.identity);
              VueThis.participantConnected(participant, VueThis);
            });

            // When a Participant leaves the Room, detach its Tracks.
            room.on("participantDisconnected", function (participant) {
              //                        VueThis.dispatchLog("Participant '" + participant.identity + "' left the room");
              //                            console.log("EVT participantDisconnected Participant '" + participant.identity + "' left the room");
              VueThis.countParticipants--;
              VueThis.detachParticipantTracks(participant);
            });

            // Once the LocalParticipant leaves the room, detach the Tracks of all Participants, including that of the LocalParticipant.
            room.on("disconnected", function () {
              //                            console.log('room disconnected');
              VueThis.detachParticipantTracks(
                VueThis.activeRoom.localParticipant
              );
              VueThis.activeRoom.participants.forEach(function (participant) {
                VueThis.detachParticipantTracks(participant);
              });

              // show appropriate "room complete" message
              VueThis.activeRoom = null;
              VueThis.roomName = null;
              //                        clearInterval(VueThis.lessonDurationIntervalId);
              clearInterval(VueThis.lessonTimeLeftIntervalId);
            });

            room.on("participantReconnecting", function (participant) {
              //                            console.log('!!! participantReconnecting ' + participant.identity);
              //                        VueThis.updateParticipantState(participant.state);
            });

            room.on("participantReconnected", function (participant) {
              //                            console.log('!!! participantReconnected ' + participant.identity);
              //                        VueThis.updateParticipantState(participant.state);
            });

            room.localParticipant.on("reconnecting", () => {
              //                            console.log('!!! localParticipant reconnecting ' + room.localParticipant.identity);
              //                        VueThis.updateParticipantState(room.localParticipant.state);
            });

            room.localParticipant.on("reconnected", () => {
              //                            console.log('!!! localParticipant reconnected ' + room.localParticipant.identity);
              //                        VueThis.updateParticipantState(room.localParticipant.state);
            });

            room.localParticipant.on("trackPublished", (publication) => {
              console.log("1 trackPublished");
              console.log(VueThis, "+++++++++dataTrack");
              if (publication.track === VueThis.dataTrack) {
                console.log("truee");
                VueThis.dataTrackPublished.resolve();
              }
            });

            room.localParticipant.on(
              "trackPublicationFailed",
              (error, track) => {
                console.log("1 trackPublicationFailed");
                if (track === this.dataTrack) {
                  this.dataTrackPublished.reject(error);
                }
              }
            );
          }, 100);
        },
        function (error) {
          alert("Unable to connect to room");
        }
      );
    },

    // A new RemoteParticipant joined the Room
    participantConnected(participant, VueThis) {
      VueThis.countParticipants++;
      //                console.log('call participantConnected');
      
      
      console.log("PEER HERE");

      if (VueThis.participantIsInstructor(participant)) {
        VueThis.instructorParticipantHere = true;

        console.log("INSTRUCTOR HERE");
      }
      let participantContainer = VueThis.getParticipantContainer(participant);
      //                setTimeout(() => {
      participant.tracks.forEach((publication) => {
        VueThis.trackPublished(publication, participantContainer);
      });
      participant.on("trackPublished", (publication) => {
        VueThis.trackPublished(publication, participantContainer);
      });
      participant.on("trackUnpublished", (publication) => {
        //                        console.log('EVT trackUnpublished');
        VueThis.trackUnpublished(publication, participantContainer);
      });
      participant.on("trackDisabled", (track) => {
        //                        console.log('EVT trackDisabled');
        //                    VueThis.onTrackDisabled(track, participantContainer);
      });
      participant.on("trackEnabled", (track) => {
        //                        console.log('EVT trackEnabled');
        //                    VueThis.onTrackEnabled(track, participantContainer);
      });
      participant.on("trackSubscribed", (track) => {
        //                        console.log(`trackSubscribed Participantsss "${participant.identity}" added ${track.kind} Track ${track.sid}`);
        console.log("KIND", track.kind);

        if (track.kind === "data") {

          if (!VueThis.participantIsInstructor(VueThis.activeRoom.localParticipant)) {
            VueThis.fetchCameraMirror();
          }

          track.on("message", (data) => {
            try {
                const messageObj = JSON.parse(data);
                console.log(messageObj);
                switch (messageObj.type) {
                  case "MIRROR_INSTRUCTOR_CAMERA":
                    this.isMirrored = messageObj.value;
                  case "FETCH_MIRROR_INSTRUCTOR_CAMERA":
                    if (VueThis.participantIsInstructor(VueThis.activeRoom.localParticipant)) {
                      VueThis.sendCameraMirror();
                    }
                }
            } catch (e) {
                this.addChatMessage(data, participant);
            }
            // this.addChatMessage(data, participant);
            //                            console.log('MESSAGE SENT', data);
          });
        }
      });
      //                }, 20000);

      //                console.log('attach to ' + participantContainer.getAttribute('id'));
    },
    sendTextMessage() {
      this.dataTrack.send(this.currentMessage);
      this.addChatMessage(
        this.currentMessage,
        this.activeRoom.localParticipant
      );
      this.currentMessage = "";
      document.querySelector(".message-area").value = "";
    },
    addChatMessage(message, participant) {
      let chatMessage = {
        text: message,
        sender: participant.identity,
        receivedAt: this.formatAMPM(new Date()),
      };
      this.chatMessages.push(chatMessage);
      if (!this.chatIsOpened) {
        console.log(this.chatMessages);
        this.countUnreadMessages++;
      }
    },

    openChat() {
      if (this.chatIsOpened) {
        this.chatIsOpened = false;

        return;
      } else {
        this.countUnreadMessages = 0;
        this.chatIsOpened = true;
      }
    },

    toggleCameraMirror() {
      this.isMirrored = !this.isMirrored;
      this.sendCameraMirror();
    },

    fetchCameraMirror() {
      console.log("fetchCameraMirror");
      this.dataTrack.send(JSON.stringify({
        type: "FETCH_MIRROR_INSTRUCTOR_CAMERA",
      }));
    },

    sendCameraMirror() {
      console.log("sendCameraMirror");
      this.dataTrack.send(JSON.stringify({
        type: "MIRROR_INSTRUCTOR_CAMERA",
        value: this.isMirrored,
      }));
    },

    toggleExpandUsersWrap() {
      if (this.isUsersExpanded) {
        this.isUsersExpanded = false;
        return;
      } else {
        this.isUsersExpanded = true;
      }
    },
  },
};
</script>