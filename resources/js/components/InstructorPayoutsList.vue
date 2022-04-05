<template>
    <div id="profile-payout-container" >
        <div class="tabele-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Payout Method</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="(payout, index) in listItems">
                    <td>{{ formatListDate(payout.payoutsPeriod) }}</td>
                    <td>ACH</td>
                    <td><strong>${{ parseFloat(Math.round(payout.totalPayoutsAmount * 100) / 100).toFixed(2) }}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="row justify-content-center" v-if="pagination.total_pages>1">
            <div class="col-6 select-show">
                <label>Per Page</label>
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
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';
	import Paginate from 'vuejs-paginate';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
            payouts: null,
            payoutsMeta: {}
        },
		components : {
			Paginate
		},
		data() {
			return {
                pagination : {
					total : 0,
                    total_pages : 0,
                    current_page : 0,
                    per_page : 0
                },
				monthShortNames : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
			}
		},
		methods: {
			getPeriodNameFromPeriodStartDate(periodDate){
				var d = new Date(periodDate);

                return d.getDay() + ' ' + this.monthShortNames[d.getMonth()] + ' ' + d.getFullYear();
			},
			getPayouts() {
				let queryParams = {};
				if (this.pagination.current_page != undefined)
					queryParams.page = this.pagination.current_page;
				else
					queryParams.page = 1;

				this.apiGet('/api/instructor/payouts', {
					params: queryParams
				});
			},
			componentHandlePostResponse(responseData){
				if (this.pagination.count == parseInt(responseData.data) && this.pagination.current_page>1){
					this.pagination.current_page -= 1;
				}
				this.getPayouts();
			},
			paginatorClickCallback(pageNum) {
				this.pagination.current_page = pageNum;
				this.getPayouts();
			},
			onChangePerPage() {
				this.pagination.current_page = 1;
				Cookies.set('instructorPayoutsPerPage', this.pagination.per_page);
				this.getPayouts();
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
            formatListDate(_date){
				return moment(_date, 'YYYY-MM-DD').format("DD MMM YYYY");
            }
		},
        created : function(){
			this.listItems = this.payouts;
			if (this.payoutsMeta!=undefined && this.payoutsMeta.pagination!=undefined){
				this.pagination.count           = this.payoutsMeta.pagination.count;
				this.pagination.total           = this.payoutsMeta.pagination.total;
				this.pagination.total_pages     = this.payoutsMeta.pagination.total_pages;
				this.pagination.current_page    = this.payoutsMeta.pagination.current_page;
				this.pagination.per_page        = this.payoutsMeta.pagination.per_page;
			}
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
			},
        }
	}
</script>