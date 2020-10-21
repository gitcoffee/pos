<template>
    <div>
        <div class="pos-payment-main" v-if="isUserLogin">
            <div class="pos-payment-header">
                <div class="title">
                    {{ $t('pos_payment.heading_title') }}
                    <i class="fa fa-close" @click="redirectToHome"></i>
                </div>
            </div>
            <div class="pos-product-container">
                <div class="checkout_details" v-if="customer_status">
                    <div class="customer_detail">
                        <div class="name"> 
                            <i class="fa fa-user-circle"></i>
                            <span>{{ cart_customer.name }}</span>
                        </div>
                        <div class="address" v-if="cart_customer.email">
                            <i class="fa fa-envelope"></i>
                            <span>{{ cart_customer.email }}</span>
                        </div>
                        <div class="address" v-if="cart_customer.faddresses">
                            <i class="fa fa-map-marker"></i>
                            <span>{{ cart_customer.faddresses }}</span>
                        </div>
                    </div>
                    <div class="payment_detail">
                        <ul class="vertical-tab">
                            <li v-for="(menu, index) in tab_menus" :key="index" :class="{ 'vertical-nav': true, 'active': index == active_tab }" @click="activeTab(index)">
                                <router-link :to="{ path: menu.route }">
                                    {{ menu.name }}
                                </router-link>
                            </li>
                        </ul>
                        
                        <div class="vertical-tabcontent">
                            <router-view
                                :localObject="localObject"
                            ></router-view>
                        </div>
                        
                    </div>
                </div>
            </div>
                
        </div>

        <div class="pos_order_component" v-if="active_print">
            <invoice-print
                :localObject="localObject"
                :orderData="order_details"
            ></invoice-print>
        </div>

        <div v-if="this.$root.posCommonModal.printOrderSuccess">
            <pos-common-modal
            id="printOrderSuccess"
            :showClose='true'
            :is-open="this.$root.posCommonModal.printOrderSuccess"
            >
                <h4 slot="header">{{ $t('pos_payment.heading_order_print') }}</h4>
                
                <div slot="body">
                    <div class="message-alert .text-success"> {{ $t('pos_payment.text_order_success') }} </div>
                    
                    <div class="pos-action text-right" v-bind:style="{ 'padding-right': '0px' }">
                        <button type="button" class="btn btn-lg btn-pos-dark" @click="printOrder('printOrderSuccess')">
                            <i class="fa fa-print"></i> {{ $t('pos_payment.btn_print') }}
                        </button>
                        <button type="button" class="btn btn-lg btn-pos-default" @click="skipPrint('printOrderSuccess')">
                            <i class="fa fa-arrow-circle-right"></i> {{ $t('pos_payment.btn_skip') }}
                        </button>
                    </div>
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
                user_id: 0,
                cart_customer: [],
                offline_data: {},
                customer_status: false,
                active_tab: 0,
                tab_menus: [
                    {
                        'code': 'cash',
                        'name': this.$t('pos_payment.vertical_menu.text_cash'),
                        'route': '/pos/payment/cash'
                    },
                    {
                        'code': 'credit',
                        'name': this.$t('pos_payment.vertical_menu.text_credit'),
                        'route': '/pos/payment/credit'
                    }
                ],
                success_offline_order: this.$t('pos_payment.success_offline_order'),
                active_print: false,
                order_details: {},
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        beforeDestroy () {
            EventBus.$off('createOrder');
        },
        created() {
            EventBus.$on('createOrder', (online_order, offline_order) => {
                this.saveOrder(online_order, offline_order);
            });
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;

                    if (this.localObject.pos_cart_customer.email) {
                        this.customer_status = true;
                        this.cart_customer = this.localObject.pos_cart_customer;
                    }
                }
            },
            
            activeTab(activeTab) {
                this.active_tab = activeTab;
            },

            redirectToHome() {
                this.$router.push({name: 'pos_home'});
            },

            saveOrder(online_order, offline_order) {
                if (this.$root.offline) {
                    offline_order.order_note    = online_order.pos_order.order_note;
                    offline_order.ref_id        = online_order.ref_id;

                    this.offline_data = {
                        online:     online_order,
                        offline:    offline_order
                    }

                    this.localObject.pos_offline_orders = this.localObject.pos_offline_orders.concat(this.offline_data);

                    EventBus.$emit('setLocalForage', {'key': 'pos_offline_orders', 'data': JSON.stringify(this.localObject.pos_offline_orders)});
                    
                    this.$root.toggleButtonDisable(false);

                    var body = document.querySelector("body");
                    body.classList.remove("pos-modal-open");
                    $("body").find(".pos-navbar-top").removeClass('pos-overlay');
                    $("body").find(".pos-navbar-left").removeClass('pos-overlay');

                    this.$root.hideCommonModal('confirmOrder');

                    this.removeCart(true);

                    EventBus.$emit('loadCartContent');
                    
                    this.order_details                  = offline_order;
                    this.order_details.created_at       = online_order.pos_order.order_date_time;
                    this.order_details.pos_order_note   = online_order.pos_order.order_note;

                    // remove current cart customer
                    this.localObject.pos_cart_customer = [];
                    EventBus.$emit('setLocalForage', {'key': 'pos_cart_customer', 'data': JSON.stringify(this.localObject.pos_cart_customer)});

                    setTimeout(() => {
                        this.$root.showCommonModal('printOrderSuccess');
                    });
                } else {
                    var thisthis = this;
                    delete online_order.ref_id;
                    
                    this.$http.post('/api/pos/auth/addOrder', {
                        order_data: online_order
                    })
                    .then((response)  =>  {
                        if (!response.data.status) {
                            this.$toast.error(response.data.message, {
                                position: 'top-right',
                                duration: 2000,
                            });
                            this.$root.toggleButtonDisable(false);
                        } else {
                            this.updateProductQty(offline_order);

                            this.$root.toggleButtonDisable(false);

                            this.$root.hideCommonModal('confirmOrder');

                            this.removeCart(true);
                            
                            EventBus.$emit('loadCartContent');
                            offline_order.order_note            = online_order.pos_order.order_note;
                            this.order_details                  = offline_order;
                            this.order_details.created_at       = online_order.pos_order.order_date_time;
                            this.order_details.id               = response.data.order_id;
                            this.order_details.order_barcode    = response.data.order_barcode;

                            // remove current cart customer
                            this.localObject.pos_cart_customer = [];
                            EventBus.$emit('setLocalForage', {'key': 'pos_cart_customer', 'data': JSON.stringify(this.localObject.pos_cart_customer)});
                            
                            setTimeout(() => {
                                this.$root.showCommonModal('printOrderSuccess');
                            });
                        }
                    })
                    .catch(function (error) {});
                }
            },

            printOrder(modalId) {
                if (this.order_details) {
                    this.$root.hideCommonModal(modalId);
                    this.active_print = true;

                    var body = document.querySelector("body");
                    body.classList.add("pos-order-print");
                    
                    setTimeout(() => {
                        window.print();
                    
                        var body = document.querySelector("body");
                        body.classList.remove("pos-order-print");
                        this.active_print = false;
                        
                        this.skipPrint(modalId);
                    }, 50);
                }
            },
            skipPrint(modalId) {
                var body = document.querySelector("body");
                body.classList.remove("pos-modal-open");
                $("body").find(".pos-navbar-top").removeClass('pos-overlay');
                $("body").find(".pos-navbar-left").removeClass('pos-overlay');

                this.$root.hideCommonModal(modalId);

                if (this.$root.offline) {
                    this.$router.push({name: 'pos_sales_offline'});
                } else {
                    this.$router.push({name: 'pos_sales_history'});
                }
            },
            updateProductQty(orderData) {
                var thisthis = this;
                $.each(orderData.items, function(key_index, ordered_product) {
                    if (ordered_product.type == 'simple' || ordered_product.type == 'virtual') {
                        var obj = $.grep(thisthis.localObject.pos_products, function(obj, key){
                            if ( parseInt(ordered_product.id) == parseInt(obj.id) ) {
                                let prod_qty = JSON.parse(obj.quantity);

                                if (prod_qty[ordered_product.id]) {
                                    prod_qty[ordered_product.id] = parseInt(prod_qty[ordered_product.id]) - parseInt(ordered_product.qty_ordered);

                                    obj.quantity = JSON.stringify(prod_qty);
                                    thisthis.localObject.pos_products[key] = obj;
                                    return true;
                                }
                            }
                        });
                    } else {
                        if (ordered_product.additional.product) {
                            let parent_prod_id = parseInt(ordered_product.additional.product);

                            var obj = $.grep(thisthis.localObject.pos_products, function(obj, key){
                                if ( parent_prod_id == parseInt(obj.id) ) {
                                    let prod_qty = JSON.parse(obj.quantity);

                                    if (prod_qty[ordered_product.id]) {
                                        prod_qty[ordered_product.id] = parseInt(prod_qty[ordered_product.id]) - parseInt(ordered_product.qty_ordered);

                                        obj.quantity = JSON.stringify(prod_qty);
                                        thisthis.localObject.pos_products[key] = obj;
                                        return true;
                                    }
                                }
                            });
                        }
                    }
                });
                EventBus.$emit('setLocalForage', {'key': 'pos_products', 'data': JSON.stringify(thisthis.localObject.pos_products)});
            },

            removeCart(removeStatus) {
                this.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');

                var next_cart = this.localObject.pos_current_cart;

                if (Object.keys(this.localObject.pos_carts).length > 1) {

                    this.localObject.pos_carts.splice(this.localObject.pos_current_cart, 1);
                    
                    next_cart = 0;
                } else {
                    if (removeStatus) {
                        this.localObject.pos_carts.splice(this.localObject.pos_current_cart, 1);
                        next_cart = 0;

                        if (this.localObject.pos_carts.length == 0) {
                            this.localObject.pos_carts[0] = {};
                            this.localObject.pos_current_cart = 0;
                        }
                    }
                }

                EventBus.$emit('cartActive', next_cart);

                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.localObject.pos_carts)});
                
                EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': this.localObject.pos_current_cart});
            }
        }
    }
</script>