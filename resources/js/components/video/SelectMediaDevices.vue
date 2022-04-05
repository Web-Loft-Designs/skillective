<template>
  <div class="card" id="select-media-devices">
    <div class="card-block">
      <div v-if="errorText" class="err-message">{{ errorText }}</div>
      <h4 class="card-title">Select Media Devices</h4>
      <div class="form-group">
        <label class="form-text text-muted">Audio Inputs</label>
        <div class="input-group">
          <select id="audioinput" name="audioinput"></select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-text text-muted">Video Input</label>
        <div class="input-group">
          <select id="videoinput" name="videoinput"></select>
        </div>
      </div>
      <!--<div class="form-group">-->
      <!--<label class="form-text text-muted">Audio Output</label>-->
      <!--<div class="input-group">-->
      <!--<select id="audiooutput" name="audiooutput"></select>-->
      <!--</div>-->
      <!--</div>-->
      <button @click="joinRoom">Connect</button>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../../Event";
import siteAPI from "../../mixins/siteAPI.js";

export default {
  mixins: [siteAPI],
  data() {
    return {
      deviceSelections: null,
      audioDeviceId: null,
      videoDeviceId: null,
    };
  },
  props: [],
  mounted() {
    this.deviceSelections = {
      audioinput: document.querySelector("select#audioinput"),
      videoinput: document.querySelector("select#videoinput"),
      //                audiooutput: document.querySelector('select#audiooutput'),
    };
    this.updateDeviceSelectionOptions();
    navigator.mediaDevices.ondevicechange = this.updateDeviceSelectionOptions();
  },
  methods: {
    joinRoom() {
      let selectedDevices = {
        audio: this.deviceSelections.audioinput.value,
        video: this.deviceSelections.videoinput.value,
        //                        audioOut : this.deviceSelections.audiooutput.value
      };

      console.log(selectedDevices);
      EventBus.$emit("deviceSelected", selectedDevices);
    },
    /**
     * Get the list of available media devices of the given kind.
     * @param {Array<MediaDeviceInfo>} deviceInfos
     * @param {string} kind - One of 'audioinput', 'audiooutput', 'videoinput'
     * @returns {Array<MediaDeviceInfo>} - Only those media devices of the given kind
     */
    getDevicesOfKind(deviceInfos, kind) {
      return deviceInfos.filter(function (deviceInfo) {
        return deviceInfo.kind === kind;
      });
    },
    /**
     * Get the list of available media devices.
     * @returns {Promise<DeviceSelectionOptions>}
     * @typedef {object} DeviceSelectionOptions
     * @property {Array<MediaDeviceInfo>} audioinput
     * @property {Array<MediaDeviceInfo>} audiooutput
     * @property {Array<MediaDeviceInfo>} videoinput
     */
    getDeviceSelectionOptions() {
      let VueThis = this;
      return navigator.mediaDevices.enumerateDevices().then((deviceInfos) => {
        var kinds = ["audioinput", "videoinput"]; // , 'audiooutput'
        return kinds.reduce(function (deviceSelectionOptions, kind) {
          deviceSelectionOptions[kind] = VueThis.getDevicesOfKind(
            deviceInfos,
            kind
          );
          return deviceSelectionOptions;
        }, {});
      });
    },
    updateDeviceSelectionOptions() {
      let VueThis = this;
      // before enumerating devices, get media permssions
      // NOTE: w/o media permissions, safari/ff does not return the labels
      // (like front camera, back camera) for the devices.

      let resolution = { width: 1920, height: 1080 };

      if (window.innerWidth <= 500) {
        resolution = { width: 800, height: 600 };
      }

      return navigator.mediaDevices
        .getUserMedia({ audio: true, video: resolution })
        .then(VueThis.getDeviceSelectionOptions)
        .then((deviceSelectionOptions) => {
          let _deviceSelectionOptions = deviceSelectionOptions;
          ["audioinput", "videoinput"].forEach((kind) => {
            // , 'audiooutput'
            let kindDeviceInfos = _deviceSelectionOptions[kind];
            let select = VueThis.deviceSelections[kind];

            [].slice.call(select.children).forEach(function (option) {
              option.remove();
            });

            kindDeviceInfos.forEach((kindDeviceInfo) => {
              var deviceId = kindDeviceInfo.deviceId;
              var label =
                kindDeviceInfo.label ||
                "Device [ id: " + deviceId.substr(0, 5) + "... ]";

              var option = document.createElement("option");
              option.value = deviceId;
              option.appendChild(document.createTextNode(label));
              select.appendChild(option);
            });
          });
        })
        .catch((error) => {
          console.log(error);
          this.errorText = error.toString();
        });
    },
  },
};
</script>

