<template>
    <div v-bind:style="{'display': 'inline-flex', 'width': '100%'}">
        <div class="table-booking-panel" v-bind:style="{'height': cart_container_height}" v-if="isUserLogin">
            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div> 

            <div class="table-list-header">
                <div class="heading">
                    <h3>{{ $t('pos_restaurant.menu_content.table_booked.heading') }}</h3>
                </div>
                <div class="control-group search-bar">
                    <input type="text" name="search_bar" class="control search-table" id="table-search" v-model="filter_name" :placeholder="$t('pos_restaurant.menu_content.table_booked.search_booking_placeholder')" @keyup="searchPosBooking" />
                </div>
            </div>

            <div class="table-row row-grid-4" v-if="pos_total_booking">
                <div class="table-record-container row-layout" v-for="(booking, index) in pos_restaurant_bookings" :key="index">
                    <div class="table-booking-header" @click="viewBooking(booking)">
                        <div class="table-name" >
                        {{ $t('pos_restaurant.menu_content.table_booked.booking_id') }}
                            <div>#{{ booking.booking_id }}</div>
                        </div>

                        <div class="table-name">
                            {{ $t('pos_restaurant.menu_content.table_booked.booking_date') }}
                            <div>{{ booking.booked_date }}</div>
                        </div>

                        <div class="table-name" >
                            {{ $t('pos_restaurant.menu_content.table_booked.time_slot') }}
                            <div>{{ booking.booked_time_from }} - {{ booking.booked_time_to }}</div>
                        </div>
                    </div>

                    <div class="table-type">
                        {{ $t('pos_restaurant.menu_content.table_booked.table_information') }}
                        <div class="table-details">
                            {{ $t('pos_restaurant.menu_content.table_booked.table_name') }}
                            <div>{{ booking.table_name }}</div>
                        </div>
                        <div class="table-details">
                            {{ $t('pos_restaurant.menu_content.table_booked.table_type') }}
                            <div>{{ booking.table_type }}</div>
                        </div>
                        <div class="table-details">
                            {{ $t('pos_restaurant.menu_content.table_booked.booked_seat') }}
                            <div>{{ booking.booked_seat }}</div>
                        </div>
                    </div>

                    <div class="table-type">
                        {{ $t('pos_restaurant.menu_content.table_booked.customer_information') }}
                        <div class="table-details">
                            {{ $t('pos_restaurant.menu_content.table_booked.customer_name') }}
                            <div>{{ booking.customer_name }}</div>
                        </div>
                        <div class="table-details">
                            {{ $t('pos_restaurant.menu_content.table_booked.customer_email') }}
                            <div>{{ booking.customer_email }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="message-alert danger">
                {{ $t("pos_restaurant.error.no_booking") }}
            </div>
        </div>

        <div class="booked-cart-container" v-bind:style="{'height': cart_container_height}" v-if="isUserLogin">
            <div class="booked-cart-header">
                <div class="booked-table-details" v-if="this.booked_id">
                    {{ $t('pos_restaurant.menu_content.table_booked.booked_details', {booking_id: booked_data.booking_id}) }}
                </div>
                <div class="booked-table-details" v-else >
                    {{ $t('pos_restaurant.menu_content.table_booked.booked_info') }}    
                </div>
            </div>

            <div class="booked-cart-content" v-bind:style="{'height': container_height}">
                <div class="booked-table-info" v-if="this.booked_id">
                    <div class="name">
                        {{ $t('pos_restaurant.menu_content.table_booked.booked_table_name') }}
                        <span>{{ booked_data.table_name }}</span>
                    </div>

                    <div class="type">
                        {{ $t('pos_restaurant.menu_content.table_booked.booked_table_type') }}
                        <div class="shape">
                            <div v-if="booked_data.table_type === 'circle'" :class="[ (booked_data.table_type === 'circle') ? 'circle' : '']">
                                {{ booked_data.table_type|capitalize }}
                            </div>
                            <div v-if="booked_data.table_type === 'square'" :class="[ (booked_data.table_type === 'square') ? 'square' : '']">
                                {{ booked_data.table_type|capitalize }}
                            </div>
                            <div v-if="booked_data.table_type === 'curved-square'" :class="[(booked_data.table_type === 'curved-square') ? 'curved-square' : '']">
                                {{ booked_data.table_type|capitalize }}
                            </div>
                        </div>
                    </div>

                    <div class="table-cart-wrapper" v-bind:style="{'height': wrapper_height}">
                        <div class="order-details">
                            {{ $t('pos_restaurant.menu_content.table_booked.booking_order_details') }}
                        </div>

                        <div class="order-item-container" v-bind:style="{'height': item_container_height}">
                            <ul class="cart_details">
                                <div v-if="cartEmpty" class="message-alert">
                                    <span class="text-danger">{{ $t('pos_home.pos_cart.text_cart_empty') }}</span>
                                </div>
                                <div v-else >
                                    <li v-for="cart_detail in cart_items">
                                        <div class="cart-product-content" @click="makeActiveProduct(cart_detail)">
                                            <div class="product-name">
                                                {{ cart_detail.name }}

                                                <span class="product-attributes" v-if="cart_detail.type == 'configurable'" >
                                                    <span v-for="attributes in cart_detail.additional.attributes">
                                                        <i> <b>{{ attributes.attribute_name }}</b>: {{ attributes.option_label }}, </i>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="product-qty">
                                                <span>
                                                    {{ cart_detail.qty_ordered }} {{ $t("pos_home.pos_cart.text_unit") }}

                                                    - {{ cart_detail.f_price }} {{ $t("pos_home.pos_cart.text_per_unit") }}
                                                </span>                                    
                                            </div>
                                        </div>
                                        <div class="cart-product-price">
                                            <span>{{ cart_detail.f_total }}</span>
                                        </div>
                                    </li>
                                </div>
                            </ul>
                        </div>

                        <div class="order-btn-container">
                            <div class="pos-table-responsive pos-table-total">
                                <table class="pos-table">
                                    <tbody>
                                        <tr>
                                            <td class="text-left">{{ $t("pos_home.pos_cart.text_grand_total") }}</td>
                                            <td class="text-right">{{ currency }} {{ grand_total }}</td>
                                        </tr>
                                    </tbody>
                                </table>                            
                            </div>
                            <div class="product-btn-group">
                                <button type="button" class="btn btn-lg btn-pos-primary pos-add-product" @click="addProduct(booked_data)">
                                    <i class="fa fa-shopping-basket"></i>
                                    {{ $t('pos_restaurant.menu_content.table_booked.add_product') }}
                                </button>
                                <button type="button" class="btn btn-lg btn-pos-primary pos-table-release" @click="releaseTable(booked_data)">
                                    <i class="fa fa-times-circle"></i>
                                    {{ $t('pos_restaurant.menu_content.table_booked.table_release') }}
                                </button>
                            </div>
                            
                            <button type="button" class="btn btn-lg btn-pos-dark pos-table-checkout" @click="tableCheckout(booked_data)">
                                <i class="fa fa-shopping-cart"></i>
                                {{ $t('pos_restaurant.menu_content.table_booked.checkout') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="message-alert danger" v-if="!this.booked_id">
                    {{ $t('pos_restaurant.menu_content.table_booked.no_booking_selected') }}
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .pos-restaurant-main {
        .pos-nav-container {
            .pos-nav-lists {
                width: 70%;
            }
        }
    }
    .pos-container-wrapper .pos-content-container .pos-restaurant-main .pos-nav-content {
        padding: 0px;
        margin-top: 1px;
    }
</style>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                user_id: 0,
                currency: '',
                sub_total: 0,
                cart_discount: 0,
                pos_discounts: [],
                tax_total: 0,
                grand_total: 0,
                pos_offline: 0,
                filter_name: '',
                pos_total_booking: 0,
                page: 1,
                limit: 10,
                totalPage: 0,
                bookingRequests: [],
                booked_id: 0,
                booked_data: {},
                pos_restaurant_bookings: [],
                cart_container_height: ($(window).height() - 115) + 'px',
                container_height: ($(window).height() - 180) + 'px',
                wrapper_height: ($(window).height() - 303) + 'px',
                item_container_height: ($(window).height() - 476) + 'px',
                cart_items: [],
                cartEmpty: true,
            };
        },

        computed: {
            isUserLogin () {
                this.checkUserLogin();
                return this.user_id;
            }
        },

        filters: {
            capitalize: function (value) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        },

        created() {
            this.getBookingTables();
        },

        mounted() {
            this.currency = window.pos_currency_symbol;
        },

        methods: {
            checkUserLogin() {
                this.pos_offline = this.$root.$root.offline;

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },

            viewBooking(booked) {
                var self = this;
                    self.cartEmpty = true;
                    self.booked_id = booked.booking_id;
                    self.booked_data = booked;
                    self.cart_items = [];
                    self.sub_total = 0;
                    self.cart_discount = 0;
                    self.tax_total = 0;
                    self.grand_total = 0;

                    if (Object.keys(self.localObject.pos_carts).length == 1 && Object.keys(self.localObject.pos_carts[self.localObject.pos_current_cart]).length == 0) {
                        this.localObject.pos_current_booking = {};
                        EventBus.$emit('setLocalForage', {'key': 'pos_current_booking', 'data': JSON.stringify(this.localObject.pos_current_booking)});
                    }

                    $.each(self.localObject.pos_carts, (cardIndex, cartProducts) => {
                        var resultObject = this.searchCardBooking(booked.booking_id, cartProducts);
                            if (typeof  resultObject !== 'undefined') {
                                self.cart_items = self.localObject.pos_carts[cardIndex];
                                self.cartEmpty = false;

                                self.updateCartTotal();
                            }
                    });
            },

            searchCardBooking(bookedId, cartProducts) {
                for (var i=0; i < Object.keys(cartProducts).length; i++) {
                    if (cartProducts[i].booking_id && cartProducts[i].booking_id === bookedId) {
                        return cartProducts[i];
                    }
                }
            },

            searchPosBooking(event) {
                if (((event.keyCode > 48 && event.keyCode < 90) || (event.keyCode > 95 && event.keyCode < 105) ) || event.keyCode == 189 || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 46) {
                    this.page = 1;
                    this.pos_total_booking = 0;
                    this.pos_restaurant_bookings = [];
                    this.getBookingTables();
                }
            },

            getBookingTables() {
                var self = this;
                self.pos_offline = self.$root.$root.offline;
                
                if (self.localObject.pos_cashier.id) {
                    if (self.pos_offline) {
                        if (Object.keys(this.localObject.pos_restaurant_bookings).length > 0) {
                            this.pos_total_booking = Object.keys(this.localObject.pos_restaurant_bookings).length;
                            
                            this.pos_restaurant_bookings = this.localObject.pos_restaurant_bookings;
                        } else {
                            this.pos_total_booking = 0;
                            this.pos_restaurant_bookings = [];
                        }
                    } else {
                        self.$http.get('/api/pos/auth/getBookedTables', {
                            params: {
                                page:           self.page,
                                user_id:        self.localObject.pos_cashier.id,
                                filter_name:    self.filter_name,
                                booking_status:    1,
                                order_id:          null,
                            }
                        })
                        .then((response)  =>  {
                            if (response.data.data && response.data.data.length > 0) {
                                self.totalPage  = response.data.meta.last_page;
                                self.pos_total_booking = response.data.meta.total;
                                self.pos_restaurant_bookings = response.data.data;
    
                                for (self.page = 2; self.page <= self.totalPage; self.page++) {
                                    self.bookingRequests.push({
                                        url: '/api/pos/auth/getBookedTables',
                                        method: 'get',
                                        async:   true,
                                        params: {
                                            page:        self.page,
                                            user_id:     self.localObject.pos_cashier.id,
                                            filter_name: self.filter_name,
                                            booking_status:    1,
                                            order_id:          null,
                                        },
                                    });
                                }
                            } else {
                                self.totalPage = 0;
                                self.pos_total_booking = 0;
                                self.bookingRequests = {};
                            }
                        })
                        .catch(function (error) {})
                        .finally(() => self.NextRequest());
                    }
                }
            },

            NextRequest() {
                var self = this;
                if (self.bookingRequests.length) {
                    self.$root.$http(self.bookingRequests.shift())
                    .then(function(response) {
                        if (response.data.data && response.data.data.length > 0) {
                            self.pos_restaurant_bookings = self.pos_restaurant_bookings.concat(response.data.data);
                        }
                    })
                    .finally(() => self.NextRequest());
                } else {
                    self.localObject.pos_restaurant_bookings = self.pos_restaurant_bookings;
                    
                    EventBus.$emit('setLocalForage', {'key': 'pos_restaurant_bookings', 'data': JSON.stringify(self.pos_restaurant_bookings)});
                }
            },

            releaseTable(table) {
                var self = this;
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    this.$root.toggleButtonDisable(true);
                    this.$http.post('/api/pos/auth/removeBooking', {
                        booking_id: table.booking_id,
                        agent_id:   self.localObject.pos_cashier.id
                    })
                    .then((response)  =>  {
                        if (!response.data.status) {
                            this.$toast.error(response.data.message, {
                                position: 'top-right',
                                duration: 2000,
                            });
                            this.$root.toggleButtonDisable(false);
                        } else {
                            this.$toast.success(response.data.message, {
                                position: 'top-right',
                                duration: 2000,
                            });
                            
                            this.$root.toggleButtonDisable(false);
                            this.$router.push({name: response.data.route});
                        }
                    })
                    .catch(function (error) {});
                }
            },

            addProduct(booking_data) {
                var self = this;
                console.log(booking_data);
                if ( booking_data.customer_email ) {
                    self.getBookingCustomer(booking_data);
                }

                EventBus.$emit('addProductTable', booking_data);
                self.$router.push({name: 'pos_home'});
            },

            tableCheckout(table_data) {
                var self = this;
                    if ( Object.keys(this.cart_items).length  == 0) {
                        this.$toast.warning(this.$t('pos_restaurant.menu_content.table_booked.no_product_added'), {
                            position: 'top-right',
                            duration: 2000,
                        });
                    } else {
                        if ( table_data.customer_email ) {
                            self.getBookingCustomer(table_data);
                        }

                        EventBus.$emit('addProductTable', table_data);
                        self.$router.push({name: 'pos_home'});
                    }
            },

            getBookingCustomer(booking_data) {
                var self = this;

                if ( self.localObject.pos_customers && Object.keys(self.localObject.pos_customers).length > 0 ) {
                    $.each(self.localObject.pos_customers, (key, customer) => {
                        if (customer.email == booking_data.customer_email ) {
                            self.localObject.pos_cart_customer = customer;
                            EventBus.$emit('setLocalForage', {'key': 'pos_cart_customer', 'data': JSON.stringify(customer)});
                        }
                    });
                } else {
                    if (!this.$root.offline) {
                        this.$http.get('/api/pos/getCustomers', {
                            params: {
                                user_id: self.user_id,
                                customer_email: booking_data.customer_email,
                                pagination: 0
                            }
                        })
                        .then((response)  =>  {
                            if (response.data.data && response.data.data.length > 0) {
                                if ( response.data.data[0].email == booking_data.customer_email ) {
                                    self.localObject.pos_cart_customer = response.data.data[0];
                                    EventBus.$emit('setLocalForage', {'key': 'pos_cart_customer', 'data': JSON.stringify(response.data.data[0])});
                                }                                    
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },

            updateCartTotal() {
                var thisthis = this;
                var cart_sub_total = 0;
                var cart_tax_total = 0;
                var cart_grand_total = 0;

                thisthis.cartEmpty = true;

                $.each(thisthis.cart_items, function(key, product) {
                    thisthis.cartEmpty = false;
                    cart_sub_total = (cart_sub_total + parseFloat(product.total));

                    if (product.tax_category_id != null && product.tax_category_id && product.tax_percent) {
                        cart_tax_total = (cart_tax_total + ( (parseFloat(product.total) * product.tax_percent) / 100 ) ) ;
                    }
                });

                thisthis.sub_total = parseFloat(cart_sub_total).toFixed(2);
                thisthis.tax_total = parseFloat(cart_tax_total).toFixed(2);
                thisthis.sub_total_tax = (parseFloat(thisthis.sub_total) + parseFloat(thisthis.tax_total));

                cart_grand_total = thisthis.sub_total_tax;

                thisthis.grand_total = parseFloat(cart_grand_total).toFixed(2);
            },
        }
    }
</script>