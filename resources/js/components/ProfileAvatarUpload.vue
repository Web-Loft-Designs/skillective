<template>
    <div id="avatar-upload-container">

            <div class="avatar-upload-custom" v-if="submitUrl!=''">
                <vue-dropzone
                        id="dashboard-dropzone-media"
                              ref="dropzone"
                              :options="getDropOptions()"
                              @vdropzone-sending="onDZSending"
                              @vdropzone-success="onDZSuccess"
                              @vdropzone-complete="onDZComplete"
                              @vdropzone-error="onDZError">
                </vue-dropzone>
            </div>

        <!--<button @click="deleteProfileImage" >Remove Profile Image</button>-->

        <div v-if="errorText" class="has-error" v-html="errorText"></div>
    </div>
</template>

<script>
	import vueDropzone from "vue2-dropzone";
	import siteAPI from '../mixins/siteAPI.js';
	import $ from 'jquery'

	export default {
		components: {
			vueDropzone
		},
		mixins : [siteAPI],
		props : {
			userId : null,
		},
		data() {
			return {
				submitUrl : '',
			}
		},
		methods: {
			deleteProfileImage(media) {
				var deleteUrl = '/api/user/profile-image';
				if (this.userId!=null)
					deleteUrl += ('/' + this.userId);

				this.apiDelete(deleteUrl);
			},
			componentHandleDeleteResponse(responseData){
				this.updateProfileImagesOnPage( responseData.data )
			},
			updateProfileImagesOnPage(imageUrl){
				var _rand = Math.floor(Math.random() * Math.floor(9999))
				imageUrl += ('?r=' + _rand.toString())
				if (this.userId==null){
				    $('#profile-image-menu').attr('src', imageUrl);
				}
				$('#uploader-profile-image').css('background-image', "url('"+imageUrl+"')");
            },
			onDZSending(file) {
				this.apiPreSend();
	        },
			onDZComplete(file) {
				this.loader.hide();
				this.$refs.dropzone.removeAllFiles();
			},
			onDZSuccess(file, response) {
//				var profileImage = response.data;
				this.updateProfileImagesOnPage( response.data )
			},
			onDZError(file, errorMessage) {
				this.loader.hide();
				this.errorText = errorMessage.message;
				if ( errorMessage.errors != undefined ){
                    for (var error in errorMessage.errors){
						if (errorMessage.errors.hasOwnProperty(error)) {
							this.errorText += '<br>' + ( errorMessage.errors[error][0].replace(/media\.(\d+)/, 'media $1') );
						}
                    }
                }
            },
			getDropOptions(){
                return {
					url: this.submitUrl, // "/api/user/profile-image"
					headers: {
						"X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
					},
					maxFiles: 1,
					addRemoveLinks: false,
					maxFilesize: 8, // MB
					paramName: 'profile_image',
					uploadMultiple: false,
					acceptedFiles: ".jpeg,.jpg,.png",
					createImageThumbnails : false,
					dictDefaultMessage : 'Upload photo'
				}
            }
		},
		created: function(){
			if (this.userId!=null)
				this.submitUrl = '/api/user/profile-image/' + this.userId;
			else
				this.submitUrl = '/api/user/profile-image';
		}
	}
</script>