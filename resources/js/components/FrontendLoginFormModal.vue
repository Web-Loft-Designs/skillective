<template>
    <magnific-popup-modal @close="clearFormAndClosePopup" :show="false" :config="{closeOnBgClick:false,showCloseBtn:true,enableEscapeKey:false}" ref="modal">
    <div id="login-popup" class="login-popup">
        <front-login-form
                v-bind:login-using-instagram-url="loginUsingInstagramUrl"
                v-bind:login-using-facebook-url="loginUsingFacebookUrl"
                v-bind:login-using-google-url="loginUsingGoogleUrl"
        ></front-login-form>
    </div>
    </magnific-popup-modal>
</template>

<script>
	import MagnificPopupModal from './external/MagnificPopupModal'

	export default {
		props : ['loginUsingInstagramUrl','loginUsingFacebookUrl',
			'loginUsingGoogleUrl'],
		components: {
			MagnificPopupModal
		},
		methods: {
			componentHandlePostResponse(responseData) {
				this.clearFormAndClosePopup();
				window.location = responseData.redirect;
			},
			clearFormAndClosePopup(){
				// this.clearSubmittedForm();
				this.$refs.modal.close()
			},
			openPopup(){
				this.$refs.modal.open();
			},
		},
		mounted() {
			this.$root.$on('showLoginFormModal', () => {
				this.openPopup();
			});
		}
	}
</script>