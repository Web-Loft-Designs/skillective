<template>
    <div id="booking-steps-list-container">
        <label>Steps</label>
        <div class="d-flex flex-wrap mb-4" v-for="(booking_step, index) in listItems">
            <div class="col-12 form-group has-feedback">
                <input type="text" class="form-control" :name="'page_meta['+meta_name+'][' + index  + '][title]'" v-model="booking_step['title']" placeholder="Title"/>
            </div>
            <div class="col-12">
                <div class="field field-files field-images">
                      <span class="wrapper-file-input">
                          <span class="input-file">
                                  <span class="name" v-if="booking_step._image_step!=null"> <img :src="booking_step['_image_step']" /> {{booking_step['_image_step']}}</span>
                                  <span class="name" v-else></span>
                        </span>
                        <input :id="meta_name_id + '-image-' + index" type="file" :name="'page_meta['+meta_name+'][' + index + '][' + image_field_name + ']'" title="Browse" >
                       </span>
                    <!--<label :for="meta_name_id + '-image-' + index"></label>-->
                </div>
                <div v-if="booking_step._image_step!=null">
                    <input type="hidden" :name="'page_meta['+meta_name+'][' + index + '][_current' + image_field_name + ']'" :value="booking_step['_image_step']">
                    <div class="row-uploads">
                        <!--<div class="uploads">-->
                            <!--<img :src="booking_step['_image_step']" />-->
                        <!--</div>-->
                        <div class="field field-checkbox">
                            <input type="checkbox" value="1" :name="'page_meta['+meta_name+'][' + index + '][_remove' + image_field_name + ']'" :id="'remove-'+meta_name+'-image-checkbox-'+index"/>
                            <label :for="'remove-'+meta_name+'-image-checkbox-'+index">Remove Uploaded Image</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <input type="button" class="btn btn-danger" @click="deleteRow(booking_step)" value="Remove Item">
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
		props: ['bookingSteps'],
		data() {
			return {
				listItems : []
			}
		},
		methods: {
			addRow() {

				this.listItems.push({
					title : null,
					_image_step : null
				});
			},
			deleteRow(rowItem){
                this.listItems.splice(this.listItems.indexOf(this.itemToDelete), 1);
                this.itemToDelete = null;
			},
		},
		created : function() {
			this.listItems = this.bookingSteps;
			this.meta_name = 'booking_steps'
			this.meta_name_id = 'booking-steps'
			this.image_field_name = '_image_step';
		}
	}
</script>