<template>
    <div class="profile-upcoming-lessons" v-if="listItems.length>0">
        <h2>Upcoming locations</h2>
        <table>
            <tr v-for="upcomingLocation in listItems" >
                <td><strong>{{ getLocationName(upcomingLocation) }}</strong></td>
                <td>{{ getLocationDate(upcomingLocation) }}</td>
                <td><a :href="getViewLessonsLink(upcomingLocation)">View lessons</a></td>
            </tr>
        </table>
        <a v-if="this.upcomingProfileLocations.length>0" @click="showMore()" class="btn btn-block btn-secondary">Show more</a>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : {
			upcomingLocations : Array,
			instructorName : ''
		},
		data() {
			return {
                portion : 4,
                upcomingProfileLocations : []
			}
		},
        methods: {
			showMore(){
				var counter = 0;
				for (var l in this.upcomingProfileLocations){
					counter++;
				    this.listItems.push( _.cloneDeep(this.upcomingProfileLocations[l]) );
				    if (counter==this.portion || counter>=this.upcomingProfileLocations.length)
				    	break;
				}
				var countToRemove = this.upcomingProfileLocations.length>=this.portion ? this.portion : this.upcomingProfileLocations.length;
                this.upcomingProfileLocations.splice(this.upcomingProfileLocations.indexOf(this.upcomingProfileLocations[0]), countToRemove);
            },
			getViewLessonsLink(upcomingLocation){
                return '/lessons?date_from=' + moment(upcomingLocation.date_day_start).format('YYYY/MM/DD') + '&date_to=' + moment(upcomingLocation.date_day_end).format('YYYY/MM/DD') + '&instructor_name=' + encodeURIComponent(this.instructorName)
            },
			getLocationDate(upcomingLocation){
                return moment(upcomingLocation.date_day_start).format("MMM DD") + ( (upcomingLocation.date_day_end!=upcomingLocation.date_day_start) ? moment(upcomingLocation.date_day_end).format("- MMM DD") : '') + ',' + moment(upcomingLocation.date_day_start).format("YYYY");
			},
			getLocationName(upcomingLocation){
				return (upcomingLocation.city!=null ? (upcomingLocation.city + ',') : '') + ( upcomingLocation.state!=null ? upcomingLocation.state : '' );
			}
        },
		created: function(){
			this.upcomingProfileLocations = this.upcomingLocations;
			this.showMore()
		}
	}
</script>