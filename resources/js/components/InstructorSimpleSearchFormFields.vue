<template>
    
        <form @submit.prevent="onSubmit">
            <div id="instructor-simple-search-form-fields " class="tg--instructor-tab d-flex flex-wrap">
            <div class="form-group has-feedback genre-input" :class="{ 'has-error' : errors.genres }">
                <label>Genre</label>
                <select class="form-control" v-bind:class="{ 'select-empty': fields.genre === ''}" name="genre" v-model="fields.genre">
                    <option selected value="">Select Genre</option>
                    <option v-for="genre in siteGenres" :value='genre.id'>{{ genre.title }}</option>
                </select>
                <span class="help-block" v-if="errors.genre">
                        <strong>{{ errors.genre[0] }}</strong>
                 </span>
            </div>

            <div class="form-group has-feedback instagram-handle-input" :class="{ 'has-error' : errors.instagram_handle }">
                <label>Instructor Instagram Handle</label>
                <input type="text" class="form-control"  name="instagram_handle" value="" v-model="fields.instagram_handle" placeholder="@instagram_name">
                <span class="help-block" v-if="errors.instagram_handle">
                        <strong>{{ errors.instagram_handle[0] }}</strong>
                    </span>
            </div>

            <div class="form-group has-feedback instructor-name-input" :class="{ 'has-error' : errors.instructor_name }">
                <label>Instructor name</label>
                <input type="text" class="form-control" name="instructor_name" value="" v-model="fields.instructor_name" placeholder="">
                <span class="help-block" v-if="errors.instructor_name">
                    <strong>{{ errors.instructor_name[0] }}</strong>
                </span>
            </div>


            <div class="form-group has-feedback location-input" :class="{ 'has-error' : errors.location }">
                <label>Location</label>
                <span class="location-input-wrapper">
                    <input type="text" class="form-control" ref="instructorLocationFilter" id="instructor-location-filter" name="location" value="" v-model="fields.location" placeholder="Where are you going to practice?">
                    <span class="location-button" @click="geoLocationButton"></span>
                </span>
                <span class="help-block" v-if="errors.location">
                    <strong>{{ errors.location[0] }}</strong>
                </span>
            </div>

            <div class="form-group has-feedback time-input" :class="{ 'has-error' : errors.rate_from }">
                <label>Lesson Rates</label>
                <time-price :hidePlaceholder="true" :priceFromProp="fields.rate_from" :priceToProp="fields.rate_to" @changeTimeModel="test"></time-price>
                <span class="help-block" v-if="errors.rate_from">
                        <strong>{{ errors.rate_from[0] }}</strong>
                    </span>
            </div>

            <div v-if="errorText" class="has-error">{{ errorText }}</div>

            <div class="m-t-15 p-15">
                <input class="btn btn-block" type="submit" value="Search Instructors"/>
            </div>
             </div>
        </form>
   
</template>

<script>
	import skillectiveHelper from '../mixins/skillectiveHelper.js';
    import ClickOutside from 'vue-click-outside'

    export default {
		mixins : [skillectiveHelper],
        props : ['siteGenres', 'defaultLocation'],
        directives: {
            ClickOutside
        },
		data() {
			return {
				fields: {
					instructor_name   : '',
					instagram_handle  : '',
                    location : '',
                    rate_from : '',
                    rate_to : '',
                    genre : '',
                },
                errors : [],
				errorText : null,
			}
		},
		methods: {
            close: function () {

            },
            test: function(start) {
                this.fields.rate_from = start[0];
                this.fields.rate_to = start[1];
            },
            onSubmit() {
                this.mobileShowFilter = false;

                let queryParams = _.cloneDeep(this.fields);
                let searchParams = new URLSearchParams();
                for (var prop in queryParams){
                    if (queryParams.hasOwnProperty(prop))
                        searchParams.set(prop, queryParams[prop]);
                }
                window.location = '/instructors' + '?' + searchParams.toString();
            },
            geoLocationButton() {
                if (navigator.geolocation) {
                    var latlng = latlng = { lat: parseFloat(0), lng: parseFloat(0)};

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latlng = { lat: parseFloat(position.coords.latitude), lng: parseFloat(position.coords.longitude)}
                            let geocoder = new google.maps.Geocoder();


                            geocoder.geocode( { 'location': latlng}, (results, status) => {
                                if (status == 'OK') {
                                    var complited_address = '';
                                    results[0].address_components.map((item) => {
                                        if(item.types[0] == "locality") {
                                            complited_address =  item.short_name
                                        }

                                        if(item.types[0] == "administrative_area_level_1") {
                                            complited_address = complited_address + ', ' + item.short_name;
                                        }

                                        if(item.types[0] == "country") {
                                            complited_address = complited_address + ', ' + item.short_name;
                                        }
                                    })
                                    this.fields.location = complited_address
                                } else {
                                    alert('Geocode was not successful for the following reason: ' + status);
                                }
                            });
                        },
                        function(error){
                            console.log(error);
                            alert('Please try again');
                        }
                    );

                }
                else {
                    alert('geolocation not enabled');
                }

            },
            closeIt: function (e) {
                var el = document.getElementsByClassName("close");
                if (el.length > 0) {
                    el[0].click();
                }
            },
            reset: function() {
                this.fields.date_from = '';
                this.fields.date_to = '';
            },
            initNewPlacesAutocomplete(_ref){
                var thisComponent = this;
                var autocomplete = this.initializeLocationField( this.$refs[_ref], ['address'] );
                google.maps.event.addListener(autocomplete, 'place_changed', function (e) {
                    thisComponent.fields.location = thisComponent.$refs[_ref].value;
                });
            },
		},
		created: function(){
			this.timeOptions = this.getTimeOptions();
			this.fields.location = this.defaultLocation;
		},
        mounted: function(){
            this.initNewPlacesAutocomplete('instructorLocationFilter');
        }
	}
</script>