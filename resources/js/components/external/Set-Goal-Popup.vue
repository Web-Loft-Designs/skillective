<template>
       <div>
           <h2>Set a Goal</h2>
           <div>
               <div class="form-group">
                   <label>Value</label>
                   <div class="d-flex">
                       <span class="dollar-wrapper">
                           <input type="number" @keypress="checknumber"  step="1" min="0" class="form-control" v-model="fields.goal_value">
                       </span>
                       <!--<span class="per-lesson">-->
                           <!--<select>-->
                               <!--<option value="month">per month</option>-->
                               <!--<option value="year">per year</option>-->
                               <!--<option value="lesson">per lesson</option>-->
                           <!--</select>-->

                       <!--</span>-->
                   </div>
                   <span class="help-block has-error" v-if="errors.goal_value">
                           {{ errors.goal_value[0] }}
                   </span>
               </div>

               <div class="form-group">
                   <label>Description</label>
                   <input type="text" class="form-control" v-model="fields.goal_description">
               </div>
               <div class="form-group">
                   <label>Color</label>
                   <div class="d-flex">
                       <div class="color-radio">
                           <label>
                               <input checked type="radio" name="color" v-model="fields.goal_color" value="#AAAAAA">
                               <span class="checkmark"></span>
                           </label>
                       </div>
                       <div class="color-radio yellow">
                           <label>
                               <input type="radio" name="color" v-model="fields.goal_color" value="#DFAC3F">
                               <span class="checkmark"></span>
                           </label>
                       </div>
                       <div class="color-radio green">
                           <label>
                               <input type="radio" name="color" v-model="fields.goal_color" value="#9AD73E">
                               <span class="checkmark"></span>
                           </label>
                       </div>
                       <div class="color-radio blue">
                           <label>
                               <input type="radio" name="color" v-model="fields.goal_color" value="#6C88DD">
                               <span class="checkmark"></span>
                           </label>
                       </div>
                       <div class="color-radio red">
                           <label>
                               <input type="radio" name="color" v-model="fields.goal_color" value="#BD4B40">
                               <span class="checkmark"></span>
                           </label>
                       </div>
                   </div>
                   <span class="help-block has-error" v-if="errors.goal_color">
                      {{ errors.goal_color[0] }}
                   </span>
               </div>
               <div class="col-12">
                   <div v-if="errorText" class="has-error">{{ errorText }}</div>
                   <div v-if="successText" class="has-success">{{ successText }}</div>
               </div>
               <div class="form-group d-flex">

                   <a href="#" class="btn btn-green" @click.prevent="updateGoal()">{{submitButtonText}}</a>
                   <a href="#" class="btn-secondary" @click.prevent="deleteGoal()" v-if="hasGoal==true">Delete a Goal</a>
               </div>
           </div>
       </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';

	export default {
        props: ['modalWindow'],
		mixins : [siteAPI, skillectiveHelper],
		data() {
			return {
                fields: {
					goal_value : 0,
                    goal_description : '',
                    goal_color : ''
                },
                defaultGoalColor: '#AAAAAA',
                hasGoal : false
			}
		},
		components: {

		},
		methods: {
            checknumber (evt) {
                if (evt.which < 48 || evt.which > 57) {
                    evt.preventDefault();
                }
            },
            close() {
                this.modalWindow.close();
            },
			getGoal(){
				this.apiGet('/api/instructor/goal');
			},
			componentHandleGetResponse(responseData) {
				this.updateGoalData(responseData.data);
			},
			updateGoal(){
				this.apiPut('/api/instructor/goal', this.fields);
            },
			componentHandlePutResponse(responseData) {
				this.clearFormAndClosePopup();
				this.hasGoal = (this.fields.goal_value>0);
				this.$emit('goalUpdated', this.fields);
			},
			deleteGoal(){
				this.apiDelete('/api/instructor/goal');
            },
			componentHandleDeleteResponse(responseData){
				this.clearFormAndClosePopup();
				this.updateGoalData({goal_value:0,goal_description:'',goal_color:this.defaultGoalColor});
				this.$emit('goalUpdated', this.fields);
			},
			clearFormAndClosePopup(){
				this.errors = {};
				this.errorText = null;
				this.successText = null;
				this.close();
			},
			updateGoalData(goalData){
				this.fields.goal_value = goalData.goal_value;
				this.fields.goal_description = goalData.goal_description;
				this.fields.goal_color = goalData.goal_color;
				this.hasGoal = (this.fields.goal_value>0);
			},
		},
		mounted() {
			this.$root.$on('goalFormOpen', () => {
				this.getGoal();
			});
		},
        computed: {
			submitButtonText : function(){
				return this.hasGoal==true ? 'Update Goal' : 'Set a Goal';
            },
        }
	}
</script>