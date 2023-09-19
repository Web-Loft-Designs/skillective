<template>
  <div id='dashboard-gallery-container'>
    <div class='dashboard-gallery-top'>
      <h2>Gallery</h2>
      <div class='filter-wrapper'>
        <a :class="{'active':(showOnly=='all')}" class='filter-button' @click.prevent="toggleShowOnly('all')">All</a>
        <a
          :class="{'active':(showOnly=='instagram')}"
          class='filter-button'
          @click.prevent="toggleShowOnly('instagram')"
        >Instagram</a>
        <a
          :class="{'active':(showOnly=='website_images')}"
          class='filter-button'
          @click.prevent="toggleShowOnly('website_images')"
        >Skillective</a>
      </div>
    </div>
    <div v-if="description!=''" v-html='description'></div>
    <div v-if='instagramMediaQueue'>Instagram media will be loaded to gallery soon</div>
    <div v-if='errorText' class='has-error mb-5' v-html='errorText'></div>
    <div class='gallery-wrapper row'>
      <div class='col-custom-20 col-md-3 col-sm-6 col-12'>
        <vue-dropzone
          id='dashboard-dropzone-media'
          ref='dropzone'
          :options='dropOptions'
          @vdropzone-sending='onDZSending'
          @vdropzone-success='onDZSuccess'
          @vdropzone-complete='onDZComplete'
          @vdropzone-error='onDZError'
        >
        </vue-dropzone>
      </div>
      <div
        v-for='(media, key) in listItems'
        v-if="(showOnly=='all' || showOnly==media.collection_name)"
        :key='key'
        :class="{'has-video': isVideo(media.url)}"
        class='col-custom-20 col-md-3 col-sm-6 col-12 gallery-wrapper-item'
      >
                <span v-if='isVideo(media.url)'>
                    <a :id="'media-'+media.id" :href='media.url' class='video-link g-item'>
                        <img :src='media.thumb_url'/>
                        <span class='play-icon'></span>
                    </a>
                    <span class='remove' @click='deleteMedia(media)'><img alt='' src='/images/remove.png'></span>
                    <span class='share'>
                            <img alt='' src='/images/share-icon.png'>
                            <span class='share-tooltip'>
                                <span>Share on:</span>
                                <a href='#'><img alt='' src='/images/insta.png'></a>
                                <a href='#'><img alt='' src='/images/tw.png'></a>
                                <a href='#'><img alt='' src='/images/fb.png' style='margin-right: 0'></a>
                            </span>
                    </span>
                  <!--<span class="info" v-if="media.collection_name=='instagram'">-->
                  <!--<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>-->
                  <!--<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>-->
                  <!--</span>-->
                </span>
        <span v-else>
                    <a :id="'media-'+media.id" :href='media.url' class='image g-item'>
                        <img :src='media.url'/>
                    </a>
                    <span class='remove' @click='deleteMedia(media)'><img alt='' src='/images/remove.png'></span>
                    <span class='share'>
                            <img alt='' src='/images/share-icon.png'>
                            <span class='share-tooltip'>
                                <span>Share on:</span>
                                                            <a href='#'><img alt='' src='/images/insta.png'></a>
                                <a
                                  :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"
                                  data-sharer='twitter'
                                ><img alt='' src='/images/tw.png'></a>
                                <a
                                  :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"
                                  data-sharer='facebook'
                                ><img alt='' src='/images/fb.png' style='margin-right: 0'></a>
                            </span>
                    </span>
          <!--<span class="info" v-if="media.collection_name=='instagram'">-->
          <!--<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>-->
          <!--<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>-->
          <!--</span>-->
                </span>
      </div>
    </div>
    <div>
      <a v-if='this.userMedia.length>0' class='more' @click='showMore()'>Show more</a>
    </div>

  </div>
</template>

<script>
import vueDropzone from 'vue2-dropzone'
import siteAPI from '../mixins/siteAPI.js'

export default {
  components: { vueDropzone },
  mixins: [siteAPI],
  props: {
    userMedia: Array,
    instagramMediaQueue: Boolean,
    description: ''
  },
  data() {
    return {
      showOnly: 'all',
      portion: 30,
      siteUrl: '',
      dropOptions: {
        url: '/api/user/media',
        headers: {
          'X-CSRF-TOKEN': document.head.querySelector('[name=csrf-token]').content
        },
        maxFiles: 1,
        addRemoveLinks: false,
        maxFilesize: 8, // MB
        paramName: 'media',
        uploadMultiple: false,
        acceptedFiles: '.jpeg,.jpg,.png,.mp4,.mov',
        createImageThumbnails: false
      }
    }
  },
  methods: {
    deleteMedia(media) {
      this.loader = null
      this.itemToDelete = media
      this.apiDelete('/api/user/media/' + this.itemToDelete.id)
    },
    isVideo(element) {
      if (element.split('.').pop() === 'mp4' || element.split('.').pop() === 'mov') {
        return true
      } else {
        return false
      }
    },
    componentHandleDeleteResponse(responseData) {
      if (this.itemToDelete != null) {
        this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1)
        this.itemToDelete = null
      }
    },
    toggleShowOnly(type) {
      this.showOnly = type
    },
    showMore(_portion) {
      if (_portion == undefined)
        _portion = this.portion
      this.toggleShowOnly('all')
      var counter = 0
      for (var l in this.userMedia) {
        counter++
        this.listItems.push(_.cloneDeep(this.userMedia[l]))
        if (counter == _portion)
          break
      }
      this.userMedia.splice(this.userMedia.indexOf(this.userMedia[0]), _portion)
    },
    onDZSending(file) {
      this.apiPreSend()
    },
    onDZComplete(file) {
      if (this.loader !== null) {
        this.loader.hide()
        this.loader = null
      }
      this.$refs.dropzone.removeAllFiles()
    },
    onDZSuccess(file, response) {
      var responseMedia = response.data.reverse()
      for (var i = 0; i < responseMedia.length; i++) {
        this.listItems.unshift(responseMedia[i])
        if (this.listItems.length > this.portion)
          this.listItems.pop()
      }
    },
    onDZError(file, errorMessage) {
      // if(this.loader) {
      //     this.loader.hide();
      // }
      if (errorMessage.message) this.errorText = errorMessage.message
      else this.errorText = errorMessage
      if (errorMessage.errors != undefined) {
        for (var error in errorMessage.errors) {
          if (errorMessage.errors.hasOwnProperty(error)) {
            this.errorText += '<br>' + (errorMessage.errors[error][0].replace(/media\.(\d+)/, 'media $1'))
          }
        }
      }
    }
  },
  created: function () {
    var _portion = this.portion - 1 // because of upload new block
    this.showMore(_portion)
    setTimeout(function () {
      if (window.location.hash != undefined && window.location.hash.length > 0) {
        var hash = window.location.hash.substring(1)
        var el = document.getElementById(hash)
        if (el) {
          el.click()
        } else {
//                        alert('this media not exist');
        }
      }
    }, 1000)
    this.siteUrl = window.location.protocol + '//' + window.location.host
  }
}
</script>