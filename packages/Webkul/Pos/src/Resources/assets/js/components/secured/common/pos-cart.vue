<template>
    <div class="pos-content">
        <div class="cart" v-if="getCartTotal">
            <div class="cart-header">
                <div class="cart-hold-section">
                    <span v-if="!booked_table.booking_id"> {{ $t("pos_home.pos_cart.cart_details") }} </span>
                    <span v-if="booked_table.booking_id" v-text="booked_table.table_name"></span>
                    <div class="btn btn-sm btn-pos-default btn-hold" @click="redirectToHoldCart">
                        <i class="fa fa-pause"></i>
                        <span class="hold_cart_count">{{ hold_count }}</span>
                    </div>
                </div>

                <div class="cart-count-section">
                    <div v-if="showMinusBtn" class="btn btn-sm btn-pos-primary cart-btn" v-bind:selctedcart="pos_current_cart" :title="$t('pos_home.pos_cart.delete_current_cart')" @click="removeCart(false)">
                        <i class="fa fa-minus"></i>
                    </div>

                    <ul class="pos-nav-lists">
                        <li v-for="(cart, index) in pos_carts" :key="index" :class="{ 'pos-nav': true, 'active': index == pos_current_cart }" :label="'cart_count_' + index" @click="activeCartNav(index)">
                            {{ index + 1 }}
                        </li>
                    </ul>

                    <div class="btn btn-sm btn-pos-primary cart-btn" @click="addCart">
                        <i class="fa fa-plus"></i>
                    </div>
                </div>
            </div>
            <div class="pos-nav-content">
                <div v-for="(cart, index) in pos_carts" :key="cart.id" :class="{ 'pos-nav-pane': true, 'active': index == pos_current_cart }" :id="'cart_count_' + index">
                    
                    <ul class="cart_details">
                        <div v-if="cartEmpty" class="message-alert">
                            <span class="text-danger">{{ $t('pos_home.pos_cart.text_cart_empty') }}</span>
                        </div>
                        <div v-else >
                        <li v-for="(cart_detail, index) in cart" :class="{'active': cart_detail.active}">
                            <div class="cart-product-content" @click="makeActiveProduct(cart_detail, index)">
                                <div class="product-name">
                                    {{ cart_detail.name }}

                                    <span class="product-attributes" v-if="cart_detail.additional" >
                                        <span v-for="attribute in cart_detail.additional.attributes">
                                            <i v-if="attribute && attribute.attribute_name"> <b>{{ attribute.attribute_name }}: </b> {{ attribute.option_label }}, </i>
                                        </span>
                                    </span>
                                </div>
                                <div class="product-qty">
                                    <span>
                                        <span class="qty-minus" @click="minusProductQty(cart_detail, index)">
                                            <i class="fa fa-minus-circle"></i>
                                        </span>

                                            {{ cart_detail.qty_ordered }} {{ $t("pos_home.pos_cart.text_unit") }}
                                        
                                        <span class="qty-plus" @click="plusProductQty(cart_detail, index)">
                                            <i class="fa fa-plus-circle"></i>
                                        </span>                                    
                                    - {{ cart_detail.f_price }} {{ $t("pos_home.pos_cart.text_per_unit") }}
                                    </span>                                    
                                </div>
                            </div>
                            <div class="cart-product-price">
                                <span>
                                    <i @click="deleteCartProduct(cart_detail, index)" class="fa fa-times-circle"></i>
                                </span>
                                <span>
                                    {{ cart_detail.f_total }}
                                </span>
                            </div>
                        </li>
                        </div>
                    </ul>
                </div>
            </div>
        </div>

        <div class="cart-total-container">
            <div class="cart-total">
                <div class="pos-table-responsive cart-totals">
                    <table class="pos-table">
                        <tbody>
                            <tr>
                                <td class="text-left">{{ $t("pos_home.pos_cart.text_sub_total") }}</td>
                                <td class="text-right" id="subtotal">
                                    {{ currency }} {{ sub_total }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-left" id="discname">
                                    {{ $t("pos_home.pos_cart.text_discount") }} {{ currency }}
                                    <i v-if="!cartEmpty" class="fa fa-caret-down" @click="openDiscountModal('addDiscountToCart')"></i>
                                </td>
                                <td class="text-right" id="discount">
                                    <!-- <input type="text" name="discount" :disabled="!discount_disabled" class="control" v-model="cart_discount" /> -->
                                    {{ currency }} {{ cart_discount }}
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left" id="taxname">{{ $t("pos_home.pos_cart.text_tax_total") }}</td>
                                <td class="text-right" id="tax">{{ currency }} {{ tax_total }}</td>
                            </tr>
                            
                            <tr class="hide">
                                <td class="text-left">{{ $t("pos_home.pos_cart.text_home_delivery") }}</td>
                                <td class="text-right">
                                    <input type="number" class="form-field" placeholder="">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-left">{{ $t("pos_home.pos_cart.text_grand_total") }}</td>
                                <td class="text-right">{{ currency }} {{ grand_total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cart-button-container pos-action">
                <button type="button" class="btn btn-lg btn-pos-primary customer-btn" @click="changeCustomer">
                    <i class="fa fa-user-circle"></i>
                    <span v-if="this.localObject.pos_cart_customer.name" :title="this.localObject.pos_cart_customer.name">
                        <div v-if="this.localObject.pos_cart_customer.name.length < 10">
                            {{ this.localObject.pos_cart_customer.name }}
                        </div>
                        <div v-else >
                            {{ this.localObject.pos_cart_customer.name.substring(0,10)+".." }}
                        </div>
                    </span>
                    <span v-else >
                        <b>{{ $t('pos_home.pos_cart.button_customer') }}</b>
                    </span>
                    <i class="fa fa-pencil"></i>
                </button>
                <button type="button" class="btn btn-lg btn-pos-dark pay-btn" @click="payForCart">
                    <b>{{ $t('pos_home.pos_cart.button_pay') }}</b>
                </button>
                <button type="button" class="btn btn-lg btn-pos-primary hold-btn" @click="showHoldNoteModal('holdCart')">
                    <i class="fa fa-pause"></i> <b>{{ $t('pos_home.pos_cart.button_hold') }}</b>
                </button>
            </div>
        </div>
        <div v-if="this.$root.posCommonModal.addDiscountToCart">
            <pos-common-modal id="addDiscountToCart" :is-open="this.$root.posCommonModal.addDiscountToCart">
                <h4 slot="header">{{ $t('pos_home.pos_cart.apply_discount') }}</h4>
                
                <div slot="body">
                    <discount-list
                        :pos_current_cart='pos_current_cart'
                        :pos_carts='pos_carts'
                        :pos_discounts='pos_discounts'
                        :localObject='localObject'
                    ></discount-list>
                </div>
            </pos-common-modal>
        </div>

        <div v-if="this.$root.posCommonModal.holdCart">

            <pos-common-modal id="holdCart" :is-open="this.$root.posCommonModal.holdCart">
                <h4 slot="header">{{ $t('pos_home.pos_cart.hold_order_note') }}</h4>
                
                <div slot="body">
                    <hold-note
                        :pos_current_cart='pos_current_cart'
                        :pos_carts='pos_carts'
                        :localObject='localObject'
                    ></hold-note>
                </div>
            </pos-common-modal>
        </div>

        <div v-if="this.$root.posCommonModal.addCustomProduct">

            <pos-common-modal id="addCustomProduct" :is-open="this.$root.posCommonModal.addCustomProduct">
                <h4 slot="header">{{ $t('pos_home.navtop.add_custom_product') }}</h4>
                
                <div slot="body">
                    <custom-product-form
                        :pos_current_cart='pos_current_cart'
                        :pos_carts='pos_carts'
                        :localObject='localObject'
                    ></custom-product-form>
                </div>
            </pos-common-modal>
        </div>
    </div>
</template>

<script>

    export default {
        props: ['pos_carts', 'pos_current_cart', 'showMinusBtn', 'cartCustomer', 'hold_count', 'localObject'],
        data() {
            return {
                pos_offline: 0,
                customer_id: 0,
                current_opration: 'qty',
                currency: '',
                sub_total: 0,
                cart_discount: 0,
                pos_discounts: [],
                discount_disabled: false,
                tax_total: 0,
                grand_total: 0,
                cartEmpty: true,

                //Pos Restaurant
                booked_table: {},

                text_cart_empty: this.$t('pos_home.pos_cart.error.error_cart_empty'),
                text_customer_empty: this.$t('pos_home.pos_cart.error.error_customer_empty'),
                current_currency_symbol: window.pos_currency_symbol,
            };
        },
        computed: {
            getCartTotal () {
                this.pos_offline = this.$root.$root.offline;

                this.updateCartTotal();
                
                this.checkBookedCart();

                return true;
            }
        },
        watch: {
            cartCustomer: function (updated_customer) {
                this.cartCustomer = updated_customer;
                this.localObject.pos_cart_customer = updated_customer;
                EventBus.$emit('setLocalForage', {'key': 'pos_cart_customer', 'data': JSON.stringify(updated_customer)});
            }
        },
        beforeDestroy () {
            EventBus.$off('onshowCartContent');
        },
        created() {
            EventBus.$on('removeOrderedCart', (removeStatus) => {
                this.removeCart(removeStatus);
            });
            EventBus.$on('onshowCartContent', () => {
                this.showCartContent();
            });
            EventBus.$on('onCartUpdate', () => {
                this.updateCartTotal();
            });
            EventBus.$on('hideNoteModal', (modal_id) => {
                this.$root.$set(this.$root.posCommonModal, modal_id, false);
            });
            EventBus.$on('resetCartDiscount', () => {
                this.cart_discount = 0;
                this.discount_disabled = false;
                this.localObject.pos_discount = {};
            });
        },
        mounted() {
            this.currency = window.pos_currency_symbol;
        },
        methods: {
            checkBookedCart() {
                var self = this;
                
                if ( Object.keys(self.localObject.pos_carts).length > 0) {
                    
                    if (Object.keys(self.localObject.pos_carts[self.localObject.pos_current_cart]).length > 0) {
                        $.each(self.localObject.pos_carts[self.localObject.pos_current_cart], function(key, product) {
                            if (! product.booking_id ) {
                                self.booked_table = {};
                            } else {
                                if ( self.localObject.pos_current_booking && Object.keys(self.localObject.pos_current_booking).length > 0 ) {
                                    self.booked_table = self.localObject.pos_current_booking;
                                }        
                            }
                        });
                    } else {
                        
                        if ( self.localObject.pos_current_booking && Object.keys(self.localObject.pos_current_booking).length > 0 ) {
                            self.booked_table = self.localObject.pos_current_booking;
                        }
                    }

                } else {
                    if ( self.localObject.pos_current_booking && Object.keys(self.localObject.pos_current_booking).length > 0 ) {
                        self.booked_table = self.localObject.pos_current_booking;
                    }
                }
            },
            removeCart(removeStatus) {
                this.cart_discount = 0;
                this.discount_disabled = false;
                this.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');

                var next_cart = this.pos_current_cart;

                if (Object.keys(this.pos_carts).length > 1) {

                    this.pos_carts.splice(this.pos_current_cart, 1);
                    
                    next_cart = 0;
                    
                    this.booked_table = {};
                    this.localObject.pos_current_booking = {};
                    EventBus.$emit('setLocalForage', {'key': 'pos_current_booking', 'data': JSON.stringify(this.localObject.pos_current_booking)});

                    if (Object.keys(this.pos_carts).length == 1) {
                        EventBus.$emit('changeMinusBtn', false);
                    }
                } else {
                    if (removeStatus) {
                        this.pos_carts.splice(this.pos_current_cart, 1);
                        next_cart = 0;

                        if (this.pos_carts.length == 0) {
                            this.pos_carts[0] = {};
                            this.pos_current_cart = 0;
                        }
                    }
                    this.booked_table = {};
                    this.localObject.pos_current_booking = {};
                    EventBus.$emit('setLocalForage', {'key': 'pos_current_booking', 'data': JSON.stringify(this.localObject.pos_current_booking)});

                    EventBus.$emit('changeMinusBtn', false);
                }

                EventBus.$emit('cartActive', next_cart);

                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.pos_carts)});
            },

            addCart() {
                this.cart_discount = 0;
                this.discount_disabled = false;
                this.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');

                this.booked_table = {};
                this.localObject.pos_current_booking = {};
                this.$root.$setItem('pos_current_booking', JSON.stringify(this.localObject.pos_current_booking));

                EventBus.$emit('addNewCart');
            },

            activeCartNav(cartActive) {
                var self = this;
                $.each(self.pos_carts[cartActive], function(key, product) {
                    if (! product.booking_id ) {
                        self.booked_table = {};
                    } else {
                        $.each(self.localObject.pos_restaurant_bookings, function(key, booking) {
                            if ( booking.booking_id == product.booking_id) {
                                self.booked_table = booking;
                                self.localObject.pos_current_booking = booking;
                                self.$root.$setItem('pos_current_booking', JSON.stringify(self.localObject.pos_current_booking));
                            }
                        });
                    }
                });

                EventBus.$emit('cartActive', cartActive);
            },

            changeCustomer() {
                this.$router.push({name: 'pos_customer'});
            },

            redirectToHoldCart() {
                this.$router.push({name: 'pos_sales_hold'});
            },

            checkOrderProduct(orderItems, product) {
                var orderProductQty = 0;
                $.each(orderItems, function(key, orderProduct) {
                    if ( parseInt(orderProduct.id) == parseInt(product.id) ) {
                        orderProductQty = orderProductQty + orderProduct.qty_ordered;
                    }
                });
                return orderProductQty;
            },

            plusProductQty(cartProduct, index) {
                var thisthis                    = this;
                var quantity                    = 0;
                var productObjs                 = this.localObject.pos_products;
                    thisthis.cart_discount      = 0;
                    thisthis.discount_disabled  = false;
                    
                    thisthis.localObject.pos_discount = {};
                    EventBus.$emit('deleteLocalForage', 'pos_discount');

                if (cartProduct.id == 0 && cartProduct.sku == 'custom') {
                    quantity = JSON.stringify({"0" : cartProduct.qty_ordered});
                } else {
                    if ( cartProduct.type == 'configurable'
                        || cartProduct.type == 'bundle'
                        || cartProduct.type == 'downloadable'
                        || cartProduct.type == 'booking'
                        ) {
                        
                        var pos_qty             = {};
                        var product_id          = cartProduct.id;
                            pos_qty[product_id] = 1;
                            quantity            = JSON.stringify(pos_qty);
                            
                        if ( cartProduct.type == 'configurable' || cartProduct.type == 'booking' ) {
                            var obj = $.grep(this.localObject.pos_products, function(obj){return obj.id === cartProduct.additional.product_id;})[0];
                            quantity = obj.quantity;

                            if ( cartProduct.type == 'booking' ) {
                                var boooking_options    = JSON.parse(obj.booking_options);
                                pos_qty[product_id]     = boooking_options.qty;
                                quantity                = JSON.stringify(pos_qty);
                            }
                        }
                    } else {
                        var obj = $.grep(this.localObject.pos_products, function(obj){return obj.id === cartProduct.id;})[0];
                        quantity = obj.quantity;
                    }
                }
                
                cartProduct.quantity    = quantity;
                var remaining_quantity  = thisthis.validateProductRemainingQty(cartProduct);
                                
                if ( ( typeof remaining_quantity == 'number' && parseInt(remaining_quantity) > 0 ) ) {
                    cartProduct.qty_ordered = parseInt(cartProduct.qty_ordered) + 1;
                    
                    // Product Customer Group Price
                    var customer_prices = {
                            price:              cartProduct.base_price,
                            converted_price:    cartProduct.price,
                            formated_price:     cartProduct.f_price,
                        };
                    
                    if ( cartProduct.type == 'simple'
                        || cartProduct.type == 'virtual'
                        || cartProduct.type == 'downloadable'
                        ) {
                            var productObj = $.grep(productObjs, function(prod_obj){return prod_obj.id === cartProduct.id;})[0];

                            if ( cartProduct.type == 'downloadable' ) {
                                var download_attributes = cartProduct.additional.attributes;
                                var download_links = download_attributes[cartProduct.additional.product_id].download_links;

                                var option_price = {price: 0, converted_price: 0};

                                $.each(download_links, function(key, link) {
                                    option_price.price = parseFloat(option_price.price) + parseFloat(link.price);
                                    option_price.converted_price = parseFloat(option_price.converted_price) + parseFloat(link.converted_price);
                                });

                                customer_prices = thisthis.calculateCustomerGroupPrice(productObj, cartProduct.qty_ordered);

                                customer_prices.converted_price  = parseFloat(customer_prices.converted_price) + parseFloat(option_price.converted_price);
                                                    
                                customer_prices.price  = parseFloat(customer_prices.price) + parseFloat(option_price.price);
                                
                                customer_prices.formated_price  = thisthis.current_currency_symbol + parseFloat(customer_prices.converted_price).toFixed(2);
                            } else {
                                customer_prices = thisthis.calculateCustomerGroupPrice(productObj, cartProduct.qty_ordered);
                            }
                    }

                    if (cartProduct.special_price != 'NaN' && cartProduct.special_price) {
                        cartProduct.total       = parseFloat(cartProduct.qty_ordered * cartProduct.special_price).toFixed(2);
                    } else {
                        cartProduct.total       = parseFloat(cartProduct.qty_ordered * customer_prices.converted_price).toFixed(2);
                        cartProduct.base_total  = parseFloat(cartProduct.qty_ordered * customer_prices.price).toFixed(2);
                    }
                    cartProduct.f_price = customer_prices.formated_price;
                    cartProduct.f_total = window.pos_currency_symbol + parseFloat(cartProduct.total).toFixed(2);

                    thisthis.pos_carts[thisthis.pos_current_cart][index] = cartProduct;

                    EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
                } else {
                    thisthis.$toast.error(thisthis.$t('pos_home.pos_products.error.no_quantity'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            },
            
            minusProductQty(cartProduct, index) {
                var thisthis                            = this;
                var productObjs = this.localObject.pos_products;
                    thisthis.cart_discount              = 0;
                    thisthis.discount_disabled          = false;
                    thisthis.localObject.pos_discount   = {};
                    EventBus.$emit('deleteLocalForage', 'pos_discount');

                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, product) {
                    if (key == index) {
                        if ( product.qty_ordered > 1 ) {
                            product.qty_ordered -= 1;

                            // Product Customer Group Price
                            var customer_prices = {
                                    price:              product.base_price,
                                    converted_price:    product.price,
                                    formated_price:     product.f_price,
                                };
                            
                            if ( product.type == 'simple'
                                || product.type == 'virtual'
                                || product.type == 'downloadable'
                                ) {
                                    var productObj = $.grep(productObjs, function(prod_obj){return prod_obj.id === product.id;})[0];

                                    if ( product.type == 'downloadable' ) {
                                        var download_attributes = product.additional.attributes;
                                        var download_links = download_attributes[product.additional.product_id].download_links;

                                        var option_price = {price: 0, converted_price: 0};

                                        $.each(download_links, function(key, link) {
                                            option_price.price = parseFloat(option_price.price) + parseFloat(link.price);
                                            option_price.converted_price = parseFloat(option_price.converted_price) + parseFloat(link.converted_price);
                                        });

                                        customer_prices = thisthis.calculateCustomerGroupPrice(productObj, product.qty_ordered);

                                        customer_prices.converted_price  = parseFloat(customer_prices.converted_price) + parseFloat(option_price.converted_price);
                                                            
                                        customer_prices.price  = parseFloat(customer_prices.price) + parseFloat(option_price.price);
                                        
                                        customer_prices.formated_price  = thisthis.current_currency_symbol + parseFloat(customer_prices.converted_price).toFixed(2);
                                    } else {
                                        customer_prices = thisthis.calculateCustomerGroupPrice(productObj, product.qty_ordered);
                                    }
                            }

                            if (product.special_price != 'NaN' && product.special_price) {
                                product.total = parseFloat(product.qty_ordered * product.special_price).toFixed(2);
                            } else {
                                product.total = parseFloat(product.qty_ordered * customer_prices.converted_price).toFixed(2);
                                product.base_total = parseFloat(product.qty_ordered * customer_prices.price).toFixed(2);
                            }

                            product.f_price = customer_prices.formated_price;
                            product.f_total = window.pos_currency_symbol + parseFloat(product.total).toFixed(2);

                            thisthis.pos_carts[thisthis.pos_current_cart][key] = product;

                            EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
                        } else {
                            thisthis.deleteCartProduct(product, index);
                        }
                    }
                });
            },

            deleteCartProduct(cart_product, index) {
                var thisthis                    = this;
                var checkActive                 = false;
                var removeCartProduct           = {};
                    thisthis.cart_discount      = 0;
                    thisthis.discount_disabled  = false;
                    thisthis.localObject.pos_discount = {};
                    EventBus.$emit('deleteLocalForage', 'pos_discount');
                
                var total_product = Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length;
                
                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(product_key, product_data) {
                    if (product_key == index) {
                        if (product_data.active == true && total_product > 0) {
                            checkActive = true;
                        }
                        delete thisthis.pos_carts[thisthis.pos_current_cart][index];
                    } else {
                        removeCartProduct[product_key] = product_data;
                    }
                });

                if ( checkActive ) {
                    var product_count = Object.keys(removeCartProduct).length;
                    if ( product_count > 0 ) {
                        var first_index = Object.keys(removeCartProduct)[0];
                        removeCartProduct[first_index].active = true;
                    }
                }

                thisthis.$set(thisthis.pos_carts, thisthis.pos_current_cart, removeCartProduct);
                
                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
            },

            makeActiveProduct(cartProduct, index) {
                var thisthis = this;
                var cart_products = {};
                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, product) {
                    product.active = false;
                    if (key == index) {
                        product.active = true;
                    }
                    cart_products[key] = product;
                });
                
                this.$set(thisthis.pos_carts, thisthis.pos_current_cart, cart_products);

                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
            },

            updateCartTotal() {
                var thisthis = this;
                var cart_sub_total = 0;
                var cart_tax_total = 0;
                var cart_grand_total = 0;

                thisthis.discount_disabled = false;
                thisthis.cartEmpty = true;

                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, product) {
                    // thisthis.discount_disabled = true;
                    thisthis.cartEmpty = false;
                    cart_sub_total = (cart_sub_total + parseFloat(product.total));

                    if (product.tax_category_id != null && product.tax_category_id && product.tax_percent) {
                        cart_tax_total = (cart_tax_total + ( (parseFloat(product.total) * product.tax_percent) / 100 ) ) ;
                    }
                });

                thisthis.sub_total = parseFloat(cart_sub_total).toFixed(2);
                
                thisthis.tax_total = parseFloat(cart_tax_total).toFixed(2);

                thisthis.sub_total_tax = (parseFloat(thisthis.sub_total) + parseFloat(thisthis.tax_total));

                if ( thisthis.pos_carts[thisthis.pos_current_cart] && Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length > 0 ) {

                    if ( thisthis.localObject.pos_discount.id) {

                        if ( parseFloat(thisthis.localObject.pos_discount.converted_fromprice) <= parseFloat(thisthis.sub_total) && parseFloat(thisthis.localObject.pos_discount.converted_toprice) >= parseFloat(thisthis.sub_total) ) {

                            if ( thisthis.localObject.pos_discount.type == 'percentage' ) {
                                thisthis.cart_discount = parseFloat((thisthis.sub_total * thisthis.localObject.pos_discount.value) / 100).toFixed(2);
                            } else {
                                thisthis.cart_discount = parseFloat(thisthis.localObject.pos_discount.converted_value).toFixed(2);
                            }
                        }
                    } else {
                        thisthis.localObject.pos_discount = {
                            id: 0,
                            user_id: thisthis.localObject.pos_cashier.id,
                            offername: '',
                            fromprice: thisthis.sub_total_tax,
                            converted_fromprice: thisthis.sub_total_tax,
                            toprice: thisthis.sub_total_tax,
                            converted_toprice: thisthis.sub_total_tax,
                            type: 'fix',
                            value: thisthis.cart_discount
                        };
                        EventBus.$emit('setLocalForage', {'key': 'pos_discount', 'data': JSON.stringify(thisthis.localObject.pos_discount)});
                    }
                }

                if (thisthis.cart_discount == 'NaN' || (thisthis.cart_discount > thisthis.sub_total_tax)) {
                    thisthis.cart_discount = 0;
                    var applied_discount = {};
                    EventBus.$emit('setLocalForage', {'key': 'pos_discount', 'data': JSON.stringify(applied_discount)});
                }

                cart_grand_total = ((thisthis.sub_total_tax - thisthis.cart_discount));

                thisthis.grand_total = parseFloat(cart_grand_total).toFixed(2);

            },
            
            openDiscountModal(modalId) {
                var thisthis = this;
                if (thisthis.pos_offline) {
                    if (Object.keys(thisthis.localObject.pos_discounts).length > 0) {
                        
                        let getFilterDiscounts = [];
                        if (thisthis.sub_total) {
                            $.each(thisthis.localObject.pos_discounts, (key, discount) => {
                                if ( (parseFloat(discount.converted_fromprice) <= parseFloat(thisthis.sub_total)) && (parseFloat(discount.converted_toprice) >= parseFloat(thisthis.sub_total)) ) {
                                    getFilterDiscounts = getFilterDiscounts.concat(discount);
                                }
                            });
                            thisthis.pos_discounts = getFilterDiscounts;
                        } else {
                            thisthis.pos_discounts = [];
                        }

                    } else {
                        thisthis.pos_discounts = [];
                    }
                } else {
                    
                    if (thisthis.localObject.pos_cashier.id) {
                        thisthis.$http.get('/api/pos/auth/getDiscounts', {
                            params: {
                                user_id: thisthis.localObject.pos_cashier.id,
                                filter_cart_total: thisthis.sub_total
                            }
                        })
                        .then((response)  =>  {
                            if ( response.data.data && response.data.data.length > 0) {
                                thisthis.pos_discounts = response.data.data;
                            } else {
                                thisthis.pos_discounts = [];
                            }
                        })
                        .catch(function (error) {});
                    }
                }
                EventBus.$emit('showCommonModal', modalId);
            },

            showHoldNoteModal(modalId) {
                if (Object.keys(this.pos_carts[this.pos_current_cart]).length > 0) {
                    EventBus.$emit('showCommonModal', modalId);
                } else {
                    this.$toast.error(this.text_cart_empty, {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            },

            payForCart() {
                var thisthis = this;

                // $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(product_key, cartProduct) {
                //     if ( cartProduct.type == 'configurable' || cartProduct.type == 'bundle' ) {
                //         var obj = $.grep(thisthis.localObject.pos_products, function(obj){
                //             if (obj.id === cartProduct.id) {

                //                 let variation_qty = JSON.parse(obj.quantity);
                //                 if (variation_qty[cartProduct.id] || cartProduct.type == 'bundle') {
                //                     return obj;
                //                 }
                //             }
                //         })[0];
                //     } else {
                //         if (cartProduct.id == 0) {
                //             cartProduct.quantity = JSON.stringify({"0" : cartProduct.qty_ordered});
                //             var obj = cartProduct;
                //         } else {
                //             var obj = $.grep(thisthis.localObject.pos_products, function(obj){return obj.id === cartProduct.id;})[0];
                //         }
                //     }

                //     var product_qty = JSON.parse(obj.quantity);
                //     var remaining_quantity = product_qty[cartProduct.id];

                //     if (Object.keys(thisthis.localObject.pos_offline_orders).length > 0) {
                //         var totalOrderProductQty = 0;
                //         $.each(thisthis.localObject.pos_offline_orders, function(key, offlineOrder) {
                //             totalOrderProductQty = totalOrderProductQty + thisthis.checkOrderProduct(offlineOrder.offline.items, cartProduct);
                //         });
                //         remaining_quantity = remaining_quantity - totalOrderProductQty;
                //     }
                //     // if (parseInt(cartProduct.qty_ordered) > parseInt(remaining_quantity)) {
                //     //     delete thisthis.pos_carts[thisthis.pos_current_cart][product_key];
                //     // }                    
                // });

                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});

                var cart_length = Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length;
                if (cart_length > 0) {
                    
                    if (thisthis.localObject.pos_cart_customer.email) {
                        thisthis.$router.push({name: 'pos_payment'});
                    } else {
                        thisthis.$toast.error(thisthis.text_customer_empty, {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                } else {
                    thisthis.$toast.error(thisthis.text_cart_empty, {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            },

            showCartContent() {
                this.updateCartTotal();
            },

            validateProductRemainingQty(product) {
                var thisthis            = this;
                var total_quantities    = [];
                var bundle_quantities   = [];
                var product_qty         = JSON.parse(product.quantity);
                if ( product.pos_qty ) {
                    var product_qty     = JSON.parse(product.pos_qty);
                }
                var remaining_quantity  = parseInt(product_qty[product.id]);

                if ( product.type == 'bundle' ) {
                    total_quantities    = thisthis.getBundleProductQty(product, 'total');
                    bundle_quantities   = thisthis.getBundleProductQty(product, 'ordered');
                }
                
                if ( Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length > 0 ) {
                    $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, cart_product) {

                        if ( product.type != 'bundle' ) {
                            if ( cart_product.type == 'simple' || cart_product.type == 'virtual' || cart_product.type == 'configurable' || cart_product.type == 'booking' ) {
                                if ( cart_product.id == product.id ) {
                                    if ( cart_product.type == 'booking' ) {
                                        var getStatus = thisthis.compareBookingOptions(product.additional, cart_product.additional);
                                        if ( getStatus == true ) {
                                            remaining_quantity = remaining_quantity - parseInt(cart_product.qty_ordered);    
                                        }
                                    } else {
                                        remaining_quantity = remaining_quantity - parseInt(cart_product.qty_ordered);
                                    }
                                }
                            } else if (cart_product.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(cart_product, 'ordered');
                                
                                if ( quantities[product.id] ) {
                                    remaining_quantity = remaining_quantity - (parseInt(quantities[product.id]) * parseInt(cart_product.qty_ordered));
                                }
                            }   
                        } else {
                            if ( cart_product.type == 'simple' || cart_product.type == 'virtual' || cart_product.type == 'configurable' ) {
                                if ( bundle_quantities[cart_product.id] ) {
                                    bundle_quantities[cart_product.id] = parseInt(bundle_quantities[cart_product.id]) + parseInt(cart_product.qty_ordered);
                                }
                            } else if (cart_product.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(cart_product, 'ordered');

                                for (const product_id in bundle_quantities) {
                                    if ( bundle_quantities[product_id] && quantities[product_id] ) {
                                        bundle_quantities[product_id] = parseInt(bundle_quantities[product_id]) + (parseInt(quantities[product_id]) * parseInt(cart_product.qty_ordered));
                                    }
                                }
                            }
                        }
                    });
                }

                if (Object.keys(thisthis.localObject.pos_offline_orders).length > 0 && parseInt(remaining_quantity) > 0 ) {
                    $.each(thisthis.localObject.pos_offline_orders, function(key, offlineOrder) {
                        
                        if ( product.type != 'bundle' ) {
                            if ( offlineOrder.type == 'simple' || offlineOrder.type == 'virtual' || offlineOrder.type == 'configurable' || offlineOrder.type == 'booking' ) {
                                if ( offlineOrder.id == product.id ) {
                                    if ( offlineOrder.type == 'booking' ) {
                                        var getStatus = thisthis.compareBookingOptions(product.additional, offlineOrder.additional);
                                        if ( getStatus == true ) {
                                            remaining_quantity = remaining_quantity - parseInt(offlineOrder.qty_ordered);    
                                        }
                                    } else {
                                        remaining_quantity = remaining_quantity - parseInt(offlineOrder.qty_ordered);
                                    }
                                }
                            } else if (offlineOrder.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(offlineOrder, 'ordered');
                                
                                if ( quantities[product.id] ) {
                                    remaining_quantity = remaining_quantity - (parseInt(quantities[product.id]) * parseInt(offlineOrder.qty_ordered));
                                }
                            }   
                        } else {
                            if ( offlineOrder.type == 'simple' || offlineOrder.type == 'virtual' || offlineOrder.type == 'configurable' ) {
                                if ( bundle_quantities[offlineOrder.id] ) {
                                    bundle_quantities[offlineOrder.id] = parseInt(bundle_quantities[offlineOrder.id]) + parseInt(offlineOrder.qty_ordered);
                                }
                            } else if (offlineOrder.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(offlineOrder, 'ordered');

                                for (const product_id in bundle_quantities) {
                                    if ( bundle_quantities[product_id] && quantities[product_id] ) {
                                        bundle_quantities[product_id] = parseInt(bundle_quantities[product_id]) + (parseInt(quantities[product_id]) * parseInt(offlineOrder.qty_ordered));
                                    }
                                }
                            }
                        }
                    });
                }

                if ( product.type == 'bundle' ) {
                    for (const product_id in total_quantities) {
                        if ( parseInt(total_quantities[product_id]) > 0 ) {
                            if ( parseInt(bundle_quantities[product_id]) <= 0 || parseInt(bundle_quantities[product_id]) > parseInt(total_quantities[product_id]) ) {
                                return remaining_quantity = 0;
                            }
                        } else {
                            return remaining_quantity = 0;
                        }
                    }
                }
                
                return remaining_quantity;
            },

            getBundleProductQty(cart_product, action_type) {
                var quantities = [];
                var additional = cart_product.additional;
                for (const optionId in additional.bundle_options ) {
                    if ( additional.bundle_options[optionId] ) {
                        
                        const optionProductIds = additional.bundle_options[optionId];
                        for (const key in optionProductIds) {
                            const optionProductId   = optionProductIds[key];
                            var attribte_option     = additional.attributes[optionId];
                            var cart_qty            = additional.bundle_option_qty[optionId];
                            var option_detail       = attribte_option.option_detail;
                            var option_products     = option_detail.option_products;
                            var option_product      = option_products[optionProductId];
                            var total_qty           = option_product.total_qty[option_product.product_id];

                            if ( option_detail.type == 'checkbox' || option_detail.type == 'multiselect' ) {
                                cart_qty = option_product.qty;
                            }
                            if ( option_product.product_id && quantities[option_product.product_id] ) {
                                if (action_type == 'total') {
                                    quantities[option_product.product_id] = parseInt(total_qty);
                                } else {
                                    quantities[option_product.product_id] = parseInt(quantities[option_product.product_id]) + parseInt(cart_qty);
                                }
                            } else {
                                if ( option_product.product_id ) {
                                    if (action_type == 'total') {
                                        quantities[option_product.product_id] = parseInt(total_qty);
                                    } else {
                                        quantities[option_product.product_id] = parseInt(cart_qty);
                                    }
                                }
                            }
                        }
                    }
                }

                return quantities;
            },

            compareBookingOptions(option1, option2) {
                var result = true;

                if (! option1 || !option2 ) {
                    return result = false;
                }
                if ( option1.product_id != option2.product_id ) {
                    return result = false;
                }
                if (Object.keys(option1.booking).length == Object.keys(option2.booking).length) {
                    // rental booking check
                    if ( option1.booking.renting_type ) {
                        if ( option1.booking.renting_type === option2.booking.renting_type ) {
                            if ( option1.booking.renting_type == 'daily' ) {
                                if ( (option1.booking.date_from !== option2.booking.date_from) || (option1.booking.date_to !== option2.booking.date_to)) {
                                    result = false;
                                }
                            } else {
                                if ( option1.booking.date === option2.booking.date ) {
                                    if ( (option1.booking.slot.from !== option2.booking.slot.from) || (option1.booking.slot.to !== option2.booking.slot.to) ) {
                                        result = false;
                                    }
                                } else {
                                    result = false;
                                }
                            }
                        } else {
                            result = false;
                        }
                    } else {                            
                        if ( option1.booking.slot ) {
                            // default & appointment booking check
                            if ( option1.booking.slot !== option2.booking.slot) {
                                result = false;
                            } else {
                                // table booking check
                                if ( option1.booking.date && (option1.booking.date !== option2.booking.date)) {
                                    result = false;
                                }
                            }
                        }
                        // event booking check
                        if ( option1.booking.ticket_id ) {
                            if ( option1.booking.ticket_id !== option2.booking.ticket_id) {
                                result = false;
                            }
                        }
                    }
                } else {
                    result = false;
                }

                return result;
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
                if ( self.localObject.pos_cart_customer.email ) {
                    customer_group_id = self.localObject.pos_cart_customer.customer_group_id;
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
        },

    }
</script>
