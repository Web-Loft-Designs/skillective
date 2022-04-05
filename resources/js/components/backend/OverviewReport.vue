<template>
    <div class="overview-component">
        <div class="overview-component-top">
            <h2>Overview</h2>
            <select v-model="selectedPeriod" @change="updateReportData()">
                <option value="-30 days">Past 30 days</option>
                <option value="-60 days">Past 60 days</option>
                <option value="-90 days">Past 90 days</option>
            </select>
        </div>
        <div class="overview-component-body">
            <table>
                <tr v-for="(item, index) in listItems">
                    <td>{{ item.paramName }}</td>
                    <td class="text-right">{{ item.currentValue }} <span :class="{ 'badge-plus': item.diff>0, 'badge-minus': item.diff<0 }">{{ item.diff>0 ? ('+'+item.diff) : item.diff }}%</span></td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';


	export default {
		mixins : [siteAPI, skillectiveHelper],
		props: ['reportData'],
		data() {
			return {
				selectedPeriod : '-30 days'
			}
		},
		methods: {
			updateReportData() {
				let queryParams = {};
				queryParams.period = this.selectedPeriod;

				this.apiGet('/api/admin/reports/overview', {
					params: queryParams
				});
			},
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data;
			}
		},
		created : function() {
			this.listItems = this.reportData;
		}
	}
</script>