<template>
    <div class="pos-booking-form" v-if="isUserLogin">

        <form autocomplete="off" @submit.prevent="bookingAction" method="POST">
            <div class="page-content">
                <div class="form-container">
                    <input type="hidden" name="table_id" v-model="table_id">

                    <div class="pos-booking-fields">
                        <div class="control-group" :class="[this.errors.has('customer') ? 'has-error' : '']" v-if="!pos_offline">
                            <label for="customer" v-text="$t('pos_restaurant.menu_content.table_list.select_customer')"></label>
                            
                            <input type="text" class="control" name="customer" v-model="customer" v-bind:style="{ 'width': width }" :placeholder="$t('pos_restaurant.menu_content.table_list.placeholder_customer')" @keyup="searchCustomer">

                            <div class="linked-product-search-result">
                                <ul v-if='customers_list.length'>
                                    <li v-for='(customer, index) in customers_list' @click="selectCustomer(customer)">
                                        <span class="customer-name">
                                            {{ customer.name }} - ({{ customer.email }})
                                        </span>
                                        
                                        <span class="customer-phone" v-if="customer.phone">
                                            <i class="fa fa-mobile"></i>
                                            {{ customer.phone }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                            <span class="control-error" v-if="this.errors.has('customer')">{{ this.errors.first('customer') }}</span>
                        </div>
                        
                        <div class="sperator" v-if="!pos_offline">- OR -</div>
                        
                        <div class="control-group" :class="[this.errors.has('name') ? 'has-error' : '']">
                            <label for="name" class="required" v-text="$t('pos_restaurant.menu_content.table_list.name')"></label>
                            <input type="text" class="control" name="name" v-validate="'required|max:50'" v-model="customer_name" v-bind:style="{ 'width': width }" :placeholder="$t('pos_restaurant.menu_content.table_list.placeholder_booking_name')" />

                            <span class="control-error" v-if="this.errors.has('name')">{{ this.errors.first('name') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('email') ? 'has-error' : '']">
                            <label for="email" v-text="$t('pos_restaurant.menu_content.table_list.email')"></label>
                            <input type="text" class="control" name="email" v-validate="'email'" v-model="customer_email" v-bind:style="{ 'width': width }" :placeholder="$t('pos_restaurant.menu_content.table_list.placeholder_booking_email')">
                            <span class="control-error" v-if="this.errors.has('email')">{{ this.errors.first('email') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('phone') ? 'has-error' : '']">
                            <label for="phone" v-text="$t('pos_restaurant.menu_content.table_list.phone')"></label>
                            <input type="text" class="control" name="phone" v-validate="'numeric|max:15'" v-model="customer_phone"  v-bind:style="{ 'width': width }" :placeholder="$t('pos_restaurant.menu_content.table_list.placeholder_phone')">
                            <span class="control-error" v-if="this.errors.has('phone')">{{ this.errors.first('phone') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('seat') ? 'has-error' : '']">
                            <label for="seat" class="required" v-text="$t('pos_restaurant.menu_content.table_list.seat')"></label>
                            
                            <div class="pos-input-group">
                                <span class="pos-input-group-btn">
                                    <div class="span-btn span-btn-sm"v-text="$t('pos_restaurant.menu_content.table_list.total_available_seat', { available_seat: table_data.no_of_seat})"></div>
                                </span>

                                <input type="text" class="control" name="seat" v-validate="'required|numeric|min_value:1|max_value:'+table_data.no_of_seat+'|max:3'" v-model="booked_seat"  v-bind:style="{ 'width': width }" :placeholder="$t('pos_restaurant.menu_content.table_list.seat')" @keyup="validateSeat">
                            </div>
                            
                            <span class="control-error" v-if="this.errors.has('seat')">{{ this.errors.first('seat') }}</span>
                        </div>

                        <div class="control-group date" :class="[this.errors.has('booking_date') ? 'has-error' : '']">
                            <label for="booking_date" class="required" v-text="$t('pos_restaurant.menu_content.table_list.booking_date')"></label>
                            <calender-date>
                                <input type="text" class="control" name="booking_date" v-validate="'required'" v-model="booking_date" v-bind:style="{ 'width': '50%' }">
                            </calender-date>
                        </div>

                        <div class="pos-group-fields">
                            <div class="pull-left">
                                <div class="control-group date" :class="[this.errors.has('booking_time_from') ? 'has-error' : '']">
                                    <label for="booking_time_from" class="required" v-text="$t('pos_restaurant.menu_content.table_list.booking_time_from')"></label>
                                    <calender-time>
                                        <input type="text" class="control" name="booking_time_from" v-validate="'required'" v-model="booking_time_from">
                                    </calender-time>
                                </div>
                            </div>

                            <div class="pull-right">
                                <div class="control-group date" :class="[this.errors.has('booking_time_to') ? 'has-error' : '']">
                                    <label for="booking_time_to" class="required" v-text="$t('pos_restaurant.menu_content.table_list.booking_time_to')"></label>
                                    <calender-time>
                                        <input type="text" class="control" name="booking_time_to" v-validate="'required'" v-model="booking_time_to">
                                    </calender-time>
                                </div>
                            </div>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_restaurant.menu_content.table_list.button_confirm_booking')"> {{ $t('pos_restaurant.menu_content.table_list.button_confirm_booking') }} </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<style lang="scss">
    .pos-booking-fields {
        .sperator {
            width: 100%;
            text-align: center;
            margin: 5px 0px 7px 0px;
            display: inline-block;
        }
    }

    .linked-product-search-result {
        box-shadow: 0 2px 4px 0 rgba(0,0,0,.16), 0 0 9px 0 rgba(0,0,0,.16);
        z-index: 10;
        text-align: left;
        border-radius: 3px;
        background-color: #fff;
        width: 70%;
        max-height: 200px;
        overflow-y: auto;
        position: absolute;

        li {
            padding: 10px;
            border-bottom: 1px solid #e8e8e8;
            cursor: pointer;
            display: inline-grid;
        }
    }
</style>


<script>
    export default {
        props: ['table_id', 'table_data', 'localObject'],
        data() {
            return {
                width: '90%',
                user_id: 0,
                customer: '',
                customers_list: [],
                customer_id: 0,
                customer_name: '',
                customer_email: '',
                customer_phone: '',
                booked_seat: 0,
                booking_date: null,
                booking_time_from: null,
                booking_time_to: null,
                pos_offline: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();
                return this.user_id;
            }
        },
        mounted() {
        },
        methods: {
            checkUserLogin() {
                this.pos_offline = this.$root.$root.offline;

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            bookingAction () {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    var self = this;
                    var current_date_time = new Date();

                    this.$root.toggleButtonDisable(true);
                    
                    this.$validator.validateAll().then(result => {
                        if (result) {
                            
                            if ( this.validateTime() ) {
                                this.$http.post('/api/pos/auth/addBooking', {
                                        booking_id: Math.floor(current_date_time.getTime()/10000),
                                        table_id: self.table_id,
                                        customer_id: self.customer_id,
                                        customer_name: self.customer_name,
                                        customer_email: self.customer_email,
                                        customer_phone: self.customer_phone,
                                        booked_seat: self.booked_seat,
                                        booked_date: self.booking_date,
                                        booked_time_from: self.booking_time_from,
                                        booked_time_to: self.booking_time_to,
                                        agent_id: self.user_id
                                })
                                .then((response)  =>  {
                                    if (!response.data.status) {
                                        this.$toast.error(response.data.message, {
                                            position: 'top-right',
                                            duration: 3000,
                                        });
                                        this.$root.toggleButtonDisable(false);
                                    } else {
                                        this.$toast.success(response.data.message, {
                                            position: 'top-right',
                                            duration: 2000,
                                        });
                                        this.$root.hideCommonModal('addBooking');
                                        this.$root.toggleButtonDisable(false);
                                        
                                        this.$router.push({name: response.data.route});
                                    }
                                })
                                .catch(function (error) {});
                            } else {
                                this.$root.toggleButtonDisable(false);
                            }
                        } else {
                            this.$root.toggleButtonDisable(false);
                        }
                    });
                }
            },
            validateSeat() {
                if (this.table_id) {
                    if (this.booked_seat > this.table_data.no_of_seat) {
                        this.$toast.error(this.$t('pos_restaurant.menu_content.table_list.warning_booked_seat'), {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                }
            },

            validateTime(fieldType, time) {
                var current_date_time = new Date();

                if (! this.booking_date ) {
                    this.$toast.error(this.$t('pos_restaurant.menu_content.table_list.warning_select_date'), {
                        position: 'top-right',
                        duration: 2000,
                    });

                    return false;
                } else {
                    var twoDigitMonth = ((current_date_time.getMonth().length + 1) === 1) ? (current_date_time.getMonth()+1) : '0' + (current_date_time.getMonth() + 1);

                    var get_date = current_date_time.getFullYear() + '-' + twoDigitMonth + '-' + current_date_time.getDate();

                    var current_date = new Date(get_date);
                    var booking_date = new Date(this.booking_date);

                    var current_time = current_date_time.getTime();
                    var booking_time_from = new Date(get_date + ' ' + this.booking_time_from);
                    var from_time = booking_time_from.getTime();

                    if ((current_date.getTime() == booking_date.getTime()) && (current_time >= from_time)) {
                        this.$toast.error(this.$t('pos_restaurant.menu_content.table_list.warning_select_from_time'), {
                            position: 'top-right',
                            duration: 2000,
                        });

                        return false;
                    } else {
                        var booking_time_to = new Date(get_date + ' ' + this.booking_time_to);
                        var to_time = booking_time_to.getTime();

                        if ( from_time >= to_time ) {
                            this.$toast.error(this.$t('pos_restaurant.menu_content.table_list.warning_time_range'), {
                                position: 'top-right',
                                duration: 2000,
                            });

                            return false;
                        }
                    }
                }

                return true;
            },

            searchCustomer() {
                var self = this;

                if (! this.$root.offline ) {
                    this.$http.get('/api/pos/getCustomers', {
                        params: {
                            user_id: self.user_id,
                            customer_name: self.customer,
                            limit: 3,
                        }
                    })
                    .then((response)  =>  {
                        self.customers_list = [];
                        if (response.data.data && response.data.data.length > 0) {
                            self.customers_list = response.data.data;
                        } else {
                            self.customers_list = [];
                        }
                    })
                    .catch(function (error) {});
                }
            },

            selectCustomer(customer) {
                if ( customer.id ) {
                    this.customer_id = customer.id;
                    this.customer_name = customer.name;
                    this.customer_email = customer.email;
                    this.customer_phone = customer.phone;
                    this.customer = '';
                    this.customers_list = [];
                }
            }
        }
    }
</script>