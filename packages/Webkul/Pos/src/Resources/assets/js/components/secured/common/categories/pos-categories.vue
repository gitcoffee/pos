<template>
    <ul class="category-section" v-if="pos_categories.length">
        
        <li :class="[pageItemClass, (!currently_selected) ? activeClass : '']" :title="$t('pos_home.pos_categories.all')" @click="allProductShow(pos_category)">
            {{ $t('pos_home.pos_categories.all') }}
        </li>
        <ul class="parent-categories" v-if="pos_categories.length">
            <li v-for="(category, index) in pos_categories.slice(0, 3)" :class="[pageItemClass, (category.id === currently_selected) ? activeClass : '']" :data-category-id="category.id" :title="category.name" @click="productFilter(category)">
                {{ category.name }}
            </li>

            <li class="show_all_category" title="Explore More Categories" @click="displayAllCategories('showAllCategories')">
                <i class="fa fa-external-link"></i>
            </li>
        </ul>

        <pos-common-modal v-if="showCategoryModal" id="showAllCategories" :is-open="this.$root.posCommonModal.showAllCategories">
            <h4 slot="header">{{ $t('pos_home.pos_categories.all_category_listing') }}</h4>
            
            <div slot="body">
                <pos-category-nav
                    :categories="pos_categories"
                ></pos-category-nav>
            </div>
        </pos-common-modal>
    </ul>
</template>

<script>

    export default {
        props: ['localObject'],
        data() {
            return {
                currently_selected: 0,
                pos_category: 0,
                pos_categories: {},
                page: 1,
                totalPage: 0,
                total_product_categories: 0,
                pos_product_categories: [],
                pos_child_status: false,
                pageItemClass: 'related_category',
                activeClass: 'focus-category',
                showCategoryModal: false
            };
        },
        created() {
            EventBus.$on('categoryProductFilter', (category) => {
                this.productFilter(category);
            });
        },
        mounted: function() {
            this.getPosCategories();
            this.getPosProductCategories();
        },
        methods: {
            getPosCategories() {
                var thisthis = this;
                if (thisthis.$root.offline) {
                    if ( Object.keys(thisthis.localObject.pos_categories).length > 0 ) {
                        thisthis.pos_categories = thisthis.localObject.pos_categories;
                    } else {
                        thisthis.pos_categories = {};
                    }
                } else {
                    if ( Object.keys(thisthis.localObject.pos_categories).length > 0 ) {
                        thisthis.pos_categories = thisthis.localObject.pos_categories;
                    } else {
                        thisthis.$http.get('/api/pos/categories')
                        .then((response)  =>  {
                            if (response.data.status) {
                                thisthis.pos_categories = response.data.categories;
                                
                                thisthis.localObject.pos_categories = thisthis.pos_categories;
                                
                                EventBus.$emit('setLocalForage', {'key': 'pos_categories', 'data': JSON.stringify(thisthis.pos_categories)});
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },
            getPosProductCategories() {
                if (this.$root.offline) {
                    if ( Object.keys(this.localObject.pos_product_categories).length > 0 ) {
                        this.pos_product_categories = this.localObject.pos_product_categories;
                        this.total_product_categories = Object.keys(this.localObject.pos_product_categories).length;
                    } else {
                        this.pos_product_categories = {};
                        this.total_product_categories = 0;
                    }
                } else {
                    if (this.localObject.pos_cashier.id) {
                        if ( Object.keys(this.localObject.pos_product_categories).length > 0 ) {
                            this.pos_product_categories = this.localObject.pos_product_categories;
                            this.total_product_categories = Object.keys(this.localObject.pos_product_categories).length;
                        } else {
                            this.$http.post('/api/pos/auth/productCategories', {
                                page: this.page,
                                user_id: this.localObject.pos_cashier.id,
                                outlet_id: this.localObject.pos_cashier.outlet_id,
                            })
                            .then((response)  =>  {
                                if (response.data.data && response.data.data.length > 0) {
                                    this.total_product_categories += response.data.data.length;
                                    this.totalPage = response.data.meta.last_page;
                                    
                                    this.pos_product_categories = this.pos_product_categories.concat(response.data.data);

                                    if (this.totalPage > this.page) {
                                        this.page += 1;
                                        this.getPosProductCategories();
                                    } else {                                
                                        EventBus.$emit('setLocalForage', {'key': 'pos_product_categories', 'data': JSON.stringify(this.pos_product_categories)});

                                        this.localObject.pos_product_categories = this.pos_product_categories;
                                    }
                                } else {
                                    this.total_product_categories = 0;
                                }
                            })
                            .catch(function (error) {});
                        }
                        
                    }
                }
            },
            allProductShow() {
                this.currently_selected = 0;
                this.$emit('onCategoryChange', null);
            },            
            productFilter: function(selectedRelatedCategory) {
                var thisthis = this;
                this.$emit('onCategoryChange', selectedRelatedCategory.id);                

                $.each(thisthis.pos_categories, function(key, child_category) {
                    if (child_category.id == selectedRelatedCategory.id) {
                        thisthis.currently_selected = child_category.id;
                    }
                });
            },
            displayAllCategories(modalId) {
                this.showCategoryModal = true;
                EventBus.$emit('showCommonModal', modalId);
            }
        }
    }
</script>
