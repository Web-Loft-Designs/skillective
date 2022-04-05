<template>
    <div>
        <div class="users-table-header">
            <div class="table-top d-flex align-items-center justify-content-between">
                <div class=" d-flex align-items-center">
                    <h2>FAQ Categories</h2>
                </div>

                <div>
                    <a href="/backend/faq-categories/create" class="btn btn-primary pull-right" style="margin-top: 3px; margin-bottom: 5px;"><img width="17" src="../../../images/ic_person_add-ic_add.png" alt="" style="margin-right: 3px;"> Add New</a>
                </div>
            </div>
            <div class="d-flex mt-3 responsive-mobile justify-content-between align-items-center">
                <div class="d-flex  responsive-mobile align-items-center">
                    <input type="text" v-model="searchString" placeholder="Search FAQ category"/>
                </div>
            </div>


        </div>
        <div class="table-responsive" v-if="listItems.length>0">
            <div v-if="errorText" class="has-error">{{ errorText }}</div>
            <div v-if="successText" class="has-success">{{ successText }}</div>

            <table class="table">
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
                    <th class="text-center w-55"></th>
                    <th scope="col">Faq Category Name</th>
                    <th scope="col">Count FAQs</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(faqCategory, index) in listItems">
                    <!--<td><span class="checkbox-wrapper"><label><input @change="select" type="checkbox" v-model="selectedItems" :value="faqCategory.id"/><span class="checkmark"></span></label></span></td>-->
                    <td class="text-center"></td>
                    <td>{{ faqCategory.title }}</td>
                    <td><a :href="'/backend/faqs?category='+faqCategory.id">{{ faqCategory.count_faqs }}</a></td>
                    <td class="action">
                        <div class="dropdown">
                            <button class="btn btn-left" @click.prevent="editFaqs(faqCategory.id)">Edit</button>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                            <div v-if="showOnly=='active'" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a  class="dropdown-item red" href="#" @click.prevent="deleteFaq(faqCategory)">Delete</a>
                                <!--<a v-if="showOnly=='disabled'" class="dropdown-item" href="#" @click.prevent="activeFaqs(faqCategory)">Active</a>-->
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
            <div class="col-6 d-flex justify-content-end align-items-center custom-pag" >
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
            faqCategories: null,
			faqCategoriesMeta: {}
        },
        components : {
            Paginate
        },
		data() {
			return {
                listItems: [],
                selectedItems: [],
                searchString : '',
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
                    for (let faqCategory in this.listItems) {
                        this.selectedItems.push(this.listItems[faqCategory].id.toString());
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
			toggleShowOnly(status){
				this.showOnly = status;
				this.allSelected = 0;
				this.selectedItems = [];
				this.pagination.current_page = 1;
				this.getFaqCategories();
			},
			paginatorClickCallback(pageNum) {
				this.pagination.current_page = pageNum;
				this.getFaqCategories();
			},
			onChangePerPage() {
				this.pagination.current_page = 1;
				Cookies.set('adminFaqCategoriesPerPage', this.pagination.per_page);
				this.getFaqCategories();
			},
//            disabledManyFaqs(){
//                this.apiPost('/api/admin/faq-categories?status=disabled', {faqCategories : this.selectedItems});
//            },
//            activeManyFaqs(){
//                this.apiPost('/api/admin/faq-categories?status=active', {faqCategories : this.selectedItems});
//            },
//            activeFaqs(faqCategory){
//                this.apiPost('/api/admin/faq-categories/' + faqCategory.id);
//            },
            deleteFaq(faqCategory){
				this.apiDelete('/api/admin/faq-categories/' + faqCategory.id);
			},
			searchFaqCategories(){
				this.pagination.current_page = 1;
				this.getFaqCategories();
			},
			getFaqCategories() {
				let queryParams = {};

//				queryParams.status = this.showOnly;

				if (this.pagination.current_page != undefined)
					queryParams.page = this.pagination.current_page;
				else
					queryParams.page = 1;

				if (this.searchString!='')
					queryParams.s = this.searchString;

				this.updateUrlQueryParams(queryParams);

				this.apiGet('/api/admin/faq-categories', {
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
				this.getFaqCategories();
			},
			editFaqs(faqId){
				window.location = '/backend/faq-categories/' + faqId +'/edit';
            }
		},
        created : function(){
            this.listItems = this.faqCategories;
			if (this.faqCategoriesMeta!=undefined && this.faqCategoriesMeta.pagination!=undefined){
				this.pagination.count           = this.faqCategoriesMeta.pagination.count;
				this.pagination.total           = this.faqCategoriesMeta.pagination.total;
				this.pagination.total_pages     = this.faqCategoriesMeta.pagination.total_pages;
				this.pagination.current_page    = this.faqCategoriesMeta.pagination.current_page;
				this.pagination.per_page        = this.faqCategoriesMeta.pagination.per_page;
			}
			if (this.getUrlParameter('s'))
				this.searchString = this.getUrlParameter('s');
			if (this.getUrlParameter('status'))
				this.showOnly = this.getUrlParameter('status');

			this.debouncedGetFaqCategories = _.debounce(this.searchFaqCategories, 500)
        },
		watch: {
			searchString: function (newSearchString, oldSearchString) {
				this.debouncedGetFaqCategories()
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