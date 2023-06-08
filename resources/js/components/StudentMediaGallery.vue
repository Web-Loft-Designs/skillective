<template>
<!--    <div id="dashboard-gallery-container" class="dashboard-gallery">-->

<!--        <div class="dashboard-gallery-top">-->
<!--            <h2>Gallery</h2>-->
<!--        </div>-->

<!--        <div v-if="description!=''" v-html="description"></div>-->

<!--        <div v-if="instagramMediaQueue">Instagram media will be loaded to gallery soon</div>-->

<!--        <div class="gallery-wrapper row">-->
<!--            <div class="col-custom-20 col-md-3 col-sm-6 col-12" v-if="submitUrl!=''">-->
<!--                <vue-dropzone id="dashboard-dropzone-media"-->
<!--                              ref="dropzone"-->
<!--                              :options="getDropOptions()"-->
<!--                              @vdropzone-sending="onDZSending"-->
<!--                              @vdropzone-success="onDZSuccess"-->
<!--                              @vdropzone-complete="onDZComplete"-->
<!--                              @vdropzone-error="onDZError">-->
<!--                </vue-dropzone>-->
<!--            </div>-->

<!--            <div class="col-custom-20 col-md-3 col-sm-6 col-12 gallery-wrapper-item" :class="{'has-video': isVideo(media.url)}" :key="key" v-for="(media, key) in listItems" >-->
<!--                <span v-if="isVideo(media.url)">-->
<!--                    <a :href="media.url" :id="'media-'+media.id" class="video-link g-item">-->
<!--                        <img :src="media.thumb_url"/>-->
<!--                        <span class="play-icon"></span>-->
<!--                    </a>-->
<!--                    <span class="remove" @click="deleteMedia(media)"><img src="/images/remove.png" alt=""></span>-->
<!--                    <span class="share">-->
<!--                            <img src="/images/share-icon.png" alt="">-->
<!--                            <span class="share-tooltip">-->
<!--                                <span>Share on:</span>-->
<!--                                <a href="#"><img src="/images/insta.png" alt=""></a>-->
<!--                                <a href="#"><img src="/images/tw.png" alt=""></a>-->
<!--                                <a href="#"><img style="margin-right: 0" src="/images/fb.png" alt=""></a>-->
<!--                            </span>-->
<!--                    </span>-->
<!--                    &lt;!&ndash;<span class="info" v-if="media.collection_name=='instagram'">&ndash;&gt;-->
<!--                        &lt;!&ndash;<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>&ndash;&gt;-->
<!--                        &lt;!&ndash;<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>&ndash;&gt;-->
<!--                    &lt;!&ndash;</span>&ndash;&gt;-->
<!--                </span>-->
<!--                <span v-else>-->
<!--                    <a :href="media.url" :id="'media-'+media.id" class="image g-item">-->
<!--                        <img :src="media.url"/>-->
<!--                    </a>-->
<!--                    <span class="remove" @click="deleteMedia(media)"><img src="/images/remove.png" alt=""></span>-->
<!--                    <span class="share">-->
<!--                            <img src="/images/share-icon.png" alt="">-->
<!--                             <span class="share-tooltip">-->
<!--                                <span>Share on:</span>-->
<!--                                                               <a href="#"><img src="/images/insta.png" alt=""></a>-->
<!--                                <a data-sharer="twitter" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img src="/images/tw.png" alt=""></a>-->
<!--                                <a data-sharer="facebook" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img style="margin-right: 0" src="/images/fb.png" alt=""></a>-->
<!--                            </span>-->
<!--                    </span>-->
<!--                    &lt;!&ndash;<span class="info" v-if="media.collection_name=='instagram'">&ndash;&gt;-->
<!--                        &lt;!&ndash;<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>&ndash;&gt;-->
<!--                        &lt;!&ndash;<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>&ndash;&gt;-->
<!--                    &lt;!&ndash;</span>&ndash;&gt;-->
<!--                </span>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div>-->
<!--            <a v-if="this.userGalleryMedia.length>0" @click="showMore()" class="more">Show more</a>-->
<!--        </div>-->
<!--        <div v-if="errorText" class="has-error" v-html="errorText"></div>-->
<!--    </div>-->
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
            instagramMediaQueue: Boolean,
			userId : null,
			description : ''
		},
		data() {
			return {
				submitUrl : '',
				portion : 6,
				userGalleryMedia : [],
                siteUrl: '',
			}
		},
		methods: {
			getDropOptions(){
				return {
					url: this.submitUrl,
					headers: {
						"X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
					},
					maxFiles: 1,
					addRemoveLinks: false,
					maxFilesize: 8, // MB
					paramName: 'media',
					uploadMultiple: false,
					acceptedFiles: ".jpeg,.jpg,.png,.mp4,.mov",
					createImageThumbnails : false,
					dictDefaultMessage : 'Upload new file'
				};
			},
			deleteMedia(media) {
                this.loader = null;
				this.itemToDelete = media;

				var deleteUrl = '/api/user/media/' + this.itemToDelete.id;
				if (this.userId!=null)
					deleteUrl = '/api/admin/user/media/' + this.itemToDelete.id;
				this.apiDelete(deleteUrl);
			},
            isVideo(element) {
                if(element.split('.').pop() === 'mp4' || element.split('.').pop() === 'mov') {
                    return true;
                } else {
                    return false;
                }
            },
			componentHandleDeleteResponse(responseData){
				if (this.itemToDelete!=null){
					this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
					this.itemToDelete = null;
				}
			},
			showMore(_portion){
				if (_portion == undefined)
					_portion = this.portion;

				var counter = 0;
				for (var l in this.userGalleryMedia){
					counter++;
					this.listItems.push( _.cloneDeep(this.userGalleryMedia[l]) );
					if (counter==_portion)
						break;
				}
				this.userGalleryMedia.splice(this.userGalleryMedia.indexOf(this.userGalleryMedia[0]), _portion);
			},
			onDZSending(file) {
				this.apiPreSend();
	        },
			onDZComplete(file) {
				this.loader.hide();
                this.loader = null;
				this.$refs.dropzone.removeAllFiles();
			},
			onDZSuccess(file, response) {
				var responseMedia = response.data.reverse();
				for (var i=0; i<responseMedia.length; i++){
					this.listItems.unshift(responseMedia[i]);
//					if (this.listItems.length>this.portion)
//                    this.listItems.pop();
                }
			},
			onDZError(file, errorMessage) {
				this.loader.hide();
                this.loader = null;
				this.errorText = errorMessage.message;
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
			if (this.userId!=null)
				this.submitUrl = '/api/admin/user/'+this.userId+'/media';
			else
				this.submitUrl = '/api/user/media';

			var _portion = this.portion - 1; // because of upload new block
			this.userGalleryMedia = this.userMedia;
			this.showMore(_portion);
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