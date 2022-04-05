<template>
    <div>
        <table class="w-100">
            <tr>
                <th>Param name</th>
                <th>Value</th>
            </tr>
            <tr v-for="(item, index) in listItems">
                <td><strong>{{ item.paramName }}</strong></td>
                <td>{{ item.value }}</td>
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
			}
		},
		methods: {
		},
		created : function() {
			this.listItems = this.reportData;
		}
	}
</script>