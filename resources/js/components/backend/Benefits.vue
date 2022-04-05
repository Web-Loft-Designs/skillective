<template>
    <div id="benefits-list-container">
        <label>Benefits</label>
        <div class="d-flex flex-wrap " v-for="(benefit, index) in listItems">
            <div class="col-12 form-group p-0 has-feedback">
                <input type="text" class="form-control" :name="'page_meta[benefits][' + index  + '][title]'" v-model="benefit['title']" placeholder="Title"/>
            </div>
            <div class="col-12 form-group p-0">
                <textarea class="form-control" :name="'page_meta[benefits][' + index  + '][text]'" v-model="benefit['text']" placeholder="Description"></textarea>
            </div>
            <div class="col-12 form-group p-0">
                <input type="button" class="btn btn-danger" @click="deleteRow(benefit)" value="Remove Item">
            </div>
        </div>
        <div class="d-flex flex-wrap">
            <div class="col-12 form-group p-0">
                <input type="button" class="btn btn-default" @click="addRow()" value="Add New Item">
            </div>
        </div>
    </div>
</template>

<script>
	export default {
		props: ['benefits'],
		data() {
			return {
				listItems : []
			}
		},
		methods: {
			addRow() {

				this.listItems.push({
					title : null,
					text : null
				});
			},
			deleteRow(rowItem){
                this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
                this.itemToDelete = null;
			},
		},
		created : function() {
			this.listItems = this.benefits;
		}
	}
</script>