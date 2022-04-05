import Vue from 'vue'
import { EventBus } from "../../../Event";
import TwilioVideo, {
    connect,
    createLocalTracks,
    createLocalVideoTrack,
    LocalVideoTrack
} from "twilio-video";
import Waveform from "../utils/waveform";
var array = require("lodash/array");
import RemoteParticipantTrack from "../RemoteParticipantTrack.vue";
const RemoteParticipantTrackClass = Vue.extend(RemoteParticipantTrack);

let twilioVideoHelper = {
    data: function() {
        return {
            waveforms: [],
            waveformsEnabled: false,
            mutedParticipants: [],
            allMuted: false,
            screenTrackWidth: 1920,
            screenTrackHeight: 1080,
            localScreenTrack: null,
            localTrack: false,
            localVideoTrack: null,
            localTracks: [],
            expandedParticipant: null,
            expandedParticipantIdentity: "",
            isLocalVideoStopped: false
        };
    },
    methods: {
        createScreenTrack(height, width) {
            if (
                typeof navigator === "undefined" ||
                !navigator.mediaDevices ||
                !navigator.mediaDevices.getDisplayMedia
            ) {
                return Promise.reject(
                    new Error("getDisplayMedia is not supported")
                );
            }
            return navigator.mediaDevices
                .getDisplayMedia({
                    mandatory: {
                        video: {
                            height: height,
                            width: width
                        }
                    }
                })
                .then(function(stream) {
                    return new TwilioVideo.LocalVideoTrack(
                        stream.getVideoTracks()[0]
                    );
                });
        },

        toggleScreensharing() {
            if (this.localScreenTrack == null) {
                try {
                    // Create and preview your local screen.
                    this.createScreenTrack(
                        this.screenTrackHeight,
                        this.screenTrackWidth
                    ).then(track => {
                        this.unpublishLocalParticipantCurrentVideoTrack();
                        this.localScreenTrack = track;
                        this.switchLocalAndScreenTracks(this.localScreenTrack);
                        this.localScreenTrack.on("stopped", () => {
                            this.unpublishLocalParticipantCurrentVideoTrack();
                            this.switchLocalAndScreenTracks(
                                this.localVideoTrack
                            );
                        });
                    });
                } catch (e) {
                    alert(e.message, e.code);
                }
            } else {
                // Stop capturing your screen.
                // this.localScreenTrack.stop();
                // this.activeRoom.localParticipant.unpublishTrack(this.localScreenTrack);
                this.unpublishLocalParticipantCurrentVideoTrack();
                this.switchLocalAndScreenTracks(this.localVideoTrack);
            }
        },

        switchLocalAndScreenTracks(track) {
            this.activeRoom.localParticipant.publishTrack(track);
            let localParticipantTrackContainer = this.getParticipantContainer(
                this.activeRoom.localParticipant
            );
            this.attachTracks([track], localParticipantTrackContainer);
        },

        unpublishLocalParticipantCurrentVideoTrack() {
            this.getTracks(this.activeRoom.localParticipant).forEach(track => {
                if (track.kind == "video") {
                    this.activeRoom.localParticipant.unpublishTrack(track);
                    this.localScreenTrack = null;

                    let participantTracksContainer = this.getParticipantContainer(
                        this.activeRoom.localParticipant
                    );
                    participantTracksContainer.querySelector("video").remove();
                }
            });
        },

        // disableLocalParticipantCurrentVideoTrack(){
        //     this.getTracks(this.activeRoom.localParticipant).forEach( track => {
        //         if (track.kind == 'video') {
        //             console.log('LOCAL PARTICIPANT VIDEO TRACK DISABLE');
        //             if (this.localScreenTrack==null){
        //                 if ( this.localVideoTrack==null )
        //                     this.localVideoTrack = track;
        //                 this.localVideoTrack.disable();
        //             }else if (this.localScreenTrack!=null && track.sid==this.localScreenTrack.sid){
        //                 this.activeRoom.localParticipant.unpublishTrack(track);
        //                 this.localScreenTrack = null;
        //             }
        //         }
        //     })
        // },

        getTracks(participant) {
            // console.log('get tracks of ' + participant.identity);
            let tracks = Array.from(participant.tracks.values())
                .filter(function(publication) {
                    return publication.track;
                })
                .map(function(publication) {
                    return publication.track;
                });
            // console.log(tracks.length);
            return tracks;
        },

        // Attach the Participant's Tracks to the DOM.
        attachParticipantTracks(participant, container) {
            // console.log('attachParticipantTracks ' + participant.identity);
            var tracks = this.getTracks(participant);
            this.attachTracks(tracks, container);
            if (participant.sid == this.activeRoom.localParticipant.sid) {
                this.localTrack = true;
            }
        },

        // Attach the Tracks to the DOM.
        attachTracks(tracks, container) {
            let VueThis = this;
            tracks.forEach(function(track) {
                if (track.kind === "audio" || track.kind === "video") {
                    // console.log('attach ' + track.kind);
                    // console.log(container.getAttribute('id'));

                    setTimeout(() => {
                        container.appendChild(track.attach());
                    }, 100);

                    if (VueThis.waveformsEnabled && track.kind === "audio") {
                        let currentTrackSid = track.sid;
                        let waveform = new Waveform();
                        waveform.setStream(
                            container.querySelector("audio").srcObject
                        );
                        container.innerHTML +=
                            '<div class="audiowave" id="audio-' +
                            currentTrackSid +
                            '" style="border:1px solid green;"></div>';
                        let waveformContainer = container.querySelector(
                            ".audiowave"
                        );
                        const canvas = waveformContainer.querySelector(
                            "canvas"
                        );
                        if (!canvas) {
                            waveformContainer.appendChild(waveform.element);
                        }
                        VueThis.waveforms.push({
                            waveform: waveform,
                            audioSid: currentTrackSid
                        });
                    }
                }
            });
        },

        // A new RemoteTrack was published to the Room.
        trackPublished(publication, container) {
            // console.log('Track was of kind ' + publication.kind + ' was published: subscribed = ' + publication.isSubscribed);
            if (publication.isSubscribed) {
                this.attachTracks([publication.track], container);
            }
            publication.on("subscribed", track => {
                // console.log('publication SUBSCRIBED ' + track.kind);
                // console.log(container);
                // console.log(container.getAttribute('id'));
                this.attachTracks([track], container);
            });
            publication.on("unsubscribed", track => {
                // console.log('publication unsubscribed' + track.kind);
                this.detachTrack(track);
            });
        },

        // A RemoteTrack was unpublished from the Room.
        trackUnpublished(publication, container) {
            // console.log('trackUnpublished');
            if (publication.kind == "video") {
                if (container && container.querySelector("video")) {
                    container.querySelector("video").remove();
                }
            } else if (publication.kind == "audio") {
                this.removeAudioWave(publication.trackSid);
                if (container && container.querySelector("audio")) {
                    container.querySelector("audio").remove();
                }
            }
        },

        onTrackDisabled(track, container) {
            // console.log('onTrackDisabled');
            if (track.kind == "video") {
                // console.log(container.querySelector('video:first-of-type'));
                container.querySelector("video").remove();
            } else if (track.kind == "audio") {
                this.removeAudioWave(track.track);
                // console.log(container.querySelector('audio'));
                container.querySelector("audio").remove();
            }
        },

        onTrackEnabled(track) {
            console.log("track " + track.sid + " enabled");
        },

        getParticipantContainer(participant) {
            // console.log('getParticipantContainer---');
            let participantContainer = null;
            if (this.participantIsInstructor(participant)) {
                participantContainer = document.getElementById(
                    "instructorTrack"
                );
                // console.log('instructorTrack');
            } else if (
                !this.participantIsInstructor(participant) &&
                participant.sid == this.activeRoom.localParticipant.sid
            ) {
                // console.log('localTrack');
                participantContainer = document.getElementById("localTrack");
            } else {
                // let remoteParticipantContainerID = 'remote-participanr-container-' + participant.sid;
                // if (document.getElementById(remoteParticipantContainerID)==null){
                // setTimeout(() => {
                // document.getElementById('participantsTracks').innerHTML += '<div id="'+remoteParticipantContainerID+'"></div>'
                // new RemoteParticipantTrackClass({ propsData : { roomSettings : this.roomSettings, participantData : participant } }).$mount('#' + remoteParticipantContainerID)
                let rp = new RemoteParticipantTrackClass({
                    propsData: {
                        roomSettings: this.roomSettings,
                        participantData: participant,
                        mutedParticipants: this.mutedParticipants,
                        isParticipantExpanded:
                            this.expandedParticipant &&
                            this.expandedParticipant.sid === participant.sid
                    }
                }).$mount();
                document
                    .getElementById("participantsTracks")
                    .appendChild(rp.$el);
                // }, 3000);
                // }
                // console.log('participant-' + participant.sid + '-track');
                participantContainer = document.getElementById(
                    "participant-" + participant.sid + "-track"
                );
            }
            return participantContainer;
        },

        removeAudioWave(trackSid) {
            if (this.waveformsEnabled) {
                let currentTrackSid = trackSid;
                this.waveforms.forEach(wf => {
                    if (wf.audioSid == currentTrackSid) {
                        wf.waveform.unsetStream();
                        let canvas = document
                            .getElementById("audio-" + currentTrackSid)
                            .querySelector("canvas");
                        if (canvas) {
                            canvas.remove();
                        }
                    }
                });
                array.remove(this.waveforms, function(wf) {
                    return wf.audioSid == currentTrackSid;
                });
                document.getElementById("audio-" + currentTrackSid).remove();
            }
        },

        // Detach the Tracks from the DOM.
        detachTracks(tracks) {
            // console.log('detachTracks');
            tracks.forEach(this.detachTrack);
        },

        detachTrack(track) {
            // console.log('detachTrack');
            if (track.kind === "audio" || track.kind === "video") {
                track.detach().forEach(element => {
                    if (track.kind === "audio") {
                        // console.log('DETACH audio');
                        this.removeAudioWave(track.sid);
                    } else if (track.kind === "video") {
                        // console.log('DETACH video');
                    }
                    element.remove();
                });
            }
        },

        detachParticipantTracks(participant) {
            var tracks = this.getTracks(participant);
            // console.log('detach ' + participant.identity + 'tracks = ' + tracks.length);
            this.detachTracks(tracks);
            // let VueThis = this;

            if (
                this.activeRoom.localParticipant != null &&
                participant.sid == this.activeRoom.localParticipant.sid
            ) {
                this.localTrack = false;
                if (this.participantIsInstructor(participant)) {
                    this.instructorParticipantHere = false;
                    document.getElementById("instructorTrack").innerHTML = "";
                } else {
                    document.getElementById("localTrack").innerHTML = "";
                }
            } else if (this.participantIsInstructor(participant)) {
                this.instructorParticipantHere = false;
                document.getElementById("instructorTrack").innerHTML = "";
            } else {
                let participantContainer = document.getElementById(
                    "participant-" + participant.sid + "-container"
                );
                participantContainer.remove();
            }
        },

        participantIsInstructor(participant) {
            return participant.identity.indexOf("Instructor: ") === 0;
        },

        // Trigger log events
        dispatchLog(message) {
            EventBus.$emit("new_log", message);
        },

        leaveRoom() {
            this.activeRoom.disconnect();
            window.close();
        },

        toggleStopLocalVideoStream(event) {
            // console.log('----------------');
            // console.log('toggleStopLocalVideoStream for ' + this.activeRoom.localParticipant.identity);
            let participant = this.activeRoom.localParticipant;
            // console.log('toggleStopLocalVideoStream for ' + participant.identity);
            this.getTracks(participant).forEach(track => {
                if (track.kind === "video") {
                    if (this.isLocalVideoStopped == false) {
                        // console.log('toggleStopLocalVideoStream VIDEO track disable');
                        track.disable();
                    } else {
                        // console.log('toggleStopLocalVideoStream VIDEO track enable');
                        track.enable();
                    }
                    this.isLocalVideoStopped = !this.isLocalVideoStopped;
                }
            });
        },

        toggleMute(participant, event, shouldBeMuted) {
            if (participant == null)
                participant = this.activeRoom.localParticipant;
            let isLocalParticipant =
                this.activeRoom.localParticipant.sid == participant.sid;

            this.getTracks(participant).forEach(track => {
                if (track.kind === "audio") {
                    let isMuted =
                        this.mutedParticipants.indexOf(participant.sid) !== -1;

                    console.log(track);

                    if (
                        (!isMuted && shouldBeMuted == null) ||
                        (!isMuted && shouldBeMuted === true)
                    ) {
                        // console.log('track disable');
                        this.mutedParticipants.push(participant.sid);
                        if (isLocalParticipant) {
                            console.log("LOCAL PARTICIPANT DISABLED");
                            track.disable();
                        } else {
                            console.log(
                                "REMOTE PARTICIPANT DISABLED, BUT HOW IT WORKS?"
                            );
                            let audioContainer = document
                                .getElementById(
                                    "participant-" + participant.sid + "-track"
                                )
                                .querySelector("audio");
                            audioContainer.muted = true;
                        }
                    } else if (
                        (isMuted && shouldBeMuted == null) ||
                        (isMuted && shouldBeMuted === false)
                    ) {
                        // console.log('track enable');
                        if (isLocalParticipant) {
                            track.enable();
                            console.log("LOCAL PARTICIPANT");
                        } else {
                            // console.log('audio tag unmute');
                            let audioContainer = document
                                .getElementById(
                                    "participant-" + participant.sid + "-track"
                                )
                                .querySelector("audio");
                            audioContainer.muted = false;

                            console.log(track);
                            console.log(
                                "LOCAL PARTICIPANT SHOULD BE ENABLED, BUT HOW IT WORKS?"
                            );
                        }

                        let mutedIndex = this.mutedParticipants.findIndex(
                            item => item == participant.sid
                        );

                        if (mutedIndex !== -1) {
                            this.mutedParticipants.splice(mutedIndex, 1);
                        }
                    }
                    this.dispatchLog(
                        "local participant " + (!isMuted ? "Muted" : "Unmuted")
                    );
                }
            });
        },

        toggleMuteAllRemote() {
            let shouldBeMuted = !this.allMuted;

            this.activeRoom.participants.forEach(participant => {
                if (participant.sid != this.activeRoom.localParticipant.sid)
                    this.toggleMute(participant, null, shouldBeMuted);
            });

            this.allMuted = !this.allMuted;
        },

        toggleExpandCollapse(participant) {
            if (this.expandedParticipant == null) {
                this.expandedParticipant = participant;
                this.expandedParticipantIdentity = this.getIdentityName(
                    this.expandedParticipant.identity
                );
                setTimeout(() => {
                    this.getTracks(this.expandedParticipant).forEach(track => {
                        if (track.kind == "video") {
                            document
                                .getElementById("expanded-video-container")
                                .appendChild(track.attach());
                        }
                    });
                }, 10);
            } else {
                let runAgain = false;
                if (
                    this.expandedParticipant != null &&
                    this.expandedParticipant.sid != participant.sid
                ) {
                    runAgain = true;
                }

                this.expandedParticipant = null;
                this.expandedParticipantIdentity = "";

                if (runAgain) {
                    this.toggleExpandCollapse(participant);
                }
            }
        },

        closeRoom() {
            let really = confirm(
                "Do you really want to finish this virtual lesson?"
            );
            if (really) {
                this.apiPreSend();
                axios
                    .delete(
                        "/api/instructor/virtual-lessons/" +
                            this.roomSettings.lessonId +
                            "/room/complete"
                    )
                    .then(response => {
                        this.apiHandleResponse(response);
                        this.roomName = null;
                        window.close();
                    })
                    .catch(error => this.apiHandleError(error));
            }
        },

        getIdentityName(identity) {
            return identity.substr(identity.indexOf(":") + 1);
        },

        getParticipantImage(identity) {
            let participantIndex = _.findIndex(
                this.roomSettings.participantsDetails,
                ["identity", identity]
            );
            if (participantIndex != undefined)
                return this.roomSettings.participantsDetails[participantIndex]
                    .image;

            return "";
        },

        formatAMPM(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? "pm" : "am";
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? "0" + minutes : minutes;
            var strTime = hours + ":" + minutes + " " + ampm;
            return strTime;
        }
    }
};

export default twilioVideoHelper;
