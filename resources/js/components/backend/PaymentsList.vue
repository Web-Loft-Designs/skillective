<template>
    <div>
        <div class="users-table-header">
            <div class="table-top d-flex align-items-center">
                <h2>Payments</h2>

            </div>
            <div class="d-flex mt-5 responsive-mobile justify-content-between align-items-center">
                <div class="d-flex  responsive-mobile align-items-center">
                    <input type="text" v-model="searchString" placeholder="Search by Name"/>
                </div>
                <div>
                    <a href="#" @click.prevent="downloadExportedFile()" class="btn-green">Export CSV</a>
                </div>
            </div>


        </div>
        <div class="table-responsive">
            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

            <table class="table">
                <thead>
                <tr>

                    <th class="text-center">#</th>
                    <th class="cursor-pointer" @click.prevent="toggleSortBy('payer')"><span :class="{ 'active': sortBy=='payer', 'sort-up-down' : (sortBy=='payer' && sortOrder=='desc'), 'sort-up-up' : (sortBy=='payer' && sortOrder=='asc') }">Payer</span></th>
                    <th class="cursor-pointer" @click.prevent="toggleSortBy('recepient')"><span :class="{ 'active': sortBy=='recepient', 'sort-up-down' : (sortBy=='recepient' && sortOrder=='desc'), 'sort-up-up' : (sortBy=='recepient' && sortOrder=='asc') }">Recipient</span></th>
                    <th class="cursor-pointer" @click.prevent="toggleSortBy('date')"><span :class="{ 'active': sortBy=='date', 'sort-up-down' : (sortBy=='date' && sortOrder=='desc'), 'sort-up-up' : (sortBy=='date' && sortOrder=='asc') }">Date</span></th>
                    <!--<th  v-click-outside="closeIt" @click="openTriggerFunction"><span class="sort-dropdown active">-->
                        <!--Type-->
                        <!--<span class="dropdown-sort" v-if="openTrigger">-->
                            <!--<span class="dropdown-sort-body">-->
                                <!--<span class="form-group checkbox-wrapper">-->
                                    <!--<label for="lesson-booking">-->
                                        <!--<input id="lesson-booking" name="type" v-model="type" value="Lesson Booking" type="checkbox">-->
                                        <!--<span class="checkmark"></span>-->
                                        <!--Lesson Booking-->
                                    <!--</label>-->
                                <!--</span>-->
                                <!--<span class="form-group checkbox-wrapper">-->
                                    <!--<label for="submission">-->
                                        <!--<input id="submission" name="type" v-model="type" value="Submission" type="checkbox">-->
                                        <!--<span class="checkmark"></span>-->
                                        <!--Submission-->
                                    <!--</label>-->
                                <!--</span>-->
                                <!--<span class="form-group checkbox-wrapper">-->
                                    <!--<label for="other-type">-->
                                        <!--<input id="other-type" name="type" v-model="type" value="Other type" type="checkbox">-->
                                        <!--<span class="checkmark"></span>-->
                                        <!--Other type-->
                                    <!--</label>-->
                                <!--</span>-->

                            <!--</span>-->
                            <!--<span class="dropdown-sort-footer">-->
                                <!--<a href="#" class="btn-clear" @click.prevent="triggerTypeClear">Clear</a>-->
                                <!--<a href="#" class="btn-apply" @click.prevent="triggerType">Apply</a>-->
                            <!--</span>-->
                        <!--</span>-->
                    <!--</span></th>-->
                    <th class="cursor-pointer" @click.prevent="toggleSortBy('updated_at')"><span :class="{ 'active': sortBy=='updated_at', 'sort-up-down' : (sortBy=='updated_at' && sortOrder=='desc'), 'sort-up-up' : (sortBy=='updated_at' && sortOrder=='asc') }">Last update</span></th>
                    <th class="cursor-pointer" @click.prevent="toggleSortBy('status')"><span :class="{ 'active': sortBy=='status', 'sort-up-down' : (sortBy=='status' && sortOrder=='desc'), 'sort-up-up' : (sortBy=='status' && sortOrder=='asc') }">Status</span></th>
                    <th class="cursor-pointer">
                        <span class="sort-dropdown" :class="{'active': filter.price_to > 0}">
                            Amount
                            <span class="clear-button" @click="clearIt"></span>
                            <time-price :hidePlaceholder="true" :priceFromProp="filter.price_from" :priceToProp="filter.price_to" @changeTimeModel="triggerPrice"></time-price>
                        </span>
                    </th>
                    <th>
                        Total Fees
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in listItems">
                        <td class="text-center">{{ item.id }}</td>
                        <td>{{ item.student_name }}</td>
                        <td>{{ item.instructor_name }}</td>
                        <td>{{ item.transaction_created_at }}</td>
                        <!--<td>Lesson Booking</td>-->
                        <td>{{ item.updated_at }}</td>
                        <td><span>{{ item.status }}</span></td>
                        <td>${{ item.amount_to_pay.toFixed(2) }}</td>
                        <td>${{ item.fees_amount }}</td>
                        <td class="action">
                            <!--<a href="#"><img src="/images/img-arrow-bottom.png" alt=""></a>-->
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="listItems.length === 0">
                <p class="text-center">No payments</p>
            </div>

        </div>
        <div class="row justify-content-center" v-if="pagination.total_pages>1">
            <div class="col-6 select-show">
                <label>Show</label>
                <select v-model="pagination.per_page" @change="onChangePerPage">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center custom-pag">
                <span>{{ firstListItemNumber }}-{{ lastListItemNumber }} of {{ pagination.total }}</span>
                <paginate
                        :page-count="pagination.total_pages"
                        :force-page="pagination.current_page"
                        :prev-text="'Prev'"
                        :next-text="'Next'"
                        :click-handler="paginatorClickCallback"
                        :container-class="'clients-pagination'">
                </paginate>
            </div>
        </div>
    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';
    import Paginate from 'vuejs-paginate';
    import ClickOutside from 'vue-click-outside'
	var FileSaver = require('file-saver');


    export default {
		mixins : [siteAPI, skillectiveHelper],
        props: {
            payments: null,
            paymentsMeta: {}
        },
        directives: {
            ClickOutside
        },
        components : {
            Paginate
        },
		data() {
			return {
                listItems: [],
                searchString : '',
				showOnly: 'active',
                pagination : {
                    total : 0,
                    total_pages : 0,
                    current_page : 0,
                    per_page : 0
                },

                filter : {
					price_from : 0,
					price_to : 0
                },
                sortBy : '',
				sortOrder : '',
                type: [],
                openTrigger: false,
			}
		},
		methods: {
            openTriggerFunction() {
                this.openTrigger = true;
            },
            closeIt() {
                this.openTrigger = false;
            },
            clearIt() {
                this.filter.price_from = 0;
                this.filter.price_to = 0;
                this.getPayments();
            },
//            triggerTypeClear() {
//                this.openTrigger = false;
//                this.type = [];
//                this.apiPost('/api/admin/bookings', {type : this.type});
//            },
//			triggerType() {
//				this.openTrigger = false,
//					this.apiPost('/api/admin/bookings', {type : this.type});
//			},
			toggleSortBy(field) {
            	if (this.sortBy == field){
					this.sortOrder = (this.sortOrder=='asc') ? 'desc' : 'asc';
                }else{
                    this.sortBy = field;
					this.sortOrder = 'asc';
				}
				this.getPayments();
            },
            triggerPrice(price) {
                this.filter.price_from = price[0];
                this.filter.price_to = price[1];
				this.getPayments();
            },
			toggleShowOnly(status){
				this.showOnly = status;
				this.pagination.current_page = 1;
				this.getPayments();
			},
			paginatorClickCallback(pageNum) {
				this.pagination.current_page = pageNum;
				this.getPayments();
			},
			onChangePerPage() {
				this.pagination.current_page = 1;
				Cookies.set('adminBookingsPerPage', this.pagination.per_page);
				this.getPayments();
			},
			searchPayments(){
				this.pagination.current_page = 1;
				this.getPayments();
			},
			getPayments() {
				let queryParams = {};

				queryParams.status = this.showOnly;

				if (this.pagination.current_page != undefined)
					queryParams.page = this.pagination.current_page;
				else
					queryParams.page = 1;

				if (this.searchString!='')
					queryParams.s = this.searchString;

				if (this.sortBy!='') {
					queryParams.order = this.sortBy;
					if (this.sortOrder!='' && (this.sortOrder=='asc' || this.sortOrder=='desc'))
						queryParams.sort = this.sortOrder;
					else {
						this.sortOrder = 'asc'
						queryParams.sort = this.sortOrder;
					}
				}

				for (var _prop in this.filter){
//					console.log(_prop);
					if (this.filter.hasOwnProperty(_prop) && this.filter[_prop]!='') {
//						console.log(this.filter[_prop]);
						queryParams[_prop] = this.filter[_prop];
					}
                }
//                console.log(queryParams)
				this.updateUrlQueryParams(queryParams);

//				this.apiGet('/api/admin/bookings' + window.location.search, this.fields);

				this.apiGet('/api/admin/bookings', {
					params: queryParams
				});
			},
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data.data;
				if (responseData.data.meta!=undefined && responseData.data.meta.pagination!=undefined){
					this.pagination.count           = responseData.data.meta.pagination.count;
					this.pagination.total           = responseData.data.meta.pagination.total;
					this.pagination.total_pages     = responseData.data.meta.pagination.total_pages;
					this.pagination.current_page    = responseData.data.meta.pagination.current_page;
					this.pagination.per_page        = responseData.data.meta.pagination.per_page;
				}
			},
			downloadExportedFile:  function(){
				if (this.loader==null){
					this.loader = this.$loading.show({
						zIndex: 9999999,
					});
				}

				return axios.get('/api/admin/bookings/payments/export' + window.location.search, {
					responseType: 'blob',
				}).then(response => {
					FileSaver.saveAs(response.data, 'payments-list.csv')
					this.loader.hide();
					this.loader = null;
				}).catch(error => {
					this.loader.hide();
					this.loader = null;
				});
			}
		},
        created : function(){
            this.listItems = this.payments;
			if (this.paymentsMeta!=undefined && this.paymentsMeta.pagination!=undefined){
				this.pagination.count           = this.paymentsMeta.pagination.count;
				this.pagination.total           = this.paymentsMeta.pagination.total;
				this.pagination.total_pages     = this.paymentsMeta.pagination.total_pages;
				this.pagination.current_page    = this.paymentsMeta.pagination.current_page;
				this.pagination.per_page        = this.paymentsMeta.pagination.per_page;
			}
			if (this.getUrlParameter('s'))
				this.searchString = this.getUrlParameter('s');
			if (this.getUrlParameter('order'))
				this.sortBy = this.getUrlParameter('order');
			if (this.getUrlParameter('sort'))
				this.sortOrder = this.getUrlParameter('sort');

			for (var prop in this.filter){
				if (this.filter.hasOwnProperty(prop) && this.getUrlParameter(prop)!='')
					this.filter[prop] = this.getUrlParameter(prop);
			}

			this.debouncedGetPayments = _.debounce(this.searchPayments, 500)
        },
		watch: {
			searchString: function (newSearchString, oldSearchString) {
				this.debouncedGetPayments()
			},
		},
        computed : {
            firstListItemNumber : function() {
                return this.pagination.current_page*this.pagination.per_page - this.pagination.per_page + 1;
            },
            lastListItemNumber : function() {
                if ( this.pagination.count == this.pagination.per_page ){
                    return this.firstListItemNumber + this.pagination.per_page - 1;
                }else{
                    return this.firstListItemNumber + this.pagination.count - 1;
                }
            }
        }
	}
</script>