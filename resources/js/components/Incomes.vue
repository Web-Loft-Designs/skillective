<template>
    <div class="row">
        <div class="col-12">
            <h2 class="page-title">Incomes</h2>
            <div class="breadcrumb-custom">
                    <a href="#" @click.prevent="changePeriod('')" :class="{ 'active' : (''==selectedYear) }">All Time</a>
                    <a href="#" v-for="year in yearOptions" @click.prevent="changePeriod(year)" :class="{ 'active' : (year==selectedYear) }"> / {{ year }}</a>
            </div>
            <chart-dashboard-2
                    :initial-goal-value="initialGoalValue"
                    :initial-goal-color="initialGoalColor"
                    :year-of-registration="yearOfRegistration"
                    :date-chart="incomes"
                    :is-dashboard="false"
            ></chart-dashboard-2>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-custom">
                    <tr>
                        <th>Date</th>
                        <th>Lessons booked</th>
                        <th>Lessons Completed</th>
                        <th>Pre-recorded Lesson Purchases</th>
                        <th>Earnings</th>
                    </tr>

                    <tr v-for="(periodIncome, index) in incomes">
                        <td><span>{{ getPeriodNameFromPeriodStartDate(periodIncome.startDate) }}</span></td>
                        <td>{{ periodIncome.countBookedLessons }}</td>
                        <td>{{ periodIncome.countHeldLessons }}</td>
                        <td>{{ periodIncome.preRPurchases }}</td>
                        <td><strong>{{ periodIncome.amountEarned>0?('$' + periodIncome.amountEarned) : '-' }}</strong></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import skillectiveHelper from '../mixins/skillectiveHelper.js';

	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: {
			yearOfRegistration: null,
		    initialGoalValue : null,
            initialGoalColor : null
        },
		data() {
			return {
				incomes: [],
                selectedYear : new Date().getFullYear(),
                yearOptions : []
			}
		},
		methods: {
			getIncomes() {
				var _api = '/api/instructor/incomes';
				if (this.selectedYear!='')
					_api += ('/' + this.selectedYear);
				this.apiGet(_api);
			},
			componentHandleGetResponse(responseData) {
                this.incomes = [];
				this.incomes = responseData.data;
			},
			buildYearOptions(){
				var currentYear = new Date().getFullYear();
				for(var y = currentYear; y>=parseInt(this.yearOfRegistration); y--){
					this.yearOptions.push(y)
                }
            },
            getPeriodNameFromPeriodStartDate(periodDate){
				if (this.selectedYear=='')
					return moment(periodDate, "YYYY-MM-DD").format('YYYY');
                else
					return moment(periodDate, "YYYY-MM-DD").format('MMMM');
            },
			changePeriod(period){
				this.selectedYear = period;
            }
		},
        created : function(){
			this.buildYearOptions();
			this.getIncomes();
        },
		watch: {
			selectedYear: function (newVal, oldVal) {
				this.getIncomes();
			}
		}
	}
</script>