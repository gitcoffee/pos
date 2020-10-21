<template>
    <div class="product-lowstock-panel">
        <div class="nav-lowstock-list">
            <div class="pos-product-container" v-if="isUserLogin">

                <div class="message-alert warning" v-if="pos_offline">
                    {{ $t("pos_view.error_offline_mode") }}
                </div> 

                <div class="product-list row-grid-5" v-if="total_product != 0">
                    <div class="product-layout" v-for="(product) in pos_products" :key="product.id" @click="requestProductStock(product)">
                        <div class="product-thumb">
                            <img v-if="product.base_image.small_image_url" :src="product.base_image.small_image_url"  />
                            <label v-else class="product-placeholder"></label>
                        </div>
                        <div class="product-name">
                            {{ product.name }}
                        </div>
                        <div class="product-quantity">
                            <span class="low-quantity">
                                {{ getRemainingQty(product) }} {{ $t("pos_product.menu_content.low_stock.text_left") }}
                            </span>
                        </div>
                    </div>
                    
                </div>
                <div v-else class="message-alert danger">
                    {{ $t("pos_product.error.no_lowstock_product") }}
                </div>
            </div>
        </div>
        <div class="nav-lowstock-request-list">
            <div class="container-panel-header">
                <h3>
                    {{ $t("pos_product.menu_content.low_stock.text_request_product") }}
                </h3>
            </div>
            <div class="requested_list_container" v-bind:style="{'height': requested_list_height}">
                <ul class="requested_list" v-if="requested_count != 0">
                    <li v-for="(request, index) in requested_list" v-bind:key="index">
                        <div class="pull-left text-center">
                            <div class="product-thumb">
                                <img v-if="request.base_image.small_image_url" :src="request.base_image.small_image_url"/>
                                <label v-else class="product-placeholder"></label>
                            </div>
                            <div class="product-quantity">
                                {{ request.requested_quantity }} {{ $t("pos_product.menu_content.low_stock.text_qty") }}
                            </div>
                        </div>
                        <div class="pull-right" :title="request.comment">
                            <div class="product-name">
                                <div class="name">{{ request.name }}</div>

                                <div class="setting-list-action" v-if="!pos_offline">
                                    <span class="discount-edit" @click="editRequest(request)">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <span class="discount-remove" @click="removeRequest(request.id)">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="supplier-name hide">
                                <label>{{ $t("pos_product.menu_content.low_stock.entry_supplier_name") }}</label>
                                {{ request.name }}
                            </div>

                            <div class="comment">
                                <label>{{ $t("pos_product.menu_content.low_stock.entry_comment") }}</label>
                                {{ request.trim_comment }}
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="pos-action text-center">
                    <button class="btn btn-md btn-pos-primary" type="submit" :text="$t('pos_product.menu_content.low_stock.button_send')" @click="sendRequest()"> {{ $t('pos_product.menu_content.low_stock.button_send') }} </button>
                </div>
            </div>
        </div>

        <div v-if="this.$root.posCommonModal.requestQuantity">
            <pos-common-modal id="requestQuantity" :is-open="this.$root.posCommonModal.requestQuantity">
                <h4 slot="header">{{ $t('pos_product.menu_content.low_stock.request_product') }}</h4>
                
                <div slot="body">
                    <request-form
                        :request_id='request_id'
                        :request_data='request_data'
                        :requested_product='requested_product'
                        :localObject='localObject'
                    ></request-form>
                </div>
            </pos-common-modal>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                pos_products: [],
                total_product: 0,
                page: 1,
                limit: 5,
                user_details: {},
                user_id: 0,
                request_id: 0,
                request_data: {},
                requested_product: {},
                requested_list: [],
                requested_count: 0,
                pos_offline: 0,
                requested_list_height: ($(window).height() - 150) + 'px',
                error_offline_action: this.$t('pos_view.error_offline_action'),
            };
        },
        created() {
            this.loadProducts();

            EventBus.$on('getRequestedLowStockProducts', () => {
                this.requested_list = [];
                this.requested_count = 0;
                this.getRequestedLowStockProducts();
            });
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
                    
                    this.getRequestedLowStockProducts();
                }
            },
            loadProducts() {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    if (Object.keys(this.localObject.pos_lowstock_products).length > 0) {
                        this.total_product = Object.keys(this.localObject.pos_lowstock_products).length;
                        this.pos_products = this.localObject.pos_lowstock_products;
                    } else {
                        this.pos_products = [];
                        this.total_product = 0;
                    }
                } else {
                    this.$http.get('/api/pos/auth/outletProducts', {
                        params: {
                            page: this.page,
                            outlet_id: this.localObject.pos_cashier.outlet_id,
                            inventory_source_id: this.localObject.pos_cashier.inventory_source_id,
                            filter_status: true,
                            filter_quantity: this.localObject.pos_cashier.low_stock ? this.localObject.pos_cashier.low_stock : 10
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length > 0) {
                            this.total_product += response.data.data.length;
                            this.totalPage = response.data.meta.last_page;
                            this.total_products = response.data.meta.total;
                            
                            this.pos_products = this.pos_products.concat(response.data.data);

                            EventBus.$emit('setLocalForage', {'key': 'pos_lowstock_products', 'data': JSON.stringify(this.pos_products)});

                        } else {
                            this.total_product = 0;
                            EventBus.$emit('setLocalForage', {'key': 'pos_lowstock_products', 'data': JSON.stringify(this.pos_products)});
                        }
                    })
                    .catch(function (error) {});
                }
            },
            
            requestProductStock(product) {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.warning(this.error_offline_action, {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    this.request_id = 0;
                    this.request_data = {};
                    this.requested_product = product;
                    this.$root.hideCommonModal('requestQuantity');
                    this.getSingleRequestedProduct(product);
                    setTimeout(function() {
                        EventBus.$emit('showCommonModal', 'requestQuantity');
                    }, 500);
                }
            },

            getSingleRequestedProduct(product) {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.warning(this.error_offline_action, {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    this.$http.get('/api/pos/auth/getLowRequestedProducts', {
                        params: {
                            user_id: this.user_id,
                            send_status: 0,
                            product_id: product.id
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length == 1) {
                            this.request_id = response.data.data[0].id;
                            this.request_data = response.data.data[0];
                        }
                    })
                    .catch(function (error) {});
                }
            },

            getRequestedLowStockProducts() {
                this.pos_offline = this.$root.$root.offline;
                if (!this.pos_offline) {
                    this.$http.get('/api/pos/auth/getLowRequestedProducts', {
                        params: {
                            user_id: this.user_id,
                            send_status: 0,
                            page: this.page,
                            limit: this.limit
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length > 0) {
                            this.requested_count = response.data.data.length;
                            this.requested_list = response.data.data;
                        } else {
                            this.requested_count = 0;
                        }
                    })
                    .catch(function (error) {});
                }
            },

            editRequest(request) {
                this.pos_offline = this.$root.$root.offline;
                if (!this.pos_offline) {
                    this.request_id = request.id;
                    this.request_data = request;
                    this.requested_product = {
                        id: request.product_id,
                        name: request.name,
                        base_image: request.base_image,
                    };
                    this.$root.hideCommonModal('requestQuantity');
                    setTimeout(function() {
                        EventBus.$emit('showCommonModal', 'requestQuantity');
                    },1000)
                }
            },
            removeRequest(request_id) {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.warning(this.error_offline_action, {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    if (this.localObject.pos_cashier.id) {
                        this.$http.post('/api/pos/auth/deleteRequest', {
                            user_id: this.localObject.pos_cashier.id,
                            request_id: request_id
                        })
                        .then((response)  =>  {
                            if (!response.data.status) {
                                window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                                this.$root.addFlashMessages();
                            } else {
                                window.flashMessages = [{'type': 'alert-success', 'message': response.data.message}];
                                this.$root.addFlashMessages();
                                this.requested_list = [];
                                this.requested_count = 0;
                                this.getRequestedLowStockProducts();
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },

            sendRequest() {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.warning(this.error_offline_action, {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    if (this.localObject.pos_cashier.id && this.requested_count > 0) {
                        this.$http.post('/api/pos/auth/sentRequest', {
                            user_id: this.localObject.pos_cashier.id,
                            requested_list: this.requested_list,
                        })
                        .then((response)  =>  {
                            if (!response.data.status) {
                                window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                                this.$root.addFlashMessages();
                            } else {
                                window.flashMessages = [{'type': 'alert-success', 'message': response.data.message}];
                                this.$root.addFlashMessages();
                                this.requested_list = [];
                                this.requested_count = 0;
                                this.getRequestedLowStockProducts();
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },
            getRemainingQty(product) {
                var thisthis = this;
                if (product.type == 'simple') {
                    let product_qty = JSON.parse(product.quantity);
                    let remaining_quantity = product_qty[product.id];

                    if (Object.keys(thisthis.localObject.pos_offline_orders).length > 0) {
                        var totalOrderProductQty = 0;
                        $.each(thisthis.localObject.pos_offline_orders, function(key, offlineOrder) {
                            totalOrderProductQty = totalOrderProductQty + thisthis.checkOrderProduct(offlineOrder.offline.items, product);
                        });
                        remaining_quantity = remaining_quantity - totalOrderProductQty;
                    }

                    return remaining_quantity;
                }
            },

            checkOrderProduct(orderItems, product) {
                var orderProductQty = 0;
                $.each(orderItems, function(key, orderProduct) {
                    if ( parseInt(orderProduct.id) == parseInt(product.id) ) {
                        orderProductQty = orderProductQty + orderProduct.qty_ordered;
                    }
                });
                return orderProductQty;
            }
        }
    }
</script>