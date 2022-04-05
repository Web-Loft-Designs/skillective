<template>
    <div id="how-this-works-list-container">
        <label>Steps</label>
        <div class="row" v-for="(listBlock, index) in listItems">
            <div class="col-sm-3">
                <input type="text" class="form-control" :name="'page_meta['+meta_name+'][' + index  + '][title]'" v-model="listBlock['title']" placeholder="Title"/>
            </div>
            <div class="col-sm-3">
                <textarea class="form-control" :name="'page_meta['+meta_name+'][' + index  + '][text]'" v-model="listBlock['text']" placeholder="Description"></textarea>
            </div>
            <div class="col-sm-4">
                <div class="field field-files field-images">
                    <input required :id="meta_name_id + '-image-' + index" type="file" :name="'page_meta['+meta_name+'][' + index + '][' + image_field_name + ']'" title="Browse" >
                    <label :for="meta_name_id + '-image-' + index"></label>
                </div>
                <div v-if="listBlock._image_block!=null">
                    <input type="hidden" :name="'page_meta['+meta_name+'][' + index + '][_current' + image_field_name + ']'" :value="listBlock['_image_block']">
                    <div class="row row-uploads">
                        <div class="uploads">
                            <img :src="listBlock['_image_block']" />
                        </div>
                        <div class="field field-checkbox">
                            <input type="checkbox" value="1" :name="'page_meta['+meta_name+'][' + index + '][_remove' + image_field_name + ']'" :id="'remove-'+meta_name+'-image-checkbox-'+index"/>
                            <label :for="'remove-'+meta_name+'-image-checkbox-'+index">Remove Uploaded Image</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <input type="button" class="btn btn-danger" @click="deleteRow(listBlock)" value="Remove Item">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <input type="button" class="btn btn-default" @click="addRow()" value="Add New Item">
            </div>
        </div>
    </div>
</template>

<script>
	export default {
		props: ['howThisWorks'],
		data() {
			return {
				listItems : []
			}
		},
		methods: {
			addRow() {

				this.listItems.push({
					title : null,
					text : null,
					_image_block : null
				});
			},
			deleteRow(rowItem){
                this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
                this.itemToDelete = null;
			},
		},
		created : function() {
			this.listItems = this.howThisWorks;
			this.meta_name = 'how_this_works_blocks'
			this.meta_name_id = 'how-this-works-blocks'
			this.image_field_name = '_image_block';
		}
	}
</script>