<template>
    <div id="instructors-profiles-container" class="container-result" v-bind:class="{'open-map-wrapper': mapActive && listItems.length!==0 }">
        <div id="list-search-form-fields" class="top-filter"  v-bind:class="{'mobile-show': mobileShowFilter }" >
            <!-- <button @click="mobileShowFilter1()" type="button" class="mobile-show-filter"></button> -->
            <form @submit.prevent="onSubmit">
            <div class="row">

                <div class="form-group has-feedback genre-input" :class="{ 'has-error' : errors.genres }">
                    <label>Genre</label>
                    <select class="form-control" v-bind:class="{ 'select-empty': fields.genre === ''}" name="genre" v-model="fields.genre">
                        <option selected value=""></option>
                        <option v-for="genre in siteGenres" :value='genre.id'>{{ genre.title }}</option>
                    </select>
                    <span class="help-block" v-if="errors.genre">
                        <strong>{{ errors.genre[0] }}</strong>
                    </span>
                </div>

                <div class="form-group has-feedback location-input" :class="{ 'has-error' : errors.location }">
                    <label>Location</label>
                    <span class="location-input-wrapper ">
                    <input type="text" class="form-control" name="location" v-model="fields.location" ref="instructorLocationFilter">
                         <span class="location-button" @click="geoLocationButton"></span>
                    </span>
                    <span class="help-block" v-if="errors.location">
                        <strong>{{ errors.location[0] }}</strong>
                    </span>
                </div>

                <div class="form-group has-feedback instagram-handle-input" :class="{ 'has-error' : errors.instagram_handle }">
                    <label>Instagram Handle</label>
                    <input type="text" class="form-control"  name="instagram_handle" value="" v-model="fields.instagram_handle" >
                    <span class="help-block" v-if="errors.instagram_handle">
                        <strong>{{ errors.instagram_handle[0] }}</strong>
                    </span>
                </div>

                <div class="form-group has-feedback instructor-name-input" :class="{ 'has-error' : errors.instructor_name }">
                    <label>Instructor name</label>
                    <input type="text" class="form-control" name="instructor_name" value="" v-model="fields.instructor_name" >
                    <span class="help-block" v-if="errors.instructor_name">
                        <strong>{{ errors.instructor_name[0] }}</strong>
                    </span>
                </div>

                <div class="form-group has-feedback time-input" :class="{ 'has-error' : errors.rate_from }">
                    <label>Lesson Rates</label>
                    <time-price :hidePlaceholder="true" :priceFromProp="fields.rate_from" :priceToProp="fields.rate_to" @changeTimeModel="test"></time-price>
                    <span class="help-block" v-if="errors.rate_from">
                    <strong>{{ errors.rate_from[0] }}</strong>
                </span>
                </div>

                <div class="form-group">
                    <input type="submit" value="Search Instructors"/>
                </div>
                <div v-if="errorText" class="has-error">{{ errorText }}</div>
            </div>
            </form>
        </div>

        <transition name="slidein">
            <div class="result-wrapper-map" v-show='mapActive && listItems.length!==0'>
                <instructors-google-map-single
                                :dataid="'search'"
                                :center="{
                                    lat: 10,
                                    lng:10
                                }"
                                :hoverid="hoverID"
                                :marker="markers"
                                @infoWindowOpen="triggerOpen"
                                @infoWindowClose="triggerClose"
                ></instructors-google-map-single>
            </div>
        </transition>
        <div>

            <div class="top-sort-wrapper" v-show='listItems.length!==0'>
                <div class="sort-wrapper">
                    <form>
                    <label>Sort by</label>
                    <select v-model="sortOrder" @change="onChangeOrder">
                        <option value="min_rate_asc">Lowest lesson rate</option>
                        <option value="min_rate_desc">Highest lesson rate</option>
                    </select>
                    </form>
                </div>
                <div class="map-switch">
                    <p>Map</p>
                    <label @click="mapActive = !mapActive" v-bind:class="{'open-map': mapActive }" class="switch-label">
                        <span v-if="!mapActive">Hide</span>
                        <span v-else>Show</span>
                    </label>
                </div>
            </div>

            <div class="result-wrapper-items">
                <div class="item"  v-for="(instructor, index) in listItems" :key="'item-' + index">
                    <div class="item-wrapper" 
                        :data-id="instructor.id" 
                        @mouseover="hoverTrigger(instructor.id)" 
                        v-click-outside="unHoverTrigger"
                        @click.stop="hoverTrigger(lesson.id)"
                    >
                        
                        <div class="item-body">
                            <!-- <a target="_blank" :href="'/profile/' + instructor.id"> -->
                            <!--<span class="avatar" :style="'background-image: url('+instructor.profile.image+')'">-->
                                <span class="avatar" >
                                    <a target="_blank" :href="'/profile/' + instructor.id" class="profile-link">
                                        <img :src="instructor.profile.image" alt="">
                                        <span>View<br> instructor</span>
                                    </a>
                                </span>
                            </a>
                            <a class="insta" v-if="instructor.profile.instagram_handle!=null" :href="'https://www.instagram.com/' + instructor.profile.instagram_handle" target="_blank"><strong>@{{ instructor.profile.instagram_handle }}</strong></a>
                            <span><a :href="'/profile/'+instructor.id" class="link-to-profile">{{ instructor.full_name }}</a></span>
                        </div>
                        <div class="item-footer">
                            <div class="left">
                                <p><strong>{{ instructor.full_name }}</strong></p>
                            </div>
                            <div class="right">
                                <p v-if="instructor.min_rate>0"><strong>${{ instructor.min_rate!=instructor.max_rate ? instructor.min_rate +' - $'+ instructor.max_rate : instructor.min_rate }}</strong>lesson rates</p>
                            </div>
                            <div class="full">
                                <a :href="'/profile/'+instructor.id" class="btn btn-block">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="listItems.length==0" class="col-12 p-5 mt-5"><h3 class="text-center">Nothing found</h3></div>

            </div>

            <div class="lessons-pagination-outer">
                <paginate
                        v-if="pagination.total_pages>1 && listItems.length>0"
                        v-model="pagination.current_page"
                        :page-count="pagination.total_pages"
                        :force-page="pagination.current_page"
                        :prev-text="'Prev'"
                        :next-text="'Next'"
                        :click-handler="paginatorClickCallback"
                        :container-class="'lessons-pagination'">
                </paginate>
            </div>
        </div>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';
    import Paginate from 'vuejs-paginate';
    import ClickOutside from 'vue-click-outside'
    import $ from 'jquery'

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
			instructors: null,
			instructorsMeta: {},
		    siteGenres: null,
			currentUserCanBook : false
        },
        directives: {
            ClickOutside
        },
		components : {
			// VueAutonumeric,
			Paginate
		},
		data() {
			return {
				listMeta: [],
				sortOrder : 'min_rate_asc',
                mapActive: true,
                mobileShowFilter: false,
                markers: [],
                hoderFlag: false,
                hoverID: null,
				fields: {
					instructor_name   : '',
					instagram_handle  : '',
					location : '',
					rate_from : '',
					rate_to : '',
					genre : '',
				},
                pagination : {
					count : 0,
					total : 0,
                    total_pages : 0,
                    current_page : 0
                },
				errors : [],
				errorText : null,
                lessonTypes : []
			}
		},
		methods: {
            triggerClose() {
                $('.item-wrapper').removeClass('active');
            },
            triggerOpen(id) {
                $([document.documentElement, document.body]).animate({
                    scrollTop: $('[data-id='+id+']').offset().top - 200
                }, 100);
                $('.item-wrapper').removeClass('active');
                $('[data-id='+id+']').addClass('active');
            },
            test: function(start) {
                this.fields.rate_from = start[0];
                this.fields.rate_to = start[1];
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
            mobileShowFilter1() {

                this.mobileShowFilter = !this.mobileShowFilter;
                if(this.mobileShowFilter) {
                    $('body').addClass('filter-active');
                } else {
                    $('body').removeClass('filter-active');
                }
                $('#app-navbar-collapse').collapse('hide');
                // $('#app-navbar-collapse').removeClass('show');
                // $('#app-navbar-collapse').removeClass('in');
                // $('#app-navbar-collapse').attr('aria-expanded',false);
                // $('.navbar-toggle').addClass('collapsed').attr('aria-expanded',false);
            },
			onSubmit() {
			    this.mobileShowFilter = false;

                let queryParams = _.cloneDeep(this.fields);
				let separatorIndex = this.sortOrder.lastIndexOf('_');
                if (separatorIndex){
					let _sortedBy = this.sortOrder.substring(this.sortOrder.lastIndexOf('_')+1);
					if (_sortedBy=='asc' || _sortedBy=='desc'){
						queryParams.sortedBy = _sortedBy;
						queryParams.orderBy = this.sortOrder.substring(0, separatorIndex);
                    }
                }
                if (this.pagination.current_page != undefined){
					queryParams.page = this.pagination.current_page;
                }
				this.updateUrlQueryParams(queryParams);

                this.apiGet('/api/instructors/search' + window.location.search, this.fields);
			},
            hoverTrigger(id) {
                $('.item-wrapper').removeClass('active');
                this.hoverID = id;
            },
            unHoverTrigger() {
                $('.item-wrapper').removeClass('active');
                this.hoverID = null;
            },
			onChangeOrder() {
                this.onSubmit();
            },
			paginatorClickCallback(pageNum) {
				this.onSubmit();
            },
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data.data;
				if (responseData.data.meta!=undefined && responseData.data.meta.pagination!=undefined){
                    this.pagination.count = responseData.data.meta.pagination.count;
                    this.pagination.total = responseData.data.meta.pagination.total;
                    this.pagination.total_pages = responseData.data.meta.pagination.total_pages;
                    this.pagination.current_page = responseData.data.meta.pagination.current_page;
                }
                this.markers = [];
                this.listItems.forEach(item => {
                    this.markers.push({
                        position: {
                            latitude: item.profile.lat,
                            longitude: item.profile.lng
                        },
                        content: item,
                    })
                })
			},
            initNewPlacesAutocomplete(_ref){
                var thisComponent = this;
                var autocomplete = this.initializeLocationField( this.$refs[_ref], ['address'] );
                google.maps.event.addListener(autocomplete, 'place_changed', function (e) {
                    thisComponent.fields.location = thisComponent.$refs[_ref].value;
                });
            },
		},
        created : function(){
			this.listItems = this.instructors;
			if (this.instructorsMeta!=undefined && this.instructorsMeta.pagination!=undefined){
				this.pagination.count = this.instructorsMeta.pagination.count;
				this.pagination.total = this.instructorsMeta.pagination.total;
				this.pagination.total_pages = this.instructorsMeta.pagination.total_pages;
				this.pagination.current_page = this.instructorsMeta.pagination.current_page;
			}

            this.markers = [];

			if (this.getUrlParameter('orderBy') && this.getUrlParameter('sortedBy')){
				this.sortOrder = (this.getUrlParameter('orderBy') + '_' + this.getUrlParameter('sortedBy')).toLowerCase();
            }
            if (this.getUrlParameter('genre'))
                this.fields.genre = this.getUrlParameter('genre');
            if (this.getUrlParameter('instagram_handle'))
                this.fields.instagram_handle = this.getUrlParameter('instagram_handle');
            if (this.getUrlParameter('instructor_name'))
                this.fields.instructor_name = this.getUrlParameter('instructor_name');
            if (this.getUrlParameter('location'))
                this.fields.location = this.getUrlParameter('location');
            if (this.getUrlParameter('rate_from'))
                this.fields.rate_from = this.getUrlParameter('rate_from');
            if (this.getUrlParameter('rate_to'))
                this.fields.rate_to = this.getUrlParameter('rate_to');
        },
		mounted() {
            this.initNewPlacesAutocomplete('instructorLocationFilter');

            this.instructors.forEach(item => {
                if (item.profile.lat)
                    this.markers.push({
                        position: {
                            latitude: item.profile.lat,
                            longitude: item.profile.lng
                        },
                        content: item,
                    })
            })
		}
	}
</script>