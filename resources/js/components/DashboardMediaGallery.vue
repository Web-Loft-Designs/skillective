<template>
    <div id="dashboard-gallery-container" class="dashboard-gallery-page">
        <div v-if="instagramMediaQueue">Instagram media will be loaded to gallery soon</div>

        <div class="gallery-wrapper row">
            <div class="col-lg-2 col-md-3 col-sm-6 col-12">
                <vue-dropzone id="dashboard-dropzone-media"
                              ref="dropzone"
                              :options="dropOptions"
                              @vdropzone-sending="onDZSending"
                              @vdropzone-success="onDZSuccess"
                              @vdropzone-complete="onDZComplete"
                              @vdropzone-error="onDZError">

                </vue-dropzone>
            </div>

            <!--<div class="col-lg-2 col-md-3 col-sm-6 col-12 gallery-wrapper-item" :class="{'hidden': key > 3}" :key="key" v-for="(media, key) in listItems">-->
                <!--<a :href="media.url" :id="'media-'+media.id" v-if="key <= 3" class="image"><img :src="media.thumb_url"/></a>-->
            <!--</div>-->

            <div class="col-lg-2 col-md-3 col-sm-6 col-12 gallery-wrapper-item" :class="{'has-video': isVideo(media.url)}" :key="key" v-for="(media, key) in listItems" >
                <span v-if="isVideo(media.url)">
                    <a :href="media.url" :id="'media-'+media.id" class="video-link g-item">
                        <img :src="media.thumb_url"/>
                        <span class="play-icon"></span>
                    </a>
                    <span class="remove" @click="deleteMedia(media)"><img src="/images/remove.png" alt=""></span>
                    <span class="share">
                            <img src="/images/share-icon.png" alt="">
                            <span class="share-tooltip">
                                <span>Share on:</span>
                                <a href="#"><img src="/images/insta.png" alt=""></a>
                                <a href="#"><img src="/images/tw.png" alt=""></a>
                                <a href="#"><img style="margin-right: 0" src="/images/fb.png" alt=""></a>
                            </span>
                    </span>
                    <!--<span class="info" v-if="media.collection_name=='instagram'">-->
                        <!--<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>-->
                        <!--<span><img src="/images/comment-icon.png" alt=""> 89</span>-->
                    <!--</span>-->
                </span>
                <span v-else>
                    <a :href="media.url" :id="'media-'+media.id" class="image g-item">
                        <img :src="media.url"/>
                    </a>
                    <span class="remove" @click="deleteMedia(media)"><img src="/images/remove.png" alt=""></span>
                    <span class="share">
                            <img src="/images/share-icon.png" alt="">
                             <span class="share-tooltip">
                                <span>Share on:</span>
                                 <a href="#"><img src="/images/insta.png" alt=""></a>
                                <a data-sharer="twitter" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img src="/images/tw.png" alt=""></a>
                                <a data-sharer="facebook" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img style="margin-right: 0" src="/images/fb.png" alt=""></a>
                            </span>
                    </span>
                    <!--<span class="info" v-if="media.collection_name=='instagram'">-->
                        <!--<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>-->
                        <!--<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>-->
                    <!--</span>-->
                </span>
            </div>
        </div>
        <div v-if="errorText" class="has-error" v-html="errorText"></div>
    </div>
</template>

<script>
	import vueDropzone from "vue2-dropzone";
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		components: {
			vueDropzone
		},
		mixins : [siteAPI],
		props : {
			userMedia : Array,
            instagramMediaQueue: Boolean
		},
		data() {
			return {
				dropOptions: {
					url: "/api/user/media",
					headers: {
						"X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
					},
					maxFiles: 1,
					addRemoveLinks: false,
					maxFilesize: 8, // MB
					paramName: 'media',
					uploadMultiple: false,
					acceptedFiles: ".jpeg,.jpg,.png,.mp4,.mov",
					createImageThumbnails : false
				},
                siteUrl: '',
			}
		},
		methods: {
			onDZSending(file) {
				this.apiPreSend();
	        },
			onDZComplete(file) {
                if(this.loader) {
                    this.loader.hide();
                    this.loader = null;
                }
				this.$refs.dropzone.removeAllFiles();
			},
			onDZSuccess(file, response) {
				var responseMedia = response.data.reverse();
				for (var i=0; i<responseMedia.length; i++){
					this.listItems.unshift(responseMedia[i]);
					if (this.listItems.length>4)
					    this.listItems.pop();
                }
			},
            isVideo(element) {
                if(element.split('.').pop() === 'mp4' || element.split('.').pop() === 'mov') {
                    return true;
                } else {
                    return false;
                }
            },
            deleteMedia(media) {
                this.loader = null;
                this.itemToDelete = media;
                this.apiDelete('/api/user/media/' + this.itemToDelete.id);
            },
            componentHandleDeleteResponse(responseData){
                if (this.itemToDelete!=null){
                    this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
                    this.itemToDelete = null;
                }
            },
			onDZError(file, errorMessage) {
                if(this.loader) {
                    this.loader.hide();
                    this.loader = null;
                }
                if(errorMessage.message) {
                    this.errorText = errorMessage.message;
                } else {
                    this.errorText = errorMessage;
                }
				if ( errorMessage.errors != undefined ){
                    for (var error in errorMessage.errors){
						if (errorMessage.errors.hasOwnProperty(error)) {
							this.errorText += '<br>' + ( errorMessage.errors[error][0].replace(/media\.(\d+)/, 'media $1') );
						}
                    }
                }
            }
		},
		created: function(){
			this.listItems = this.userMedia;
            setTimeout(function () {
				if(window.location.hash!=undefined && window.location.hash.length>0) {
                    var hash = window.location.hash.substring(1);
                    var el = document.getElementById(hash);
                    if(el) {
                        el.click();
                    } else {
//                        alert('this media not exist');
                    }
                }
            },1000)
            this.siteUrl = window.location.protocol+'//'+window.location.host;
		}
	}
</script>