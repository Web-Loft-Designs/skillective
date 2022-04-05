<template>
       <div>
           <img src="/images/img-popup.png" alt="">
           <p>{{registrationConfirmationText}}</p>
           <span class="btn" @click="close">Got it, Thanks!</span>
       </div>
</template>

<script>

	export default {
        props: ['modalWindow', 'registrationConfirmationText'],
		data() {
			return {

			}
		},
		components: {

		},
		methods: {
            close() {

                this.modalWindow.close();
            }
		},
		mounted() {

		}
	}
</script>