<template>
    <div class="vertical-content" v-if="isUserLogin">
        <div>
            <div class="control-group" :class="[this.errors.has('bank') ? 'has-error' : '']">
                <label for="bank" class="required" v-text="$t('pos_payment.pos_cash.entry_bank_list')"></label>
                <select name="bank" class="control" v-model="bank_name" v-bind:style="{ 'width': '100%' }" v-validate="'required'" >
                    <option :value="bank.name" v-for="(bank, index) in pos_banks" :key="index"> {{ bank.name }} </option>
                </select>
                <span class="control-error" v-if="this.errors.has('bank')">{{ this.errors.first('bank') }}</span>
            </div>

            <div class="control-group" :class="[this.errors.has('card_detail') ? 'has-error' : '']">
                <label for="card_detail" class="required" v-text="$t('pos_payment.pos_cash.entry_card_details')"></label>
                <input type="card_detail" class="card_four_digit control" name="card_detail" v-validate="'required|numeric|min:4|max:4'" placeholder="XXXX" v-model="card_detail" >
                <span class="control-error" v-if="this.errors.has('card_detail')">{{ this.errors.first('card_detail') }}</span>
            </div>

            <div class="control-group" v-bind:style="{ 'margin': '0px' }" >
                <textarea class="control" name="comment" v-model="order_comment"  v-bind:style="{ 'width': '100%' }" :placeholder="$t('pos_payment.pos_cash.placeholder_order_comment')" v-validate="'max:250'" ></textarea>
                <span class="control-error" v-if="this.errors.has('comment')">{{ this.errors.first('comment') }}</span>
            </div>
        </div>

        <div class="pos-action text-right" v-bind:style="{ 'padding-right': '0px' }" @click="confirmOrder('confirmOrder')">
            <button type="button" class="btn btn-lg btn-pos-primary" >
                <i class="fa fa-credit-card"></i> {{ $t('pos_payment.pos_cash.button_confirm_pay') }}
            </button>
        </div>

        <div v-if="this.$root.posCommonModal.confirmOrder">
            <pos-common-modal id="confirmOrder" :is-open="this.$root.posCommonModal.confirmOrder">
                <h4 slot="header">{{ $t('pos_home.pos_cart.text_confirm') }}</h4>
                
                <div slot="body">
                    <div class="message-alert .text-default"> {{ $t('pos_home.pos_cart.text_confirm_msg') }} </div>
                    <div class="pos-action text-right" v-bind:style="{ 'padding-right': '0px' }">
                        <button type="button" class="btn btn-lg btn-pos-dark" @click="validateOrder">
                            <i class="fa fa-check-circle"></i> {{ $t('pos_home.pos_cart.text_confirm') }}
                        </button>
                        <button type="button" class="btn btn-lg btn-pos-default" @click="hideCommonModal('confirmOrder')">
                            {{ $t('pos_home.pos_cart.text_cancel') }}
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
                pos_banks: [],
                bank_name: null,
                card_detail: null,
                active_digit: '',
                cart_customer: [],
                customer_status: false,
                current_currency: window.pos_currency_symbol,
                tendered_amount: 0,
                tendered_string: '',
                change_amount: 0,
                order_comment: '',
                pos_cashier: {},
                pos_carts: [],
                pos_current_cart: 0,
                order_items: [],
                total_cart_item: 0,
                total_cart_qty: 0,                
                discount_total: 0,
                base_discount_total: 0,

                sub_total: 0,
                base_sub_total: 0,
                
                tax_total: 0,
                base_tax_total: 0,

                grand_total: 0,
                base_grand_total: 0,

                tendered_limit: 10000000,
                online_order: {},
                offline_order: {},
                
                months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        mounted() {
            this.getCartTotal();
            this.getBanks();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                    this.pos_cashier = this.localObject.pos_cashier;
                }
            },

            getBanks() {
                var self = this;
                if (self.localObject.pos_cashier.id) {
                    if (Object.keys(self.localObject.pos_banks).length > 0) {
                        self.pos_banks = self.localObject.pos_banks;
                    }
                }
            },

            getCartTotal() {                
                var thisthis = this;
                var total_item_count = 0;
                var total_item_qty = 0;
                
                var cart_sub_total = 0;
                var base_cart_sub_total = 0;
                
                var cart_tax_total = 0;
                var base_cart_tax_total = 0;

                var cart_grand_total = 0;
                var base_cart_grand_total = 0;
                
                if (Object.keys(thisthis.localObject.pos_carts).length > 0) {
                    thisthis.pos_carts = thisthis.localObject.pos_carts;
                }

                if (thisthis.localObject.pos_current_cart != 'null') {
                    thisthis.pos_current_cart = thisthis.localObject.pos_current_cart;
                }

                if (thisthis.localObject.pos_cart_customer.email) {
                    thisthis.customer_status = true;
                    thisthis.cart_customer = thisthis.localObject.pos_cart_customer;
                }

                if (thisthis.pos_carts[thisthis.pos_current_cart] && Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length > 0) {

                    $.each(thisthis.pos_carts[thisthis.pos_current_cart], (key, product) => {
                        total_item_count = total_item_count + 1;

                        total_item_qty = parseInt(total_item_qty) + parseInt(product.qty_ordered);

                        cart_sub_total = (cart_sub_total + parseFloat(product.total));
                        base_cart_sub_total = (base_cart_sub_total + parseFloat(product.base_total));

                        if (product.tax_category_id != null && product.tax_category_id && product.tax_percent) {
                            cart_tax_total = (cart_tax_total + ( (parseFloat(product.total) * product.tax_percent) / 100 ) ) ;
                            
                            base_cart_tax_total = (base_cart_tax_total + ( (parseFloat(product.base_total) * product.tax_percent) / 100 ) ) ;
                        }
                    });

                    thisthis.total_cart_item = total_item_count;
                    thisthis.total_cart_qty = total_item_qty;

                    thisthis.sub_total = parseFloat(cart_sub_total).toFixed(2);
                    thisthis.base_sub_total = parseFloat(base_cart_sub_total).toFixed(2);

                    thisthis.tax_total = parseFloat(cart_tax_total).toFixed(2);
                    thisthis.base_tax_total = parseFloat(base_cart_tax_total).toFixed(2);

                    if ( thisthis.localObject.pos_discount.id || (thisthis.localObject.pos_discount.id == 0) ) {

                        if ( (parseFloat(thisthis.localObject.pos_discount.converted_fromprice) <= parseFloat(thisthis.sub_total)) && parseFloat(thisthis.localObject.pos_discount.converted_toprice) >= parseFloat(thisthis.sub_total) ) {

                            if ( thisthis.localObject.pos_discount.type == 'percentage' ) {
                                thisthis.discount_total = parseFloat((thisthis.sub_total * thisthis.localObject.pos_discount.value) / 100).toFixed(2);
                                thisthis.base_discount_total = parseFloat((thisthis.base_sub_total * thisthis.localObject.pos_discount.value) / 100).toFixed(2);
                            } else {
                                thisthis.discount_total = parseFloat(thisthis.localObject.pos_discount.converted_value).toFixed(2);
                                thisthis.base_discount_total = parseFloat(thisthis.localObject.pos_discount.converted_value).toFixed(2);
                            }
                        }
                    }
    
                    if (thisthis.discount_total == 'NaN') {
                        thisthis.discount_total = 0;
                        thisthis.base_discount_total = 0;
                    }

                    cart_grand_total = (((parseFloat(thisthis.sub_total) + parseFloat(thisthis.tax_total)) - thisthis.discount_total));

                    base_cart_grand_total = (((parseFloat(thisthis.base_sub_total) + parseFloat(thisthis.base_tax_total)) - thisthis.base_discount_total));


                    thisthis.grand_total = parseFloat(cart_grand_total).toFixed(2);
                    thisthis.base_grand_total = parseFloat(base_cart_grand_total).toFixed(2);
                }
            },

            confirmOrder(modalId) {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        EventBus.$emit('showCommonModal', modalId);
                    }
                });
            },

            hideCommonModal(modalId) {
                EventBus.$emit('hideCommonModal', modalId);
            },

            validateOrder() {
                this.$root.toggleButtonDisable(true);
                
                var virtual_product = 0;
                var validate_status = false;
                var error_message   = '';
                var thisthis        = this;

                if (Object.keys(thisthis.localObject.pos_carts).length == 0) {
                    var validate_status = true;
                    error_message = this.$t('pos_home.pos_cart.error.error_no_cart');
                }

                if (thisthis.localObject.pos_carts[thisthis.localObject.pos_current_cart] && Object.keys(thisthis.localObject.pos_carts[thisthis.localObject.pos_current_cart]).length == 0) {
                    var validate_status = true;
                    error_message = this.$t('pos_home.pos_cart.error.error_no_product');
                }

                if ( !thisthis.localObject.pos_cart_customer.email ) {
                    var validate_status = true;
                    error_message = this.$t('pos_home.pos_cart.error.error_no_customer');
                }

                if (validate_status) {
                    window.flashMessages = [{'type': 'alert-warning', 'message': error_message }];

                    this.$root.addFlashMessages();
                } else {
                    thisthis.online_order = {};
                    thisthis.offline_order = {};
                    
                    var customer_data = {
                        id: thisthis.cart_customer.id,
                        first_name: thisthis.cart_customer.first_name,
                        last_name: thisthis.cart_customer.last_name,
                        email: thisthis.cart_customer.email,
                        gender: thisthis.cart_customer.gender,
                        date_of_birth: thisthis.cart_customer.date_of_birth,
                        phone: thisthis.cart_customer.phone,
                        status: thisthis.cart_customer.status,
                        is_verified: thisthis.cart_customer.is_verified,
                        customer_group_id: thisthis.cart_customer.customer_group_id,
                    };
                    thisthis.online_order.customer = customer_data;
                    
                    thisthis.offline_order.customer = customer_data;

                    // Offline Shipping Address Details
                    var cashier_address = thisthis.pos_cashier.outlet_address;
                    var shipping_address =  {
                        first_name: customer_data.first_name,
                        last_name: customer_data.last_name,
                        email: customer_data.email,
                        address1: cashier_address.address1,
                        address2: cashier_address.address2,
                        country: cashier_address.country,
                        city: cashier_address.city,
                        postcode: cashier_address.postcode,
                        phone: customer_data.phone,
                        address_type: "shipping",
                    };
                    thisthis.offline_order.shipping_address = shipping_address;
                    
                    // Offline Billing Address Details
                    var billing_address =  {
                        first_name: customer_data.first_name,
                        last_name: customer_data.last_name,
                        email: customer_data.email,
                        phone: customer_data.phone,
                        address_type: "billing",
                    };
                    if (thisthis.cart_customer.addresses && Object.keys(thisthis.cart_customer.addresses).length > 0) {
                        billing_address = thisthis.cart_customer.addresses;
                        delete billing_address.default_address;
                        delete billing_address.name;
                        delete billing_address.id;
                    } else {
                        billing_address.address1 = cashier_address.address1;
                        billing_address.address2 = cashier_address.address2;
                        billing_address.country = cashier_address.country;
                        billing_address.city = cashier_address.city;
                        billing_address.postcode = cashier_address.postcode;
                    }
                    thisthis.offline_order.billing_address = billing_address;

                    thisthis.offline_order.payment = {
                        method: 'cashondelivery',
                        method_title: 'Card Payment',
                    };
                    thisthis.offline_order.payment_mode = 'card';
                    thisthis.offline_order.bank_name = thisthis.bank_name;
                    thisthis.offline_order.card_detail = thisthis.card_detail;

                    var total_item_count = Object.keys(thisthis.localObject.pos_carts[thisthis.localObject.pos_current_cart]).length;
                    thisthis.offline_order.total_item_count = thisthis.total_cart_item;
                    thisthis.offline_order.total_qty_ordered = thisthis.total_cart_qty;
                    
                    thisthis.offline_order.base_currency_code = window.base_currency.code;
                    thisthis.offline_order.channel_currency_code = window.pos_currency_code;
                    thisthis.offline_order.order_currency_code = window.pos_currency_code;

                    thisthis.offline_order.items = thisthis.pos_carts[thisthis.pos_current_cart];

                    thisthis.offline_order.sub_total = thisthis.sub_total;
                    thisthis.offline_order.base_sub_total = thisthis.base_sub_total;

                    thisthis.offline_order.tax_amount = thisthis.tax_total;
                    thisthis.offline_order.base_tax_amount = thisthis.base_tax_total;

                    thisthis.offline_order.discount_amount = thisthis.discount_total;
                    thisthis.offline_order.base_discount_amount = thisthis.base_discount_total;
                    
                    thisthis.offline_order.grand_total = thisthis.grand_total;
                    thisthis.offline_order.base_grand_total = thisthis.base_grand_total;

                    thisthis.offline_order.tendered_amount = thisthis.grand_total;
                    thisthis.offline_order.change_amount = 0;
                    
                    var current_date_time = new Date();
                    var current_month = current_date_time.getMonth();

                    if (current_month < 10) {
                        current_month = '0'+current_month;
                    }
                    thisthis.offline_order.created_at = current_date_time.getFullYear() + '-' + current_month + '-' + current_date_time.getDate() + ' ' + current_date_time.getHours() + ':' + current_date_time.getMinutes() + ':' + current_date_time.getSeconds();

                    thisthis.online_order.tendered_amount = thisthis.grand_total;

                    thisthis.online_order.ref_id = customer_data.first_name.charAt(0) + customer_data.last_name.charAt(0) + '-' + current_date_time.getTime();

                    thisthis.online_order.payment_mode = 'card';

                    thisthis.online_order.order_items = [];
                    var cart_products = Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length;

                    var booking_id = '';

                    $.each(thisthis.pos_carts[thisthis.pos_current_cart], (key, product) => {
                        {
                            if ( product.type == 'virtual' || product.type == 'downloadable' || product.type == 'booking') {
                                virtual_product += 1;
                            }
                            if ( product.booking_id ) {
                                booking_id = product.booking_id;
                            }
                            var cart_product = {
                                id: product.id,
                                sku: product.sku,
                                type: product.type,
                                qty_ordered: product.qty_ordered,
                            };
                            if ( product.id == 0) {
                                cart_product.name = product.name;
                                cart_product.price = product.price;
                                cart_product.weight = product.weight;
                            }
                            if ( product.type != 'simple' && product.type != 'virtual') {
                                cart_product.additional = product.additional;
                            }
                            thisthis.online_order.order_items.push(cart_product);
                        }
                    });

                    if ( cart_products == virtual_product) {
                        thisthis.offline_order.no_shipping = true;
                    }

                    if ( thisthis.localObject.pos_discount.id ) {
                        thisthis.online_order.discount_id = thisthis.localObject.pos_discount.id;
                    }

                    thisthis.pos_order                  = {};
                    thisthis.pos_order.user_id          = thisthis.user_id;
                    thisthis.pos_order.booking_id       = booking_id;
                    thisthis.pos_order.outlet_id        = thisthis.pos_cashier.outlet_id;
                    thisthis.pos_order.bank_name        = thisthis.bank_name;
                    thisthis.pos_order.card_detail      = thisthis.card_detail;
                    thisthis.pos_order.order_note       = thisthis.order_comment;
                    thisthis.pos_order.order_currency   = window.pos_currency_code;
                    thisthis.pos_order.order_date       = this.months[current_date_time.getMonth()] + ' ' + current_date_time.getDate() + ', ' + current_date_time.getFullYear();
                    thisthis.pos_order.order_time       = current_date_time.getHours() + ':' + current_date_time.getMinutes() + ':' + current_date_time.getSeconds();
                    thisthis.pos_order.order_date_time  = thisthis.pos_order.order_date + ' ' + thisthis.pos_order.order_time;

                    thisthis.online_order.pos_order = thisthis.pos_order;

                    if ( thisthis.online_order ) {
                        EventBus.$emit('createOrder', thisthis.online_order, thisthis.offline_order);
                    }
                }
            }
        }
    }
</script>