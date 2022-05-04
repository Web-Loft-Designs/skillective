<template>
  <div class="avatar-cropper">

    <transition name="show">
      <div v-if="opened" class="avatar-cropper__backdrop" @click.prevent="opened = false"></div>
    </transition>

    <transition name="scalein">
      <div v-if="opened" class="avatar-cropper__popup-container">
        <div class="avatar-cropper__popup" v-scroll-lock="opened" @click.stop>
          <close-button class="avatar-cropper__close" @click="opened = false" />
          <div class="avatar-cropper__content">
            <h3 class="avatar-cropper__heading">Upload image</h3>
            <div class="avatar-cropper__preview">
              <vue-avatar
                :width="200"
                :height="200"
                :scale="Number(scale)"
                :rotation="Number(rotation)"
                :borderRadius="100"
                ref="vueAvatar"
                @vue-avatar-editor:image-ready="onImageReady"
              />
            </div>
            <div class="avatar-cropper__ranges">
              <span class="avatar-cropper__label">Zoom:</span>
              <input
                class="avatar-cropper__range"
                type="range"
                :min="1"
                :max="3"
                :step="0.05"
                v-model="scale"
              />
              <span class="avatar-cropper__label">Rotation:</span>
              <input
                class="avatar-cropper__range"
                type="range"
                :min="0"
                :max="360"
                :step="1"
                v-model="rotation"
              />
            </div>
            <loader-button
              :disabled="!hasImage"
              :isLoading="isLoading"
              text="Save"
              @click="saveClicked()"
              class="avatar-cropper__button"
            />
            <field-errors v-model="errors" />
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script>
import { VueAvatar } from "vue-avatar-editor-improved";
import CloseButton from "../../student/CloseButton/CloseButton.vue";
import LoaderButton from "../../cart/LoaderButton/LoaderButton.vue";
import FieldErrors from "../../instructor/FieldErrors/FieldErrors.vue";
import axios from "axios";

export default {
  name: "AvatarCropper",
  components: {
    VueAvatar,
    CloseButton,
    LoaderButton,
    FieldErrors,
  },
  data () {
    return {
      opened: false,
      scale: 1,
      rotation: 0,
      isLoading: false,
      hasImage: false,
      submitUrl: "",
      errors: [],
    };
  },
  methods: {
    show(submitUrl) {
      this.submitUrl = submitUrl;
      this.opened = true;
    },
    saveClicked() {
      this.isLoading = true;
      this.submitImage();
    },
    dataURItoBlob(dataURI) {
      // convert base64/URLEncoded data component to raw binary data held in a string
      var byteString;
      if (dataURI.split(',')[0].indexOf('base64') >= 0) {
        byteString = atob(dataURI.split(',')[1]);
      } else {
        byteString = unescape(dataURI.split(',')[1]);
      }
      // separate out the mime component
      var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
      // write the bytes of the string to a typed array
      var ia = new Uint8Array(byteString.length);
      for (var i = 0; i < byteString.length; i++) {
          ia[i] = byteString.charCodeAt(i);
      }
      return new Blob([ia], {type:mimeString});
    },
    submitImage() {
      let img = this.$refs.vueAvatar.getImageScaled().toDataURL();
      let data = this.dataURItoBlob(img);

      const form = new FormData();
      form.append("profile_image", data);

      axios({
        method: 'post',
        url: this.submitUrl,
        data: form,
        headers: {
          "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content,
          "Content-Type": `multipart/form-data; boundary=${ form._boundary }`,
        },
      }).then((response) => {
        this.done(response);
        this.opened = false;
      }).catch((error) => {
        console.log(error);
        if (error && error.response.data.errors) {
          this.errors = error.response.data.errors.profile_image;
        }
        this.done(null);
      });
    },
    onImageReady() {
      this.scale = 1;
      this.rotation = 0;
      this.hasImage = true;
      this.errors = [];
    },
    done(response) {
      this.isLoading = false;
      this.scale = 1;
      this.rotation = 0;
      this.hasImage = false;
      if (response) {
        this.$emit("response", response);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./AvatarCropper.scss";
</style>