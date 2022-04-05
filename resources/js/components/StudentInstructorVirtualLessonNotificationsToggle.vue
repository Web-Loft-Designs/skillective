<template>
    <div class="toggle-favorite-container">
        <img class="icon-w-a" :style="{cursor : 'pointer', opacity : bellOpacity}" :src="bellImage" :class="{'geo-disabled':!isVirtualLessonNotificationsAllowed}" :alt="buttonTitle" :title="buttonTitle" @click.prevent="toggleVirtualLessonNotifications()">
        <div v-if="errorText" class="has-error" v-html="errorText"></div>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : {
			virtualLessonNotificationsAllowed : Boolean,
            instructorId: Number
		},
		data() {
			return {
				bellImage : '',
                buttonTitle : '',
				bellOpacity : 0,
				isVirtualLessonNotificationsAllowed : false
			}
		},
		methods: {
            toggleVirtualLessonNotifications(){
				if (this.isVirtualLessonNotificationsAllowed){
					this.apiDelete('/api/student/instructor/virtual-lesson-notifications/' + this.instructorId);
                }else{
					this.apiPost('/api/student/instructor/virtual-lesson-notifications/' + this.instructorId);
                }
			},
			componentHandlePostResponse(responseData) {
				this.isVirtualLessonNotificationsAllowed = true;
				this.updateBellImage()
			},
			componentHandleDeleteResponse(responseData) {
				this.isVirtualLessonNotificationsAllowed = false;
				this.updateBellImage()
			},
			updateBellImage(){
				this.bellImage = this.isVirtualLessonNotificationsAllowed ? '/images/icon-_bell-active.png' : '/images/icon-_bell.png'
				this.buttonTitle = this.isVirtualLessonNotificationsAllowed ? 'Disable Virtual Lessons Notifications' : 'Enable Virtual Lessons Notifications'
				this.bellOpacity = this.isVirtualLessonNotificationsAllowed ? 1 : 0.2;
            }
		},
		created : function(){
			this.isVirtualLessonNotificationsAllowed = this.virtualLessonNotificationsAllowed;
			this.updateBellImage()
		},
	}
</script>