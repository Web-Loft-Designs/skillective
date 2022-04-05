<template>
    <div id="form-benefits-list-container">
        <label>Benefits in Block with a Form</label>
        <div class="d-flex flex-wrap mb-4" v-for="(benefit, index) in listItems">
            <div class="col-12 p-0 form-group has-feedback">
                <input type="text" class="form-control" :name="'page_meta['+meta_name+'][' + index  + '][title]'" v-model="benefit['title']" placeholder="Title"/>
            </div>
            <div class="col-12 p-0">
                <div class="field field-files field-images">
                      <span class="wrapper-file-input">
                          <span class="input-file">
                                  <span class="name" v-if="benefit._image_benefit!=null"> <img :src="benefit['_image_benefit']" /> {{benefit['_image_benefit']}}</span>
                                  <span class="name" v-else></span>
                        </span>
                        <input :id="meta_name_id + '-image-' + index" type="file" :name="'page_meta['+meta_name+'][' + index + '][' + image_field_name + ']'" title="Browse" >
                       </span>
                    <!--<label :for="meta_name_id + '-image-' + index"></label>-->
                </div>
                <!--<div class="field field-files field-images">-->
                    <!---->
                    <!--<input :id="meta_name_id + '-image-' + index" type="file" :name="'page_meta['+meta_name+'][' + index + '][' + image_field_name + ']'" title="Browse" >-->
                    <!--<label :for="meta_name_id + '-image-' + index"></label>-->
                <!--</div>-->
                <div v-if="benefit._image_benefit!=null">
                    <input type="hidden" :name="'page_meta['+meta_name+'][' + index + '][_current' + image_field_name + ']'" :value="benefit['_image_benefit']">
                    <div class="row-uploads">
                        <!--<div class="uploads">-->
                            <!--<img :src="benefit['_image_benefit']" />-->
                        <!--</div>-->
                        <div class="field field-checkbox">
                            <input type="checkbox" value="1" :name="'page_meta['+meta_name+'][' + index + '][_remove' + image_field_name + ']'" :id="'remove-'+meta_name+'-image-checkbox-'+index"/>
                            <label :for="'remove-'+meta_name+'-image-checkbox-'+index">Remove Uploaded Image</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 p-0">
                <input type="button" class="btn btn-danger" @click="deleteRow(benefit)" value="Remove Item">
            </div>
        </div>
        <div class="d-flex flex-wrap">
            <div class="col-12 p-0">
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
					_image_benefit : null
				});
			},
			deleteRow(rowItem){
                this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
                this.itemToDelete = null;
			},
		},
		created : function() {
			this.listItems = this.benefits;
			this.meta_name = 'form_benefits'
			this.meta_name_id = 'form-benefits'
			this.image_field_name = '_image_benefit';
		}
	}
</script>