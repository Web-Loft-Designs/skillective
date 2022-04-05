<template>
    <div class="profile-upcoming-lessons" v-if="listItems.length>0">
        <h2>Upcoming Virtual Lessons & Classes</h2>
        <table>
            <tr v-for="upcomingDate in listItems" >
                <td><strong>{{ upcomingDate.genre_title }}</strong></td>
                <td>{{ getLocationDate(upcomingDate) }}</td>
                <td><a :href="getViewLessonsLink(upcomingDate)">View lessons</a></td>
            </tr>
        </table>
        <a v-if="this.upcomingVirtualLessonsDates.length>0" @click="showMore()" class="btn btn-block btn-secondary">Show more</a>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : {
			upcomingDates : Array,
			instructorName : ''
		},
		data() {
			return {
                portion : 4,
                upcomingVirtualLessonsDates : []
			}
		},
        methods: {
			showMore(){
				var counter = 0;
				for (var l in this.upcomingVirtualLessonsDates){
					counter++;
				    this.listItems.push( _.cloneDeep(this.upcomingVirtualLessonsDates[l]) );
				    if (counter==this.portion || counter>=this.upcomingVirtualLessonsDates.length)
				    	break;
				}
				var countToRemove = this.upcomingVirtualLessonsDates.length>=this.portion ? this.portion : this.upcomingVirtualLessonsDates.length;
                this.upcomingVirtualLessonsDates.splice(this.upcomingVirtualLessonsDates.indexOf(this.upcomingVirtualLessonsDates[0]), countToRemove);
            },
			getViewLessonsLink(upcomingDate){
                return '/lessons?date_from=' + moment(upcomingDate.date_day_start).format('YYYY/MM/DD') + '&date_to=' + moment(upcomingDate.date_day_end).format('YYYY/MM/DD') + '&instructor_name=' + encodeURIComponent(this.instructorName) + '&genre=' + upcomingDate.genre_id
            },
			getLocationDate(upcomingDate){
                return moment(upcomingDate.date_day_start).format("MMM DD") + ( (upcomingDate.date_day_end!=upcomingDate.date_day_start) ? moment(upcomingDate.date_day_end).format("- MMM DD") : '') + ',' + moment(upcomingDate.date_day_start).format("YYYY");
			}
        },
		created: function(){
			this.upcomingVirtualLessonsDates = this.upcomingDates;
			this.showMore()
		}
	}
</script>