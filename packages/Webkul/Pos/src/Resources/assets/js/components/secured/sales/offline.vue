<template>
    <div class="sale-offline-panel">
        <div class="sale-offline-list">
            <div class="pos-product-container" v-if="isUserLogin">
                <div class="product-list row-grid-3" v-if="offline_count != 0">
                    <div class="product-layout" v-for="(offline_order, index) in posOfflineOrders" :key="index">
                        <div class="order-detail" v-if="offline_order.offline.customer.email">
                            <div class="order_detail_head">
                                <div class="left_section" v-if="offline_order.online.id">
                                    <span class="head">{{ $t('pos_sales.menu_content.offline_sales.order_id') }}</span>
                                    <span><i>#{{ offline_order.online.id }}</i></span>
                                </div>
                                <div class="left_section" v-else-if="offline_order.online.ref_id">
                                    <span class="head">{{ $t('pos_sales.menu_content.offline_sales.order_ref_id') }}</span>
                                    <span><i>#{{ offline_order.online.ref_id }}</i></span>
                                </div>
                                <div class="right_section">
                                    <span class="head">{{ $t('pos_sales.menu_content.offline_sales.order_date') }}</span>
                                    <span><i>{{ offline_order.online.pos_order.order_date_time }}</i></span>
                                </div>
                            </div>

                            <div class="order_detail_head" v-bind:style="{ 'height': '60px' }" >
                                <div class="customer_data">
                                    <div class="name">
                                        <i class="fa fa-user"></i>
                                        {{ offline_order.offline.customer.first_name }} {{ offline_order.offline.customer.last_name }}
                                    </div>
                                    
                                    <div class="email" v-if="offline_order.offline.customer.phone">
                                        <i>
                                            <i class="fa fa-phone"></i>
                                            {{ offline_order.offline.customer.phone }}
                                        </i>
                                    </div>
                                    <div class="email">
                                        <i>
                                            <i class="fa fa-envelope"></i>
                                            {{ offline_order.offline.customer.email }}
                                        </i>
                                    </div>
                                </div>
                            </div>

                            <div class="order_detail_head" v-if="offline_order.offline.items">
                                <label class="head">
                                    <i class="fa fa-cart-plus"></i>
                                    {{ $t('pos_sales.menu_content.offline_sales.item_detail') }}
                                </label>
                                <div class="customer_data order_item_section">
                                    <table>
                                        <tr v-for="item in offline_order.offline.items">
                                            <td class="offline-product-name">{{ item.name }}
                                                <span class="product-attributes" v-if="item.additional" >
                                                    <span v-for="attributes in item.additional.attributes">
                                                        <i v-bind:style="{ 'text-align': 'left', 'font-style': 'italic' }"> <b>{{ attributes.attribute_name }}</b>: {{ attributes.option_label }}, </i>
                                                    </span>
                                                </span>
                                                
                                            </td>
                                            <td>{{ item.qty_ordered }} x </td>
                                            <td>
                                                <span v-if="offline_order.offline.order_currency_code == current_currency">
                                                    {{ item.price }}
                                                </span>
                                                <span v-else>
                                                    {{ item.currency_symbol }} {{ formatPrice(item.base_price) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span v-if="offline_order.offline.order_currency_code == current_currency">
                                                    <b>{{ item.total }}</b>
                                                </span>
                                                <span v-else>
                                                    <b>{{ item.currency_symbol }} {{ formatPrice(item.base_total) }}</b>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="order_detail_head">
                                <label class="head">
                                    <i class="fa fa-info-circle"></i>
                                    {{ $t('pos_sales.menu_content.hold_sales.text_note') }}
                                </label>
                                <div class="customer_data order_note">
                                    {{ offline_order.online.pos_order.order_note }}
                                </div>
                            </div>

                            <div class="offline_footer">
                                <div :class="{'pull-left': !pos_offline, 'text-center': offline_status}">
                                    <button class="btn btn-md btn-pos-primary" type="button" :text="$t('pos_sales.menu_content.hold_sales.button_cart')" @click="viewOfflineOrder(offline_order)">
                                        <i class="fa fa-eye"></i> {{ $t('pos_sales.menu_content.offline_sales.btn_view') }}
                                    </button>
                                </div>
                                <div class="pull-right" v-if="!pos_offline">
                                    <button class="btn btn-md btn-pos-success" type="button" :text="$t('pos_sales.menu_content.hold_sales.button_delete')" @click="syncOfflineOrder(index, offline_order)">
                                        <i class="fa fa-compress"></i>{{ $t('pos_sales.menu_content.offline_sales.btn_sync') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div v-else class="message-alert danger">
                    {{ $t('pos_sales.menu_content.offline_sales.no_offline_order') }}
                </div>
            </div>
        </div>
        <div class="sale-offline-detail" v-if="offline_count != 0">
            <div :class="{'pos-product-container': true, 'hide_sync_button': true}">
                <div class="sync_button_area" v-if="!pos_offline">
                    <button class="btn btn-lg btn-pos-success" type="button" @click="syncOfflineOrder">
                        <i class="fa fa-compress"></i>{{ $t('pos_sales.menu_content.offline_sales.btn_all_sync') }}
                    </button>
                </div>

                <div class="order_view_area">
                    <div v-if="order_view_status">
                        <div class="order_view_row" v-if="order_details.offline.customer.email">
                            <div class="order_row_data left_section">
                                <span class="label_head">
                                    {{ $t('pos_sales.menu_content.offline_sales.customer_detail') }}
                                </span>
                                <span class="order_view_value">
                                    <span class="name">
                                        <i class="fa fa-user"></i>
                                        {{ order_details.offline.customer.first_name }} {{ order_details.offline.customer.last_name }}</span>
                                    <span class="email">
                                        <i class="fa fa-envelope"></i>
                                        {{ order_details.offline.customer.email }}</span>
                                </span>
                            </div>
                            <div class="order_row_data right_section">
                                <span class="label_head">
                                    {{ $t('pos_sales.menu_content.offline_sales.order_detail') }}
                                </span>
                                <span class="order_view_value" v-if="order_details.online.id">
                                    #{{ order_details.online.id }}
                                </span>
                                <span class="order_view_value" v-else-if="order_details.online.ref_id">
                                    #{{ order_details.online.ref_id }}
                                </span>
                                <span class="order_view_value">
                                    <i class="fa fa-calendar"></i>
                                    {{ order_details.online.pos_order.order_date }} {{ order_details.online.pos_order.order_time }}
                                </span>
                            </div>
                        </div>

                        <div class="order_view_row" v-if="order_details.offline.customer.email">
                            <div class="order_row_data">
                                <span class="label_head">
                                    {{ $t('pos_sales.menu_content.sales_history.text_order_summary') }}
                                </span>
                                <span class="order_view_value">

                                    <div class="row_value">
                                        <div v-for="(item, index) in order_details.offline.items" :key="index">
                                            <div class="product_info">
                                                <span class="product_name">{{ item.name }}
                                                    <span class="product-attributes" v-if="item.type == 'configurable'" >
                                                        <span v-for="attributes in item.additional.attributes">
                                                            <i> <b>{{ attributes.attribute_name }}</b>: {{ attributes.option_label }}, </i>
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="product_unit">
                                                    {{ item.qty_ordered }}
                                                    {{ $t('pos_sales.menu_content.sales_history.text_units') }}
                                                </span>
                                            </div>
                                            <div class="price_info">
                                                <span class="product_price" v-if="current_currency == order_details.offline.order_currency_code">
                                                    {{ currency_symbol }} {{ formatPrice(item.total) }}
                                                </span>
                                                <span class="product_price" v-else>
                                                    {{ base_symbol }} {{ formatPrice(item.base_total) }}
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>


                        <div class="order_view_row" v-bind:style="{ 'border-bottom': 'none' }" v-if="order_details.offline.customer.email">
                            <div class="order_row_data">
                                
                                <span class="order_view_value">
                                    <div v-if="current_currency == order_details.offline.order_currency_code">
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">{{ $t('pos_sales.menu_content.sales_history.text_sub_total') }}</div>
                                                <div class="total_value">
                                                    {{ currency_symbol }} {{ formatPrice(order_details.offline.sub_total) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    {{ $t('pos_sales.menu_content.sales_history.text_discount') }}
                                                </div>
                                                <div class="total_value">
                                                    {{ currency_symbol }} {{ formatPrice(order_details.offline.discount_amount) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    {{ $t('pos_sales.menu_content.sales_history.text_taxes') }}
                                                </div>
                                                <div class="total_value">
                                                    {{ currency_symbol }} {{ formatPrice(order_details.offline.tax_amount) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row" v-bind:style="{ 'border-bottom': 'none' }">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    <b>{{ $t('pos_sales.menu_content.sales_history.text_grand_total') }}</b>
                                                </div>
                                                <div class="total_value order_id">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.grand_total) }} 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    <span v-if="order_details.offline.payment_mode == 'cash'">
                                                        {{ $t('pos_sales.menu_content.sales_history.text_mode_cash') }}
                                                    </span>
                                                    <span v-else >
                                                        {{ $t('pos_sales.menu_content.sales_history.text_mode_card') }}
                                                    </span>
                                                </div>
                                                <div class="total_value">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.tendered_amount) }} 
                                                </div>
                                            </div>

                                            <div class="total_row_value"><span v-if="order_details.offline.bank_name"> - <i>({{ order_details.offline.bank_name }})</i></span></div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    <b>{{ $t('pos_sales.menu_content.sales_history.text_balance') }}</b>
                                                </div>
                                                <div class="total_value">
                                                    <b>{{ base_symbol }} {{ formatPrice(order_details.offline.change_amount) }} </b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    {{ $t('pos_sales.menu_content.sales_history.text_sub_total') }}
                                                </div>
                                                <div class="total_value">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.base_sub_total) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    {{ $t('pos_sales.menu_content.sales_history.text_discount') }}
                                                </div>
                                                <div class="total_value">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.base_discount_amount) }} 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    {{ $t('pos_sales.menu_content.sales_history.text_taxes') }}
                                                </div>
                                                <div class="total_value">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.base_tax_amount) }} 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row" v-bind:style="{ 'border-bottom': 'none' }">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    <b>{{ $t('pos_sales.menu_content.sales_history.text_grand_total') }}</b>
                                                </div>
                                                <div class="total_value order_id">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.base_grand_total) }} 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    <span v-if="order_details.offline.payment_mode == 'cash'">
                                                        {{ $t('pos_sales.menu_content.sales_history.text_mode_cash') }}
                                                    </span>
                                                    <span v-else >
                                                        {{ $t('pos_sales.menu_content.sales_history.text_mode_card') }}
                                                    </span>
                                                </div>
                                                <div class="total_value">
                                                    {{ base_symbol }} {{ formatPrice(order_details.offline.tendered_amount) }} 
                                                </div>
                                            </div>

                                            <div class="total_row_value"><span v-if="order_details.offline.bank_name"> - <i>({{ order_details.offline.bank_name }})</i></span></div>
                                        </div>
                                        <div class="order_row">
                                            <div class="total_row_value">
                                                <div class="total_text">
                                                    <b>{{ $t('pos_sales.menu_content.sales_history.text_balance') }}</b>
                                                </div>
                                                <div class="total_value">
                                                    <b>{{ base_symbol }} {{ formatPrice(order_details.offline.change_amount) }} </b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>

                        <div class="text-center" v-bind:style="{ 'padding-right': '0px' }">
                            <button type="button" class="btn btn-lg btn-pos-primary" @click="printInvoice(order_details.offline)">
                                <i class="fa fa-print"></i> {{ $t('pos_sales.menu_content.sales_history.button_print_invoice') }}
                            </button>
                        </div>
                    </div>
                    <div class="message-alert danger" v-else>
                        {{ $t('pos_sales.menu_content.offline_sales.no_offline_order') }}
                    </div>
                </div>
            </div>
        </div>

        <div v-if="this.$root.posCommonModal.syncAllOrders">
            <pos-common-modal
            id="syncAllOrders"
            :showClose='true'
            :is-open="this.$root.posCommonModal.syncAllOrders"
            >
                <h4 slot="header">{{ $t('pos_sales.menu_content.offline_sales.sync_all_order') }}</h4>
                
                <div slot="body">
                    <bagisto-pos-progress-bar
                        :isActive=isActive
                        :progressWidth=progressWidth
                        :progressData=progressData
                        :messageContent=messageContent
                    ></bagisto-pos-progress-bar>
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
                offline_status: false,
                posOfflineOrders: [],
                syncOfflineCustomers: [],
                offline_count: 0,
                current_currency: window.pos_currency_code,
                currency_symbol: window.pos_currency_symbol,
                order_details: {},
                order_view_status: false,
                base_symbol: window.base_currency_symbol,
                error_offline_action: this.$t('pos_view.error_offline_action'),
                
                orderRequests: [],
                totalOrderIterations: 0,
                success_count: 0,
                isActive: 0,
                progressWidth: 0,
                progressData: {
                    noteInfo: this.$t('pos_sales.menu_content.offline_sales.dont_refresh'),
                    textContent: '',
                    textContentType: '',
                    messageStatus: false,
                },
                messageContent: [],
                pos_offline: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();
                return this.user_id;
            }
        },
        created() {
            this.loadOfflineCarts();
        },
        methods: {
            checkUserLogin() {
                this.pos_offline = this.$root.$root.offline;

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            loadOfflineCarts() {
                if (this.$root.offline) {
                    this.offline_status = true;
                } else {
                    this.offline_status = false;
                }
                var offline_length = Object.keys(this.localObject.pos_offline_orders).length;
                if ( offline_length > 0 ) {
                    this.offline_count = offline_length;
                    this.posOfflineOrders = this.localObject.pos_offline_orders;
                }
            },
            formatPrice(price) {
                return parseFloat(price).toFixed(2);
            },

            viewOfflineOrder(orderDetail) {
                this.order_view_status = true;
                this.order_details = orderDetail;
            },
            syncOfflineOrder(index, orderDetail) {
                this.$root.toggleButtonDisable(true);

                var thisthis = this;
                if (thisthis.$root.offline) {

                    thisthis.offline_status = true;

                    thisthis.$toast.warning(thisthis.error_offline_action, {
                        position: 'top-right',
                        duration: 2000,
                    });
                    thisthis.$root.toggleButtonDisable(false);
                } else {
                    thisthis.offline_status = false;
                    thisthis.$root.showCommonModal('syncAllOrders');

                    setTimeout(() => {
                        thisthis.isActive = 1;

                        var count = 0;
                        var total_customers = Object.keys(thisthis.localObject.pos_offline_customers).length;

                        if ( total_customers > 0) {
                            thisthis.syncOfflineCustomers = thisthis.localObject.pos_offline_customers;
                            
                            $.each(thisthis.syncOfflineCustomers, (key, customer) => {
                                customer.customer_email = '';
                                this.syncOffliceCustomer(key, customer);
                                count++;
                            });
                        }
                        if (count == total_customers) {
                            this.order_view_status = false;
                            this.order_details = {};

                            if (orderDetail) {
                                // only current order sync
                                thisthis.totalOrderIterations = 1;
                                var process_order = {
                                    order_data: orderDetail.online,
                                    offline_order: orderDetail.offline,
                                    order_key: index
                                };
                                
                                thisthis.orderRequests.push({
                                    url: '/api/pos/auth/addOrder',
                                    method: 'post',
                                    async:   true,
                                    data: process_order,
                                });
                                thisthis.NextOrderRequest();
                            } else {
                                // all order sync
                                var total_orders =  Object.keys(thisthis.localObject.pos_offline_orders).length;

                                thisthis.totalOrderIterations = total_orders;

                                if ( total_orders > 0 ) {
                                    thisthis.progressData.textContentType = 'information';
                                    thisthis.progressData.textContent = thisthis.$t('pos_sales.menu_content.offline_sales.total_offline_order', {total_order_found: total_orders});

                                    let order_requests = thisthis.localObject.pos_offline_orders;
                                    
                                    $.each(order_requests, (key, order) => {
                                        var process_order = {
                                            order_data: order.online,
                                            offline_order: order.offline,
                                            order_key: key
                                        };
                                        thisthis.orderRequests.push({
                                            url: '/api/pos/auth/addOrder',
                                            method: 'post',
                                            async:   true,
                                            data: process_order,
                                        });
                                    });
                                }

                                if (thisthis.orderRequests.length === total_orders) {
                                    thisthis.NextOrderRequest();
                                }
                            }
                        }
                    });
                }
            },
            NextOrderRequest() {
                var thisthis = this;
                
                if (thisthis.orderRequests.length) {
                    thisthis.progressWidth = (100 - (thisthis.orderRequests.length / thisthis.totalOrderIterations) * 100);

                    let currentRequest = thisthis.orderRequests.shift();
                    let current_order_key = currentRequest.data.order_key;

                    thisthis.$root.$http(currentRequest)
                    .then(function(response) {
                        if (response.data.status) {
                            thisthis.success_count = thisthis.success_count + 1;
                            thisthis.progressData.messageStatus = true;
                            thisthis.messageContent.push({
                                class: 'success',
                                text: thisthis.$t('pos_sales.menu_content.offline_sales.total_sync_order', {total_sync: thisthis.success_count}),
                            });
                            
                            thisthis.updateProductQty(currentRequest.data.offline_order);

                            if ( (Object.keys(thisthis.localObject.pos_offline_orders).length == 1) ) {
                                thisthis.offline_count == 0;
                                thisthis.posOfflineOrders = [];
                                thisthis.localObject.pos_offline_orders = [];
                            } else {
                                thisthis.localObject.pos_offline_orders.splice(current_order_key, 1);
                            }
                            
                            EventBus.$emit('setLocalForage', {'key': 'pos_offline_orders', 'data': JSON.stringify(thisthis.localObject.pos_offline_orders)});
                        }
                    })
                    .finally(() => thisthis.NextOrderRequest());
                } else {
                    thisthis.isActive = 0;
                    thisthis.$root.hideCommonModal('syncAllOrders');
                    thisthis.$root.toggleButtonDisable(false);
                    this.$toast.success(thisthis.$t('pos_sales.menu_content.offline_sales.total_sync_success'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            },
            updateProductQty(orderData) {
                var thisthis = this;
                $.each(orderData.items, function(key_index, ordered_product) {
                    if (ordered_product.type == 'simple') {
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

            printInvoice(orderDetail) {
                EventBus.$emit('printPosOrder', orderDetail);
            },
            syncOffliceCustomer(key, customerDetail) {
                var thisthis = this;
                if (customerDetail.email) {

                    this.$http.post('/api/pos/auth/addCustomer', customerDetail)
                    .then((response)  =>  {
                        if (response.data.status) {
                            thisthis.localObject.pos_offline_customers.splice(key, 1);

                            EventBus.$emit('setLocalForage', {'key': 'pos_offline_customers', 'data': JSON.stringify(thisthis.localObject.pos_offline_customers)});
                        } else {
                            if (!response.data.status && (response.data.key && response.data.key == 'already_exist')) {
                                thisthis.localObject.pos_offline_customers.splice(key, 1);

                                EventBus.$emit('setLocalForage', {'key': 'pos_offline_customers', 'data': JSON.stringify(thisthis.localObject.pos_offline_customers)});
                            }
                        }
                    })
                    .catch(function (error) {});
                }
            },
        }
    }
</script>