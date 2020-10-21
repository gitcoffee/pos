<template>
    <div class="product-lowstock-panel">
        <div class="pos-product-container" v-if="isUserLogin">

            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div>

            <div class="pos-setting-list row-grid-5">
                <div class="pos-setting row-layout">
                    <div class="setting-list-name">
                        <div class="name">{{ $t('pos_cashier.menu_content.today_cash.total_cash_sale') }}</div>
                    </div>
                    <div class="setting-list-rate">
                        {{ selected_currency }}{{ total_cash_sale }}
                    </div>
                </div>
                <div class="pos-setting row-layout">
                    <div class="setting-list-name">
                        <div class="name">{{ $t('pos_cashier.menu_content.today_cash.total_card_sale') }}</div>
                    </div>
                    <div class="setting-list-rate">
                        {{ selected_currency }}{{ total_card_sale }}
                    </div>
                </div>
            </div>
            <div class="pos-table-responsive" style="margin-top:20px;">
                <table class="pos-table">
                    <thead>
                        <tr>
                            <th class="text-left">{{ $t('pos_cashier.menu_content.sales_history.order_id') }}</th>
                            <th class="text-left">{{ $t('pos_cashier.menu_content.sales_history.order_date') }}</th>
                            <th class="text-left">{{ $t('pos_cashier.menu_content.sales_history.order_total') }}</th>
                            <th class="text-left">{{ $t('pos_cashier.menu_content.sales_history.payment_mode') }}</th>
                        </tr>
                    </thead>
                    <tbody v-if="total_order != 0">
                        <tr v-for="(order, index) in today_sale_history" v-bind:key="index">
                            <td class="text-left">#{{ order.id }}</td>
                            <td class="text-left">{{ order.created_at }}</td>
                            <td class="text-left">
                                <span v-if="current_currency == order.order_currency_code">
                                    {{ order.order_currency_symbol }}{{ formatPrice(order.grand_total) }}
                                </span>
                                <span v-else>
                                    {{ order.base_currency_symbol }}{{ formatPrice(order.base_grand_total) }}
                                </span>
                            </td>
                            <td class="text-left"> {{ order.payment_mode }}</td>
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
    </div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                user_id: 0,
                drawer_id: 0,
                total_cash_sale: 0,
                total_card_sale: 0,
                selected_currency: '',
                total_order: 0,
                today_sale_history: [],
                current_currency: window.pos_currency_code,
                pos_offline: 0,
                orderRequests: [],
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
            this.getTotalSale();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                    this.drawer_id = this.localObject.pos_drawer.user_id;
                }
            },
            getTotalSale() {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {

                } else {
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
                                this.order_currency_code = response.data.data[0].order_currency_code;

                                if (window.pos_currency_code != this.order_currency_code) {
                                    this.selected_currency = response.data.data[0].order_currency_symbol;
                                    this.total_cash_sale = this.formatPrice(response.data.data[0].total_amount);
                                } else {
                                    this.selected_currency = response.data.data[0].base_currency_symbol;
                                    this.total_cash_sale = this.formatPrice(response.data.data[0].base_total_amount);
                                }
                            }
                        } else {
                            this.total_cash_sale = 0;
                            this.total_card_sale = 0;
                        }
                    })
                    .catch(function (error) {});
                    
                    this.$http.get('/api/pos/auth/getOrders', {
                        params: {
                            user_id: this.user_id,
                            filter_date: true,
                            filter_amount: 'card',
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length == 1) {
                            if ( response.data.data[0].id ) {
                                this.order_currency_code = response.data.data[0].order_currency_code;

                                if (window.pos_currency_code != this.order_currency_code) {
                                    this.selected_currency = response.data.data[0].order_currency_symbol;
                                    this.total_card_sale = this.formatPrice(response.data.data[0].total_amount);
                                } else {
                                    this.selected_currency = response.data.data[0].base_currency_symbol;
                                    this.total_card_sale = this.formatPrice(response.data.data[0].base_total_amount);
                                }
                            }
                        } else {
                            this.total_cash_sale = 0;
                            this.total_card_sale = 0;
                        }
                    })
                    .catch(function (error) {});

                    this.$http.get('/api/pos/auth/getOrders', {
                        params: {
                            user_id: this.user_id,
                            filter_date: true,
                            page: 1,
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length > 0) {
                            
                            this.today_sale_history = response.data.data;
                            this.total_order = response.data.data.length;

                            EventBus.$emit('setLocalForage', {'key': 'pos_today_sales', 'data': JSON.stringify(this.today_sale_history)});

                            let totalPages = response.data.meta.last_page;

                            for (let page = 2; page <= totalPages; page++) {
                                this.orderRequests.push({
                                    url: '/api/pos/auth/getOrders',
                                    method: 'get',
                                    async:   true,
                                    params: {
                                        user_id: this.user_id,
                                        filter_date: true,
                                        page: page,
                                    },
                                });
                            }

                        } else {
                            this.today_sale_history = [];
                            this.total_order = 0;
                        }
                    }).finally(() => this.NextOrderRequest());
                }
            },
            formatPrice(price) {
                return parseFloat(price).toFixed(2);
            },

            NextOrderRequest() {
                var thisthis = this;
                if (thisthis.orderRequests.length) {
                    thisthis.$root.$http(thisthis.orderRequests.shift())
                    .then(function(response) {
                        if (response.data.data && response.data.data.length > 0) {
                            thisthis.today_sale_history = thisthis.today_sale_history.concat(response.data.data);

                            thisthis.total_order = thisthis.total_order + response.data.data.length;

                            EventBus.$emit('setLocalForage', {'key': 'pos_today_sales', 'data': JSON.stringify(thisthis.today_sale_history)});
                        }
                    })
                    .finally(() => thisthis.NextOrderRequest());
                }
            }
        }
    }
</script>