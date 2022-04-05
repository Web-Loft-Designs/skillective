<template>
    <div>
        <div class="users-table-header">
            <div class="table-top d-flex align-items-center justify-content-between">
                <div class=" d-flex align-items-center">
                    <h2>FAQs</h2>
                </div>

                <div>
                    <a href="/backend/faqs/create" class="btn btn-primary pull-right" style="margin-top: 3px; margin-bottom: 5px;"><img width="17" src="../../../images/ic_person_add-ic_add.png" alt="" style="margin-right: 3px;"> Add New</a>
                </div>
            </div>
            <div class="d-flex mt-3 responsive-mobile justify-content-between align-items-center">
                <div class="d-flex  responsive-mobile align-items-center">
                    <!--<div v-if="selectedItems.length>0" class="d-flex align-items-center">-->
                        <!--<span v-if="showOnly!='disabled'" @click="disabledManyFaqs" class="btn btn-danger mr-2">Disabled {{ selectedItems.length }} faqs</span>-->
                        <!--<span v-if="showOnly!='active'" @click="activeManyFaqs" class="btn btn-danger mr-2">Active {{ selectedItems.length }} faqs</span>-->
                    <!--</div>-->
                    <input type="text" v-model="searchString" placeholder="Search FAQ"/>
                </div>
            </div>
        </div>
        <div class="table-responsive" v-if="listItems.length>0">
            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

            <table class="table table-custom">
                <thead>
                <tr>
                    <!--<th scope="col">-->
                            <!--<span class="checkbox-wrapper">-->
                                <!--<label for="checkAll">-->
                                    <!--<input @change="selectAll" :indeterminate.prop="indeterminate" v-model="allSelected" id="checkAll" type="checkbox"/>-->
                                    <!--<span class="checkmark" :class="{'indeterminate':indeterminate === true}"></span>-->
                                <!--</label>-->
                            <!--</span>-->
                    <!--</th>-->
                    <th></th>
                    <th>Title</th>
                    <th>Category</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(faq, index) in listItems">
                    <!--<td><span class="checkbox-wrapper"><label><input @change="select" type="checkbox" v-model="selectedItems" :value="faq.id"/><span class="checkmark"></span></label></span></td>-->
                    <td></td>
                    <td>{{ faq.title }}</td>
                    <td>{{ faq.category.title }}</td>
                    <td class="action">
                        <div class="dropdown">
                            <button class="btn btn-left" @click.prevent="editFaqs(faq.id)">Edit</button>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div v-if="showOnly=='active'" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a  class="dropdown-item red" href="#" @click.prevent="disableFaq(faq)">Delete</a>
                                <!--<a v-if="showOnly=='disabled'" class="dropdown-item" href="#" @click.prevent="activeFaqs(faq)">Active</a>-->
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="row justify-content-center" v-if="listItems.length>0">
            <div class="col-6 select-show">
                <label>Show</label>
                <select v-model="pagination.per_page" @change="onChangePerPage">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center custom-pag">
                <span v-if="pagination.total_pages>1">{{ firstListItemNumber }}-{{ lastListItemNumber }} of {{ pagination.total }}</span>
                <paginate v-if="pagination.total_pages>1"
                        :page-count="pagination.total_pages"
                        :force-page="pagination.current_page"
                        :prev-text="'Prev'"
                        :next-text="'Next'"
                        :click-handler="paginatorClickCallback"
                        :container-class="'clients-pagination'">
                </paginate>
            </div>
        </div>
        <div v-if="listItems.length==0"><p>Nothing found</p></div>
    </div>
</template>

<script>
	import siteAPI from '../../mixins/siteAPI.js';
	import skillectiveHelper from '../../mixins/skillectiveHelper.js';
    import Paginate from 'vuejs-paginate';


    export default {
		mixins : [siteAPI, skillectiveHelper],
        props: {
            faqs: null,
            faqsMeta: {}
        },
        components : {
            Paginate
        },
		data() {
			return {
                listItems: [],
                selectedItems: [],
                searchString : '',
				category : '',
				showOnly: 'active',
                allSelected: false,
                indeterminate: false,
                pagination : {
                    total : 0,
                    total_pages : 0,
                    current_page : 0,
                    per_page : 0
                }
			}
		},
		methods: {
            selectAll: function() {
                this.selectedItems = [];

                if (this.allSelected) {
                    this.indeterminate = false;
                    for (let faq in this.listItems) {
                        this.selectedItems.push(this.listItems[faq].id.toString());
                    }
                }
            },
            select: function() {
                if(this.listItems.length == this.selectedItems.length ) {
                    this.allSelected = 1;
                    this.indeterminate = false;
                } else if(this.selectedItems.length === 0) {
                    this.allSelected = 0;
                    this.indeterminate = false;
                } else {
                    this.allSelected = 0;
                    this.indeterminate = true;
                }
            },
			paginatorClickCallback(pageNum) {
				this.pagination.current_page = pageNum;
				this.getFaqs();
			},
			onChangePerPage() {
				this.pagination.current_page = 1;
				Cookies.set('adminFaqsPerPage', this.pagination.per_page);
				this.getFaqs();
			},
//            disabledManyFaqs(){
//                this.apiPost('/api/admin/faqs?status=disabled', {faqs : this.selectedItems});
//            },
//            activeManyFaqs(){
//                this.apiPost('/api/admin/faqs?status=active', {faqs : this.selectedItems});
//            },
//            activeFaqs(){
//                this.apiPost('/api/admin/faqs/' + faqs.id);
//            },
            disableFaq(faqs){
				this.apiDelete('/api/admin/faq/' + faqs.id);
			},
			searchFaqs(){
				this.pagination.current_page = 1;
				this.getFaqs();
			},
			getFaqs() {
				let queryParams = {};

				queryParams.status = this.showOnly;

				if (this.pagination.current_page != undefined)
					queryParams.page = this.pagination.current_page;
				else
					queryParams.page = 1;

				if (this.searchString!='')
					queryParams.s = this.searchString;

				if (this.category!='')
					queryParams.category = this.category;

				this.updateUrlQueryParams(queryParams);

				this.apiGet('/api/admin/faqs', {
					params: queryParams
				});
			},
			componentHandleGetResponse(responseData) {
				this.listItems = responseData.data.data;
				this.selectedItems = [];
				if (responseData.data.meta!=undefined && responseData.data.meta.pagination!=undefined){
					this.pagination.count           = responseData.data.meta.pagination.count;
					this.pagination.total           = responseData.data.meta.pagination.total;
					this.pagination.total_pages     = responseData.data.meta.pagination.total_pages;
					this.pagination.current_page    = responseData.data.meta.pagination.current_page;
					this.pagination.per_page        = responseData.data.meta.pagination.per_page;
				}
                this.allSelected= false;
                this.indeterminate= false;
			},
			componentHandleDeleteResponse(responseData){
				this.getFaqs();
			},
			editFaqs(faqsId){
				window.location = '/backend/faqs/' + faqsId +'/edit';
            }
		},
        created : function(){
            this.listItems = this.faqs;
			if (this.faqsMeta!=undefined && this.faqsMeta.pagination!=undefined){
				this.pagination.count           = this.faqsMeta.pagination.count;
				this.pagination.total           = this.faqsMeta.pagination.total;
				this.pagination.total_pages     = this.faqsMeta.pagination.total_pages;
				this.pagination.current_page    = this.faqsMeta.pagination.current_page;
				this.pagination.per_page        = this.faqsMeta.pagination.per_page;
			}
			if (this.getUrlParameter('s'))
				this.searchString = this.getUrlParameter('s');
			if (this.getUrlParameter('category'))
				this.category = this.getUrlParameter('category');
			if (this.getUrlParameter('status'))
				this.showOnly = this.getUrlParameter('status');

			this.debouncedGetFaqs = _.debounce(this.searchFaqs, 500)
        },
		watch: {
			searchString: function (newSearchString, oldSearchString) {
				this.debouncedGetFaqs()
			},
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
            }
        }
	}
</script>