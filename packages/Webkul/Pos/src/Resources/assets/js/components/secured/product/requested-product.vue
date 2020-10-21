<template>
    <div class="product-requested-panel">

        <div class="requested-search-area hide">
            <div class='request_search'>
                <i class="fa fa-search"></i>
                <input type="text" class="control_disabled request_search_field" :placeholder="$t('pos_product_requested.placeholder_search')" v-model="search_request" @keyup="getRequestedProducts" />
            </div>
        </div>

        <div class="pos-table-responsive">
            <table class="pos-table" v-if="isUserLogin">
                <thead>
                    <tr>
                        <th class="text-left">{{ $t("pos_product.menu_content.request_list.column_id") }}</th>
                        <th class="text-left">{{ $t("pos_product.menu_content.request_list.column_name") }}</th>
                        <th class="text-left">{{ $t("pos_product.menu_content.request_list.column_qty") }}</th>
                        <th class="text-left hide">{{ $t("pos_product.menu_content.request_list.column_supplier") }}</th>
                        <th class="text-left">{{ $t("pos_product.menu_content.request_list.column_comment") }}</th>
                        <th class="text-left">{{ $t("pos_product.menu_content.request_list.column_date") }}</th>
                        <th class="text-left">{{ $t("pos_product.menu_content.request_list.column_status") }}</th>
                    </tr>
                </thead>
                <tbody v-if="total_product != 0">
                    <tr v-for="(request, index) in requested_products" v-bind:key="index">
                        <td class="text-left">{{ request.id }}</td>
                        <td class="text-left">
                            <div class="request-product">
                                <div class="product-thumb">
                                    <img v-if="request.base_image.small_image_url" :src="request.base_image.small_image_url" />
                                    <label v-else class="product-placeholder"></label>
                                </div>
                                <div class="product-name">
                                    {{ request.name }}
                                </div>
                            </div>
                        </td>
                        <td class="text-left">{{ request.requested_quantity }}</td>
                        <td class="text-left hide">{{ request.supplier_id }}</td>
                        <td class="text-left">
                            <div class="request_comment">
                                {{ request.comment }}
                            </div>
                        </td>
                        <td class="text-left">{{ request.created_at }}</td>
                        <td class="text-left">
                            <div class="request_status" v-if="request.request_status == 1">
                                <span class="status_complete"></span>
                                {{ $t("pos_product.menu_content.request_list.action_complete") }}
                            </div>
                            <div class="request_status" v-else-if="request.request_status == 2">
                                <span class="status_decline"></span>
                                {{ $t("pos_product.menu_content.request_list.action_decline") }}
                            </div>
                            <div class="request_status" v-else >
                                <span class="status_pending"></span>
                                {{ $t("pos_product.menu_content.request_list.action_pending") }}
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
                <tfoot v-else >
                    <tr>
                        <td class="text-center" colspan="7">{{ $t("pos_product.menu_content.request_list.no_record_found") }} </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                page: 1,
                user_id: 0,
                totalPage: 0,
                total_product: 0,
                requested_products: [],
                pos_offline: 0,
                search_request: '',
            };
        },
        mounted() {
            this.getRequestedProducts();
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            getRequestedProducts() {
                var thisthis = this;
                thisthis.pos_offline = thisthis.$root.$root.offline;
                if (thisthis.pos_offline) {
                    
                    let getFilterProducts = [];

                    if (Object.keys(thisthis.localObject.pos_requested_products).length > 0) {
                        if (thisthis.search_request.length > 0) {
                            $.each(thisthis.localObject.pos_requested_products, (key, product) => {
                                if ((product.name.toLowerCase().indexOf(thisthis.search_request.toLowerCase()) !== -1)) {
                                    getFilterProducts = getFilterProducts.concat(product);
                                    thisthis.total_product += 1;    
                                }
                            });
                            thisthis.requested_products = getFilterProducts;
                        } else {
                            thisthis.requested_products = thisthis.localObject.pos_requested_products;

                            thisthis.total_product = Object.keys(thisthis.localObject.pos_requested_products).length;
                        }
                    } else {
                        this.requested_products = [];
                        this.total_product = 0;
                    }
                } else {
                    this.$http.get('/api/pos/auth/getLowRequestedProducts', {
                        params: {
                            user_id: this.user_id,
                            filter_name: this.search_request,
                            send_status: 1,
                            page: this.page
                        }
                    })
                    .then((response)  =>  {
                        
                        if (response.data.data && response.data.data.length > 0) {
                            this.total_product += response.data.data.length;
                            this.totalPage = response.data.meta.last_page;
                            this.total_products = response.data.meta.total;
                            
                            this.requested_products = this.requested_products.concat(response.data.data);

                            if (this.totalPage > this.page) {
                                this.page += 1;
                                this.getRequestedProducts();
                            } else {
                                EventBus.$emit('setLocalForage', {'key': 'pos_requested_products', 'data': JSON.stringify(this.requested_products)});
                            }
                        } else {
                            this.total_product = 0;
                            EventBus.$emit('setLocalForage', {'key': 'pos_requested_products', 'data': JSON.stringify(this.requested_products)});
                        }
                    })
                    .catch(function (error) {});
                }
            }
        }
    }
</script>