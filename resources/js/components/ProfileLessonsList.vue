<template>
    <div id="profile-lessons-container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Genre</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time from</th>
                        <th scope="col">Time to</th>
                        <th scope="col">Location</th>
                        <th scope="col">Count Spots</th>
                        <th scope="col">Spot Price</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(lesson, index) in listItems">
                        <td>{{ lesson.genre.title }}</td>
                        <td>{{ lesson.start | moment("MMMM D, YYYY") }}</td>
                        <td>{{ lesson.start | moment("h:mm a") }}</td>
                        <td>{{ lesson.end | moment("h:mm a") }}</td>
                        <td class="width-fix">
                            <div class="width-fix-content" v-if="lesson.lesson_type=='in_person'" v-html="lesson.location"></div>
                            <div class="width-fix-content" v-if="lesson.lesson_type=='virtual'">Virtual Lesson</div>
                        </td>
                        <td>{{ lesson.spots_count }}</td>
                        <td>{{ lesson.spot_price }}</td>
                        <td>
                            <span class="btn btn-default" @click="editLesson(lesson)">Edit</span>
                            <span class="btn btn-default" @click="cancelLesson(lesson)">Cancel Lesson</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props: ['lessons'],
		data() {
			return {
			}
		},
		methods: {
			cancelLesson(lesson){
				this.itemToDelete   = lesson;
                this.apiDelete('/api/lesson/' + lesson.id);
            },
			editLesson(lesson){
                console.log('emit.lessonUpdateInit');
				this.$root.$emit('lessonUpdateInit', lesson);
			},
			componentHandleDeleteResponse(responseData){
				if (this.itemToDelete!=null){
					this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
					this.itemToDelete = null;
				}
            }
		},
        created : function(){
			this.listItems = this.lessons;
        },
		mounted() {
			this.$root.$on('lessonCreated', (lesson) => {
				this.listItems.push(lesson);
			});
			this.$root.$on('lessonUpdated', (lesson) => {
				for ( var i=0; i<this.listItems.length; i++ ){
					if (this.listItems[i].id==lesson.id) {
						this.listItems.splice(i, 1, lesson);
					}
                }
			});
		}
	}
</script>