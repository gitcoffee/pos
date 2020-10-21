<template>
    <div class="pos-invoice-panel" v-if="isUserLogin">
        <div class="pos_thermal">
            <div class="pos_shop_detail">
                <div class="details_row receipt-header">
                    <span class="main_heading" v-if="show_logo == 1">
                        <div class="pos_logo_thumb" v-if="pos_logo_result" >
                            <img :src="pos_logo" class="pos_logo" />
                        </div>
                        <div class="pos_logo_thumb" v-else >
                            <img :src="pos_logo_default" class="pos_logo" />
                        </div>
                    </span>
                    <span class="detail_value">
                        <div class="address-container" v-if="custom_address_status == 1">
                            
                            <span class="store_name" v-if="custom_address.store_name">{{ custom_address.store_name }}</span>

                            <span v-if="custom_address.store_address"><pre>{{  custom_address.store_address }}</pre></span>
                            
                            <span v-if="custom_address.email_address"><b>{{ $t('pos_sales.menu_content.print_invoice.email') }} </b>{{ custom_address.email_address }}</span>
                            
                            <span v-if="custom_address.website"><b>{{ $t('pos_sales.menu_content.print_invoice.website') }} </b><a :href="custom_address.website" target="_blank">{{ custom_address.website }}</a></span>
                            
                            <span v-if="custom_address.phone_number"><b>{{ $t('pos_sales.menu_content.print_invoice.phone') }}</b>{{ custom_address.phone_number }}</span>
                            
                            <span v-if="custom_address.cc_number"><b>{{ $t('pos_sales.menu_content.print_invoice.customer_care') }} </b>{{ custom_address.cc_number }}</span>
                            
                            <span v-if="custom_address.gstin"><b>{{ $t('pos_sales.menu_content.print_invoice.gstin') }} </b>{{ custom_address.gstin }}</span>
                        </div>
                        <div class="address-container" v-else>
                            <span v-if="outlet_address.address1">{{ outlet_address.address1 }},</span>
                            <span v-if="outlet_address.address2">{{ outlet_address.address2 }},</span>
                            <span v-if="outlet_address.state_name">{{ outlet_address.state_name }},</span>
                            <span v-if="outlet_address.country_name">{{ outlet_address.country_name }},</span>
                            <span v-if="outlet_address.postcode">{{ outlet_address.postcode }}</span>
                        </div>
                    </span>
                </div>
                <div class="separate_row"></div>
                
                <div class="details_row">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.date') }}</span>
                    <span class="detail_value">{{ orderData.created_at }}</span>
                </div>
                <div class="details_row" v-if="orderData.id || orderData.order_id">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.order_id') }} </span>
                    <span class="detail_value bold_content">#{{ orderData.id }}</span>
                </div>
                <div class="details_row" v-if="orderData.ref_id">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.order_ref_id') }}</span>
                    <span class="detail_value bold_content">#{{ orderData.ref_id }}</span>
                </div>
                <div class="details_row">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.cashier') }}</span>
                    <span class="detail_value">{{ pos_cashier.firstname }} {{ pos_cashier.lastname }}</span>
                </div>

                <div class="details_row">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.customer') }}</span>
                    <span class="detail_value padding_content">
                        <i class="fa fa-id-card"></i>
                        {{ orderData.customer.first_name }} {{ orderData.customer.last_name }}
                    </span>
                    
                </div>
                <div class="details_row" v-if="orderData.customer.phone">
                    <span class="main_heading"></span>
                    <span class="detail_value padding_content">
                        <i class="fa fa-phone"></i>
                        {{ orderData.customer.phone }}
                    </span>
                </div>
                <div class="details_row">
                    <span class="main_heading"></span>
                    <span class="detail_value padding_content">
                        <i class="fa fa-envelope"></i>
                        {{ orderData.customer.email }}
                    </span>
                </div>

                <div class="details_row" v-if="!orderData.no_shipping">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.mode_of_shipping') }}</span>
                    <span class="detail_value">{{ $t('pos_sales.menu_content.print_invoice.pickup') }}</span>
                </div>
                <div class="details_row">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.mode_of_payment') }}</span>
                    <span class="detail_value" v-if="orderData.payment_mode == 'Cash' || orderData.payment_mode == 'cash'">{{ $t('pos_sales.menu_content.print_invoice.cash_payment') }}</span>
                    <span class="detail_value" v-if="orderData.payment_mode == 'Card' || orderData.payment_mode == 'card'">{{ $t('pos_sales.menu_content.print_invoice.card_payment') }}</span>
                </div>
                <div class="details_row" v-if="orderData.bank_name">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.sales_history.text_bank_name') }}</span>
                    <span class="detail_value">{{ orderData.bank_name }}</span>
                </div>
            </div>
            
            <div class="separate_row"></div>

            <div class="order_items">
                <table class="itams_table">
                    <tr>
                        <th class="item_head">{{ $t('pos_sales.menu_content.print_invoice.product') }}</th>
                        <th class="item_head">{{ $t('pos_sales.menu_content.print_invoice.quantity') }}</th>
                        <th class="item_head">{{ $t('pos_sales.menu_content.print_invoice.price') }}</th>
                        <th class="item_head">{{ $t('pos_sales.menu_content.print_invoice.amount') }}</th>
                    </tr>

                    <tr v-for="(item, index) in orderData.items" :key="index">
                        <td class="invoice_product_name">
                            <div class="product_name">{{ item.name }}
                                <span class="product-attributes" v-if="item.additional">
                                    <span v-for="(attributes, index) in item.additional.attributes" :key="index">
                                        <i> <b>{{ attributes.attribute_name }}</b>: {{ attributes.option_label }}, </i>
                                    </span>
                                </span>
                            </div>
                        </td>
                        <td>{{ item.qty_ordered }}</td>
                        <td>
                            <span v-if="orderData.order_currency_code == current_currency_code">
                                    {{ current_currency_symbol }}{{ formatPrice(item.price) }}
                            </span>
                            <span v-else>
                                    {{ base_currency_symbol }}{{ formatPrice(item.base_price) }}
                            </span>
                        </td>
                        <td>
                            <span v-if="orderData.order_currency_code == current_currency_code">
                                    {{ current_currency_symbol }}{{ formatPrice(item.total) }}
                            </span>
                            <span v-else>
                                    {{ base_currency_symbol }}{{ formatPrice(item.base_total) }}
                            </span>
                        </td>
                    </tr>
                </table>
                
                <table class="itams_table">
                    <tr>
                        <td>{{ $t('pos_sales.menu_content.print_invoice.total_qty') }}</td>
                        <td>{{ orderData.total_qty_ordered }}</td>
                        
                        <td>{{ $t('pos_sales.menu_content.print_invoice.sub_total') }}</td>
                        <td>
                            <span v-if="orderData.order_currency_code == current_currency_code">
                                {{ current_currency_symbol }}{{ formatPrice(orderData.sub_total) }}
                            </span>
                            <span v-else>
                                {{ base_currency_symbol }}{{ formatPrice(orderData.base_sub_total) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>{{ $t('pos_sales.menu_content.print_invoice.discount') }}</td>
                        <td>
                            <span v-if="orderData.order_currency_code == current_currency_code">
                                {{ current_currency_symbol }}{{ formatPrice(orderData.discount_amount) }}
                            </span>
                            <span v-else>
                                {{ base_currency_symbol }}{{ formatPrice(orderData.base_discount_amount) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>{{ $t('pos_sales.menu_content.print_invoice.tax') }}</td>
                        <td>
                            <span v-if="orderData.order_currency_code == current_currency_code">
                                {{ current_currency_symbol }}{{ formatPrice(orderData.tax_amount) }}
                            </span>
                            <span v-else>
                                {{ base_currency_symbol }}{{ formatPrice(orderData.base_tax_amount) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><b>{{ $t('pos_sales.menu_content.print_invoice.total') }}</b></td>
                        <td>
                            <span v-if="orderData.order_currency_code == current_currency_code">
                                <b>{{ current_currency_symbol }}{{ formatPrice(orderData.grand_total) }}</b>
                            </span>
                            <span v-else>
                                <b>{{ base_currency_symbol }}{{ formatPrice(orderData.base_grand_total) }}</b>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>
                            <span v-if="orderData.payment_mode == 'Cash' || orderData.payment_mode == 'cash'">{{ $t('pos_sales.menu_content.print_invoice.cash_payment') }}: </span>
                            <span v-else >{{ $t('pos_sales.menu_content.print_invoice.card_payment') }}: </span>
                        </td>
                        <td>
                            <div v-if="orderData.tendered_amount">
                                <span>
                                    {{ current_currency_symbol }}{{ formatPrice(orderData.tendered_amount) }}
                                </span>
                            </div>
                            <div v-else>
                                0.00
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><b>{{ $t('pos_sales.menu_content.print_invoice.balance') }}</b></td>
                        <td>
                            <div v-if="orderData.change_amount">
                                <span>
                                    <b>{{ current_currency_symbol }}{{ formatPrice(orderData.change_amount) }}</b>
                                </span>
                            </div>
                            <div v-else>
                                0.00
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="separate_row"></div>
            
            <div class="pos_order_note">
                <div class="details_row">
                    <span class="main_heading">{{ $t('pos_sales.menu_content.print_invoice.order_note') }}</span>
                    <span class="detail_value">{{ orderData.order_note ? orderData.order_note : 'N/A' }}</span>
                </div>
            </div>
            
            <div class="separate_row"></div>
            
            <div class="footer_note" v-if="custom_footer_note.length">
                <span><pre>{{ custom_footer_note }}</pre></span>
            </div>
            <div class="footer_note" v-else >
                <span>{{ $t('pos_sales.menu_content.print_invoice.footer_note_1') }}</span>
                <span>{{ $t('pos_sales.menu_content.print_invoice.footer_note_2') }}</span>
            </div>
            <span class="footer_barcode_container" v-if="order_barcode_status == 1">
                <div class="order_barcode_thumb" v-if="orderData.order_barcode" >
                    <img :src="orderData.order_barcode" class="order_barcode" />
                </div>
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['localObject', 'orderData'],
        data() {
            return {
                user_id: 0,
                pos_cashier: {},
                pos_logo: window.pos_logo_image,
                pos_logo_result: window.pos_logo_result,
                pos_logo_default: window.pos_logo_default,
                current_currency_code: window.pos_currency_code,
                current_currency_symbol: window.pos_currency_symbol,
                base_currency_symbol: window.base_currency_symbol,
                show_logo: window.show_logo,
                custom_address_status: window.custom_address_status,
                outlet_address: Object,
                custom_address: window.custom_address,
                custom_footer_note: window.bill_footer_note,
                order_barcode_status: window.show_order_barcode,                
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        methods: {
            checkUserLogin() {
                if (window.channel_logo) {
                    this.pos_shop_logo = window.channel_logo;
                }
                if ( this.custom_address_status == 0) {
                    var outletAddress = this.localObject.pos_cashier.outlet_address;
                    this.outlet_address = outletAddress;
                }
                
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                    this.pos_cashier = this.localObject.pos_cashier;
                }
            },
            formatPrice(price) {
                return parseFloat(price).toFixed(2);
            },
        }
    }
</script>