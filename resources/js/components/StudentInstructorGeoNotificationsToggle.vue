<template>
    <div class="toggle-favorite-container">
        <img class="icon-w-a" :style="{cursor : 'pointer', opacity : bellOpacity}" :src="bellImage" :class="{'geo-disabled':!isGeoNotificationsAllowed}" :alt="buttonTitle" :title="buttonTitle" @click.prevent="toggleGeoNotifications()">
        <div v-if="errorText" class="has-error" v-html="errorText"></div>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : {
			geoNotificationsAllowed : Boolean,
            instructorId: Number
		},
		data() {
			return {
				bellImage : '',
                buttonTitle : '',
				bellOpacity : 0,
				isGeoNotificationsAllowed : false
			}
		},
		methods: {
			toggleGeoNotifications(){
				if (this.isGeoNotificationsAllowed){
					this.apiDelete('/api/student/instructor/geo-notifications/' + this.instructorId);
                }else{
					this.apiPost('/api/student/instructor/geo-notifications/' + this.instructorId);
                }
			},
			componentHandlePostResponse(responseData) {
				this.isGeoNotificationsAllowed = true;
				this.updateBellImage()
			},
			componentHandleDeleteResponse(responseData) {
				this.isGeoNotificationsAllowed = false;
				this.updateBellImage()
			},
			updateBellImage(){
				this.bellImage = this.isGeoNotificationsAllowed ? '/images/icon-_bell-active.png' : '/images/icon-_bell.png'
				this.buttonTitle = this.isGeoNotificationsAllowed ? 'Disable Geo Notifications' : 'Enable Geo Notifications'
				this.bellOpacity = this.isGeoNotificationsAllowed ? 1 : 0.2;
            }
		},
		created : function(){
			this.isGeoNotificationsAllowed = this.geoNotificationsAllowed;
			this.updateBellImage()
		},
	}
</script>