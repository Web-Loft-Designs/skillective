<template>
    <div class="toggle-favorite-container">
        <img class="icon-w-a" style="cursor:pointer;" src="/images/star.svg" :class="{'not-favorite':!isFavoriteInstructor}" :alt="buttonTitle" :title="buttonTitle" @click.prevent="toggleFavorite()">
        <div v-if="errorText" class="has-error" v-html="errorText"></div>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : {
			isFavorite : Boolean,
            instructorId: Number
		},
		data() {
			return {
				starImage : '',
                buttonTitle : '',
				isFavoriteInstructor : false
			}
		},
		methods: {
			toggleFavorite(){
				if (this.isFavoriteInstructor){
					this.apiDelete('/api/student/instructor/favorite/' + this.instructorId);
                }else{
					this.apiPost('/api/student/instructor/favorite/' + this.instructorId);
                }
			},
			componentHandlePostResponse(responseData) {
				this.isFavoriteInstructor = true;
				this.updateStarImage()
			},
			componentHandleDeleteResponse(responseData) {
				this.isFavoriteInstructor = false;
				this.updateStarImage()
			},
			updateStarImage(){
				this.starImage = this.isFavoriteInstructor ? '' : '/images/star-inactive.svg'
				this.buttonTitle = this.isFavoriteInstructor ? 'Remove from favorites' : 'Add to favorites'
            }
		},
		created : function(){
			this.isFavoriteInstructor = this.isFavorite;
			this.updateStarImage()
		},
	}
</script>