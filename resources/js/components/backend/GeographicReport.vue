<template>
    <div>
        <table>
            <tr>
                <th>State</th>
                <th>Percent</th>
                <th>Users number</th>
            </tr>
            <tr v-for="(item, index) in listItems">
                <td><strong>{{ item.stateName }}</strong></td>
                <td>{{ item.percent }}%</td>
                <td>{{ item.count }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td colspan="3">{{ totalUsers }}</td>
            </tr>
        </table>
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
				totalUsers : 0
			}
		},
		methods: {
		},
		created : function() {
			this.listItems = this.reportData;
			for (var _index in this.listItems){
				if (this.listItems.hasOwnProperty(_index)){
					this.totalUsers += this.listItems[_index].count;
                }
            }
		}
	}
</script>