<template>
    <div>
        <div class="pos-customer-main" v-if="isUserLogin">
            <div class="pos-customer-header">
                <div class="title">{{ $t('pos_customer.top_menu.title') }}</div>
            </div>
            <div class="pos-product-container" v-bind:style="{'height': container_height}">
                <div class="pos-customer-list">
                    <div class='customer_search'>
                        <i class="fa fa-search"></i>
                        <input type="text" class="control_disabled customer_search_field" :placeholder="$t('pos_customer.menu_content.search_area.placeholder_search')" @keyup="loadCustomers" v-model="search_customer" />
                    </div>
                    <ul class="customer_list" v-if="customer_count" v-bind:style="{'height': customer_list_height}">
                        <li v-for="(customer, index) in customers_list" :key="index" :class="{'record': true, 'active': customer.email == active_customer }" @click="selectCustomer(customer)">

                            <div class="customer_name">
                                {{ customer.name }}
                            </div>
                            <div class="customer_contact">
                                <i class="fa fa-envelope"></i>
                                {{ customer.email }}
                            </div>
                            <div class="customer_contact" v-if="customer.faddresses" >
                                <i class="fa fa-map-marker"></i>
                                {{ customer.faddresses }}
                            </div>
                        </li>
                    </ul>
                    <div v-else class="message-alert danger">
                        {{ $t('pos_customer.error.no_customer_record') }}
                    </div>
                </div>
                <div class="pos-customer-view">
                    <customerview
                        :selected_customer_email="selected_customer_email"
                        :selectedCustomer="selected_customer"
                        :localObject="localObject"
                        @onChangeCustomer="updateCartCustomer"
                        @onRemoveCustomer="removeCustomer"
                    ></customerview>
                </div>
            </div>
        </div>
        
        <div class="pos-cart-container" v-bind:style="{'height': cart_container_height}">
            <pos-cart
                :pos_carts='pos_carts'
                :pos_current_cart='pos_current_cart'
                :showMinusBtn='showMinusBtn'
                :cartCustomer='cartCustomer'
                :hold_count='holdCartCount'
                :localObject='localObject'
            ></pos-cart>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['localObject'],
        data() {
            return {
                pos_offline: 0,
                pos_carts: [],
                pos_current_cart: 0,
                showMinusBtn: false,
                customers_list: [],
                customer_count: 0,
                holdCartCount: 0,
                search_customer: '',
                active_customer: '',
                selected_customer: [],
                selected_customer_email: '',
                cartCustomer: [],
                success_message: this.$t('pos_customer.menu_content.success_customer_cart'),
                
                success_customer_remove: this.$t('pos_customer.menu_content.success_customer_remove'),
                user_id: 0,
                cart_container_height: ($(window).height() - 60) + 'px',
                container_height: ($(window).height() - 105) + 'px',
                customer_list_height: ($(window).height() - 165) + 'px',
                current_currency_symbol: window.pos_currency_symbol,
            };
        },
        computed: {
            isUserLogin () {
                this.pos_offline = this.$root.offline;

                this.checkUserLogin();
                this.countHoldTotal();

                return this.user_id;
            }
        },
        beforeDestroy () {
            EventBus.$off('getAllCustomers');
        },
        created() {
            EventBus.$on('getAllCustomers', () => {
                this.loadCustomers();
            });
            EventBus.$on('customerHoldCart', () => {
                this.countHoldTotal();
            });
        },
        mounted() {
            this.loadCarts();
            this.loadCustomers();
            this.loadCustomer();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },

            countHoldTotal() {
                if (this.localObject.pos_holds) {
                    this.holdCartCount = Object.keys(this.localObject.pos_holds).length;
                }
            },
            loadCarts() {
                EventBus.$emit('getLocalForage', 'pos_current_cart');

                if (this.localObject.pos_carts.length > 0) {
                    if (this.localObject.pos_carts.length > 1) {
                        this.showMinusBtn = true;
                    }
                    this.pos_carts = this.localObject.pos_carts;
                    this.pos_current_cart = this.localObject.pos_current_cart;
                } else {
                    if (this.pos_carts.length == 0) {
                        this.pos_carts[0] = {};
                        this.pos_current_cart = 0;
                        
                        EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.pos_carts)});
                        
                        EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': this.pos_current_cart});
                    }
                }
            },            
            loadCustomers() {
                var thisthis = this;

                if (this.$root.offline) {
                    if ( Object.keys(thisthis.localObject.pos_customers).length > 0 ) {

                        if ( Object.keys(thisthis.localObject.pos_offline_customers).length > 0 ) {
                            $.each(thisthis.localObject.pos_offline_customers, (key, offline_customer) => {
                                this.checkOfflineCustomer(thisthis.localObject.pos_customers, offline_customer);
                            });
                        }

                        if (thisthis.search_customer.length > 0) {
                            let getFilterCustomers = [];
                            thisthis.customer_count = 0;

                            $.each(thisthis.localObject.pos_customers, (key, customer) => {
                                if (customer.name.toLowerCase().indexOf(thisthis.search_customer) !== -1) {
                                    getFilterCustomers = getFilterCustomers.concat(customer);
                                    thisthis.customer_count += 1;    
                                }
                            });
                            thisthis.customers_list = getFilterCustomers;
                        } else {
                            thisthis.customers_list = thisthis.localObject.pos_customers;
                            thisthis.customer_count = Object.keys(thisthis.localObject.pos_customers).length;
                        }
                        
                    } else if ( Object.keys(thisthis.localObject.pos_offline_customers).length > 0 ) {
                        if (thisthis.search_customer.length > 0) {
                            let getFilterCustomers = [];
                            thisthis.customer_count = 0;

                            $.each(thisthis.localObject.pos_offline_customers, (key, customer) => {
                                if (customer.name.toLowerCase().indexOf(thisthis.search_customer) !== -1) {
                                    getFilterCustomers = getFilterCustomers.concat(customer);
                                    thisthis.customer_count += 1;    
                                }
                            });
                            thisthis.customers_list = getFilterCustomers;
                        } else {
                            thisthis.customers_list = thisthis.localObject.pos_offline_customers;
                            thisthis.customer_count = Object.keys(thisthis.localObject.pos_offline_customers).length;
                            EventBus.$emit('setLocalForage', {'key': 'pos_customers', 'data': JSON.stringify(thisthis.localObject.pos_offline_customers)});
                        }
                        
                    } else {
                        thisthis.customers_list = [];
                        thisthis.customer_count = 0;
                        thisthis.selected_customer_email = '';
                    }
                } else {
                    this.$http.get('/api/pos/getCustomers', {
                        params: {
                            user_id: thisthis.user_id,
                            customer_name: thisthis.search_customer
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length > 0) {
                            thisthis.customer_count = response.data.meta.total;
                            thisthis.customers_list = response.data.data;

                            thisthis.localObject.pos_customers = thisthis.customers_list;
                            EventBus.$emit('setLocalForage', {'key': 'pos_customers', 'data': JSON.stringify(thisthis.customers_list)});
                        } else {
                            thisthis.customer_count = 0;
                            thisthis.localObject.pos_customers = thisthis.customers_list;
                        }
                    })
                    .catch(function (error) {});
                }
            },
            selectCustomer(customer) {
                EventBus.$emit('getLocalForage', 'pos_cart_customer');
                customer.currentActive = false;
                
                if (this.localObject.pos_cart_customer && this.localObject.pos_cart_customer.id) {
                    if (this.localObject.pos_cart_customer.id == customer.id) {
                        customer.currentActive = true;
                    }
                }
                this.active_customer = customer.email;
                this.selected_customer = customer;
                this.selected_customer_email = customer.email;
            },
            loadCustomer() {
                EventBus.$emit('getLocalForage', 'pos_cart_customer');
                
                if (this.localObject.pos_cart_customer && this.localObject.pos_cart_customer.id) {
                    this.cartCustomer           = this.localObject.pos_cart_customer;
                    this.active_customer        = this.localObject.pos_cart_customer.email;
                    this.selected_customer_email= this.localObject.pos_cart_customer.email;
                    this.selected_customer      = this.localObject.pos_cart_customer;
                    this.selected_customer.currentActive = true;
                }
            },
            updateCartCustomer(updatedCustomer) {
                var self = this;
                var productObjs = this.localObject.pos_products;

                if (updatedCustomer.email) {
                    EventBus.$emit('setLocalForage', {'key': 'pos_cart_customer', 'data': JSON.stringify(updatedCustomer)});
                    
                    this.cartCustomer           = updatedCustomer;
                    this.active_customer        = updatedCustomer.email;
                    this.selected_customer_email= updatedCustomer.email;
                    this.selected_customer      = updatedCustomer;
                    
                    $.each(self.localObject.pos_carts[self.localObject.pos_current_cart], function(key, cart_product) {

                        // Product Customer Group Price
                        var customer_prices = {
                                price:              cart_product.base_price,
                                converted_price:    cart_product.price,
                                formated_price:     cart_product.f_price,
                            };
                        
                        if ( cart_product.type == 'simple'
                            || cart_product.type == 'virtual'
                            || cart_product.type == 'downloadable'
                        ) {
                            var productObj = $.grep(productObjs, function(obj){return obj.id === cart_product.id;})[0];

                            if ( cart_product.type == 'downloadable' ) {
                                var download_attributes = cart_product.additional.attributes;
                                var download_links = download_attributes[cart_product.additional.product_id].download_links;

                                var option_price = {price: 0, converted_price: 0};

                                $.each(download_links, function(key, link) {
                                    option_price.price = parseFloat(option_price.price) + parseFloat(link.price);
                                    option_price.converted_price = parseFloat(option_price.converted_price) + parseFloat(link.converted_price);
                                });
                                
                                customer_prices = self.calculateCustomerGroupPrice(productObj, cart_product.qty_ordered);

                                customer_prices.converted_price  = parseFloat(customer_prices.converted_price) + parseFloat(option_price.converted_price);
                                                    
                                customer_prices.price  = parseFloat(customer_prices.price) + parseFloat(option_price.price);
                                
                                customer_prices.formated_price  = self.current_currency_symbol + parseFloat(customer_prices.converted_price).toFixed(2);
                            } else {
                                customer_prices = self.calculateCustomerGroupPrice(productObj, cart_product.qty_ordered);
                            }
                        }
                        
                        cart_product.price      = customer_prices.converted_price;
                        cart_product.f_price    = customer_prices.formated_price;

                        cart_product.total      = parseFloat(cart_product.qty_ordered * customer_prices.converted_price).toFixed(2);
                        cart_product.f_total    = self.current_currency_symbol + parseFloat(cart_product.total).toFixed(2);
                    });

                    self.pos_carts          = self.localObject.pos_carts;

                    EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(self.localObject.pos_carts)});

                    this.$toast.success(this.success_message, {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            },
            removeCustomer() {
                var self = this;
                var productObjs = this.localObject.pos_products;

                EventBus.$emit('deleteLocalForage', 'pos_cart_customer');
                this.cartCustomer           = [];
                this.active_customer        = '';
                this.selected_customer_email= '';
                this.selected_customer      = [];

                $.each(self.localObject.pos_carts[self.localObject.pos_current_cart], function(key, cart_product) {

                    // Product Customer Group Price
                    var customer_prices = {
                            price:              cart_product.base_price,
                            converted_price:    cart_product.price,
                            formated_price:     cart_product.f_price,
                        };
                    
                    if ( cart_product.type == 'simple'
                        || cart_product.type == 'virtual'
                        || cart_product.type == 'downloadable'
                    ) {
                        var productObj = $.grep(productObjs, function(obj){return obj.id === cart_product.id;})[0];

                        if ( cart_product.type == 'downloadable' ) {
                            var download_attributes = cart_product.additional.attributes;
                            var download_links = download_attributes[cart_product.additional.product_id].download_links;

                            var option_price = {price: 0, converted_price: 0};

                            $.each(download_links, function(key, link) {
                                option_price.price = parseFloat(option_price.price) + parseFloat(link.price);
                                option_price.converted_price = parseFloat(option_price.converted_price) + parseFloat(link.converted_price);
                            });
                            
                            customer_prices = self.calculateCustomerGroupPrice(productObj, cart_product.qty_ordered);

                            customer_prices.converted_price  = parseFloat(customer_prices.converted_price) + parseFloat(option_price.converted_price);
                                                
                            customer_prices.price  = parseFloat(customer_prices.price) + parseFloat(option_price.price);
                            
                            customer_prices.formated_price  = self.current_currency_symbol + parseFloat(customer_prices.converted_price).toFixed(2);
                        } else {
                            customer_prices = self.calculateCustomerGroupPrice(productObj, cart_product.qty_ordered);
                        }
                    }
                    
                    cart_product.price      = customer_prices.converted_price;
                    cart_product.f_price    = customer_prices.formated_price;

                    cart_product.total      = parseFloat(cart_product.qty_ordered * customer_prices.converted_price).toFixed(2);
                    cart_product.f_total    = self.current_currency_symbol + parseFloat(cart_product.total).toFixed(2);
                });

                self.pos_carts          = self.localObject.pos_carts;

                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(self.localObject.pos_carts)});

                this.$toast.success(this.success_customer_remove, {
                    position: 'top-right',
                    duration: 2000,
                });
            },
            checkOfflineCustomer(arr, offline_record) {
                const { length } = arr;
                const id = length + 1;
                const found = arr.some(el => el.email === offline_record.email);
                if (!found) arr.push(offline_record);
                return arr;
            },
            calculateCustomerGroupPrice(product, qty) {
                var self = this;
                var lastValueObj = {
                    qty:                1,
                    customerGroupId:    null,
                    price:              product.price,
                    converted_price:    product.converted_price,
                    formated_price:     product.formated_price,
                };
                var customer_group_id = 0;
                
                if ( self.selected_customer.email ) {
                    customer_group_id = self.selected_customer.customer_group_id;
                } else {
                    $.each(self.localObject.pos_customer_groups, function(key, customer_group) {
                        if ( customer_group.code == 'guest' ) {
                            customer_group_id = customer_group.id;
                        }
                    });
                }
                
                var group_price_count = Object.keys(product.customerGroupPrices).length;

                if (! qty || qty == 0) {
                    qty = 1;
                }

                if ( group_price_count <= 0) {
                    return lastValueObj;
                }

                $.each(product.customerGroupPrices, function(key, group_price) {
                    if ( (group_price.customer_group_id == customer_group_id) || !group_price.customer_group_id ) {
                        
                        if ( (group_price.customer_group_id && group_price.customer_group_id != customer_group_id)
                            || (qty < group_price.qty)
                            || (group_price.qty < lastValueObj.qty)
                        ) {
                            return true;
                        }

                        if ( group_price.qty == lastValueObj.qty
                            && lastValueObj.customerGroupId != null
                            && group_price.customer_group_id == null
                        ) {
                            return true;
                        }

                        if ( group_price.value <= lastValueObj.price && group_price.value_type == 'fixed' ) {
                            lastValueObj.price = group_price.value;
                            lastValueObj.converted_price = group_price.converted_value;
                            lastValueObj.formated_price = self.current_currency_symbol + parseFloat(lastValueObj.converted_price).toFixed(2);
                            lastValueObj.qty = group_price.qty;
                            lastValueObj.customerGroupId = group_price.customer_group_id;
                        } else {
                            if ( group_price.value_type == 'discount' && group_price.value <= 100 ) {
                                var discounted_price = product.price - (product.price * group_price.value) / 100;

                                if ( discounted_price <= lastValueObj.price ) {

                                    lastValueObj.price = discounted_price;
                                    lastValueObj.converted_price = product.converted_price - (product.converted_price * group_price.value) / 100;
                                    lastValueObj.formated_price = self.current_currency_symbol + parseFloat(lastValueObj.converted_price).toFixed(2);
                            
                                    lastValueObj.qty = group_price.qty;
                                    lastValueObj.customerGroupId = group_price.customer_group_id;
                                }
                            }
                        }
                    }
                });

                return lastValueObj;
            },
        }
    }
</script>
