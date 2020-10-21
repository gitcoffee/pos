<template>
<div>
    <div class="cashier-close-panel pos-col-12" v-if="isUserLogin">
        <div class="message-alert warning" v-if="pos_offline">
            {{ $t('pos_view.error_offline_mode') }}
        </div>
        <form autocomplete="off" @submit.prevent="closeDrawer" method="POST">
            <div class="pos-col-4">
                <div class="container-panel-header">
                    <h3> {{ $t('pos_cashier.menu_content.close_counter.text_drawer_amount') }} </h3>
                </div>
                <div class="cashier_content">
                    <div class="detail_section">
                        <label>{{ $t('pos_cashier.menu_content.close_counter.text_open_amount') }}</label>
                        <label>{{ current_symbol }}{{ formatPrice(opening_amount) }}</label>
                    </div>

                    <div class="detail_section">
                        <label>{{ $t('pos_cashier.menu_content.close_counter.text_total_cash') }}</label>
                        <label class="cash_total">
                            {{ selected_currency }}{{ today_cash_total }}
                        </label>
                    </div>

                    <div class="detail_section">
                        <label>{{ $t('pos_cashier.menu_content.close_counter.text_current_drawer_sale') }}</label>
                        <label class="cash_total">
                            {{ selected_currency }}{{ f_current_drawer_sale }}
                        </label>
                    </div>

                    <div class="detail_section">
                        <label>{{ $t('pos_cashier.menu_content.close_counter.text_expected_amount') }}</label>
                        <label class="main_total">
                            {{ selected_currency }}{{ expected_amount }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="pos-col-4">
                <div class="container-panel-header">
                    <h3> {{ $t('pos_cashier.menu_content.close_counter.text_counted_amount') }} </h3>
                </div>
                <div class="cashier_content">
                    <div class="detail_section control-group">
                        <label>
                            {{ $t('pos_cashier.menu_content.close_counter.text_amount', { currency: selected_currency }) }}
                        </label>

                        <input type="text" class="control" id="total_amount" name="total_amount" v-model="amount" />
                    </div>
                    <div class="detail_section control-group" :class="[this.errors.has('remark') ? 'has-error' : '']">
                        <label for="remark" class="required">{{ $t('pos_cashier.menu_content.close_counter.text_remark') }}</label>
                        <textarea class="control" v-validate="'required|max:250'" name="remark" id="remark" v-model="remark" :placeholder="$t('pos_cashier.menu_content.close_counter.text_remark_placeholder')"></textarea>
                        <span class="control-error" v-if="this.errors.has('remark')">{{ this.errors.first('remark') }}</span>
                    </div>
                </div>
            </div>
            <div class="pos-col-4">
                <div class="container-panel-header">
                    <h3> {{ $t('pos_cashier.menu_content.close_counter.text_closing_details') }} </h3>
                </div>
                <div class="cashier_content">
                    <div class="detail_section">
                        <label>{{ $t('pos_cashier.menu_content.close_counter.text_total_drawer_amount') }}</label>
                    </div>
                    <div class="detail_section">
                        <label class="main_total">
                            {{ selected_currency }}{{ expected_amount }}
                        </label>
                    </div>
                </div>

                <div class="pos-action text-left">
                    <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_cashier.menu_content.close_counter.button_close_drawer')"> {{ $t('pos_cashier.menu_content.close_counter.button_close_drawer') }} </button>
                </div>
            </div>
        </form>
    </div>
</div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                user_id: 0,
                drawer_id: 0,
                opening_amount: 0,
                today_cash_total: 0,
                expected_amount: 0,
                amount: 0,
                front_currency_code: window.pos_currency_code,
                selected_currency: window.pos_currency_symbol,
                current_symbol: '',
                order_currency_code: '',

                current_drawer_sale: 0,
                f_current_drawer_sale: 0,

                remark: '',
                difference_status: false,
                difference_amount: 0,
                pos_offline: 0,
                error_offline_action: this.$t('pos_view.error_offline_action'),
                response_status: false,
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();
                this.pos_offline = this.$root.$root.offline;
                return this.user_id;
            }
        },
        mounted() {
            this.getDrawerDetails();
            this.getTotalSale();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            formatPrice(price) {
                return parseFloat(price).toFixed(2);
            },
            getDrawerDetails() {

                this.pos_offline = this.$root.$root.offline;
                if (!this.pos_offline) {
                    this.$http.post('/api/pos/auth/getDrawerDetails', {
                        user_id: this.user_id
                    })
                    .then((response)  =>  {
                        if (response.data.status && response.data.drawer_data.id) {
                            this.localObject.pos_drawer = response.data.drawer_data;

                            this.current_symbol = this.localObject.pos_drawer.base_symbol;
                            this.opening_amount = this.localObject.pos_drawer.opening_amount;

                            this.getCurrentDrawer();

                            EventBus.$emit('setLocalForage', {'key': 'pos_drawer', 'data': JSON.stringify(response.data.drawer_data)});
                        }
                    })
                    .catch(function (error) {});
                }
            },
            getCurrentDrawer() {
                this.$http.get('/api/pos/auth/getOrders', {
                    params: {
                        user_id: this.user_id,
                        filter_date: true,
                        filter_drawer_id: this.localObject.pos_drawer.id,
                        filter_amount: 'cash',
                    }
                })
                .then((response)  =>  {
                    if (response.data.data && response.data.data.length == 1) {

                        if ( response.data.data[0].id ) {

                            this.selected_currency = response.data.data[0].base_currency_symbol;

                            this.f_current_drawer_sale = this.formatPrice(response.data.data[0].base_total_amount);
                            this.current_drawer_sale = response.data.data[0].base_total_amount;
                            
                            this.expected_amount = this.amount = this.formatPrice(parseFloat(this.localObject.pos_drawer.opening_amount) + parseFloat(response.data.data[0].base_total_amount));

                            if (parseFloat(this.localObject.pos_drawer.opening_amount) > parseFloat(this.current_drawer_sale)) {
                                this.difference_status = true;
                                this.difference_amount = this.formatPrice(parseFloat(this.localObject.pos_drawer.opening_amount) - parseFloat(this.current_drawer_sale));
                            }
                        }
                    } else {
                        this.current_drawer_sale = 0;
                        this.f_current_drawer_sale = 0;
                        this.expected_amount = this.amount = this.formatPrice(parseFloat(this.localObject.pos_drawer.opening_amount));
                        this.difference_status = true;
                        this.difference_amount = this.formatPrice(parseFloat(this.localObject.pos_drawer.opening_amount));
                        this.selected_currency = window.base_currency_symbol;
                    }
                })
                .catch(function (error) {});
            },
            getTotalSale() {
                this.pos_offline = this.$root.$root.offline;
                if (!this.pos_offline) {

                    this.$http.get('/api/pos/auth/getOrders', {
                        params: {
                            user_id: this.user_id,
                            filter_date: true,
                            filter_amount: 'cash',
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length == 1) {

                            if ( response.data.data[0].id ) {
                                
                                this.selected_currency = response.data.data[0].base_currency_symbol;

                                this.today_cash_total = this.formatPrice(response.data.data[0].base_total_amount);
                            }
                        } else {
                            this.selected_currency = window.base_currency_symbol;
                            this.today_cash_total = 0;
                        }
                    })
                    .catch(function (error) {});
                }
            },
            closeDrawer () {
                var thisthis = this;
                thisthis.pos_offline = this.$root.$root.offline;
                if (thisthis.pos_offline) {
                    
                    this.$toast.warning(this.error_offline_action, {
                        position: 'top-right',
                        duration: 2000,
                    });

                } else {
                    this.$root.toggleButtonDisable(true);                
                    this.$validator.validateAll().then(result => {
                        if (result) {
                            this.$http.post('/api/pos/auth/updateDrawer', {
                                user_id: thisthis.user_id,
                                amount: thisthis.amount,
                                currency_code: thisthis.front_currency_code,
                                remark: thisthis.remark,
                                update_status: thisthis.current_drawer_sale ? true : false,
                                status: 0,
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

                                    this.opening_amount = this.amount;
                                    this.remark = '';
                                    this.current_drawer_sale = 0;
                                    this.f_current_drawer_sale = 0;
                                    this.difference_amount = 0;
                                    this.difference_status = false;
                                    this.response_status = true;

                                    this.$router.push({name: 'pos_cashier_closecounter'});
                                }
                            })
                            .finally(() => {
                                
                                if (this.response_status) {
                                    setTimeout(() => {
                                        this.errors.remove('remark');        
                                    });
                                }
                            })
                            .catch(function (error) {});
                        } else {
                            this.$root.toggleButtonDisable(false);
                        }
                    });
                }
            },
        }
    }
</script>