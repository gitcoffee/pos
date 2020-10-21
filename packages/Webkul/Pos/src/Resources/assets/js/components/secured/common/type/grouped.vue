<template>
    <div class="grouped-product-container" v-if="grouped_products">
        <div class="price-container">
            <label>{{ $t('pos_home.pos_products.total_amount') }}</label>

            <div>{{ currency_options + formated_total_price }}</div>
        </div>
        
        <form id="grouped-app" autocomplete="off" @submit.prevent="validateOptions" method="POST">
            <ul class="grouped-product-list">
                <li class="heading">
                    <div>{{ $t('pos_home.pos_products.name') }}</div>
                    <div>{{ $t('pos_home.pos_products.quantity') }}</div>
                </li>
                <li v-for="(grouped_product, group_index) in grouped_products" :key="group_index">
                    <div class="image">
                        <img :src="grouped_product.image.small_image_url" :alt="grouped_product.name" :title="grouped_product.name" v-if="grouped_product.image.small_image_url" />

                        <span class="name" v-if="grouped_product.special_price">
                            {{ grouped_product.name }} + {{ grouped_product.formated_special_price }}
                        </span>

                        <span class="name" v-else >
                            {{ grouped_product.name }} + {{ grouped_product.formated_price }}
                        </span>
                    </div>

                    <div class="qty">
                        <quantity-panel
                            :control-name="group_index"
                            :field-required="1"
                            :validations="'required|numeric|min_value:1'"
                            :quantity="grouped_product.qty"
                            :min-quantity="0"
                            @onQtyUpdated="qtyUpdated($event)">
                        </quantity-panel>
                    </div>
                </li>
            </ul>

            <div class="pos-buttons text-center">
                <button type="submit" class="pos-button button-md button-dark">{{ $t('pos_home.pos_products.btn_add_to_cart') }}</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['localObject', 'product', 'grouped_associated'],
        data() {
            return {
                grouped_products: JSON.parse(this.grouped_associated),
                quantities: JSON.parse(this.product.quantity),
                currency_options: window.pos_currency_symbol
            }
        },

        computed: {
            formated_total_price: function() {
                var total               = 0;
                    this.base_price     = 0;
                    
                for (var key in this.grouped_products) {

                    var simple_product = this.grouped_products[key];
                    if ( simple_product.special_price ) {
                        var currentTime = new Date(); 
                        var fromTime    = new Date(simple_product.special_price_from);
                        var toTime      = new Date(simple_product.special_price_to);
                        
                        if ( currentTime.getTime() >= fromTime.getTime()
                            && currentTime.getTime() <= toTime.getTime()
                        ) {
                            total += simple_product.qty * simple_product.converted_special_price;

                            this.base_price += simple_product.qty * simple_product.special_price;
                        }
                    } else {
                        total += simple_product.qty * simple_product.converted_price;

                        this.base_price += simple_product.qty * simple_product.price;
                    }                   
                }

                return parseFloat(total).toFixed(2);
            }
        },

        methods: {
            validateOptions() {
                var thisthis    = this;
                var productObjs = this.localObject.pos_products;
                this.$root.toggleButtonDisable(true);
                
                this.$validator.validateAll().then(result => {
                    if (result) {
                        var success_count = 0;
                        $.each(this.$validator.fields.items, function(key, field) {
                            var associated_product_qty = thisthis.quantities[field.el.id];

                            if (Object.keys(thisthis.localObject.pos_offline_orders).length > 0) {
                                var totalOrderProductQty = 0;
                                
                                $.each(thisthis.localObject.pos_offline_orders, function(key, offlineOrder) {
                                    totalOrderProductQty = totalOrderProductQty + thisthis.checkOrderProduct(offlineOrder.offline.items, {id: field.el.id});
                                });
                                associated_product_qty = associated_product_qty - totalOrderProductQty;
                            }
            
                            if ( field.el.value > associated_product_qty ) {
                                thisthis.$validator.errors.items.push({
                                    id: field.el.id,
                                    field: field.el.name,
                                    msg: thisthis.$t('pos_home.pos_products.error.no_available_qty', {available_qty: associated_product_qty}),
                                    rule: "min_value",
                                    scope: null,
                                });
                                thisthis.$toast.error(thisthis.$t('pos_home.pos_products.error.no_available_qty', {available_qty: associated_product_qty}), {
                                    position: 'top-right',
                                    duration: 2000,
                                    });
                                return false;
                            } else {
                                success_count += 1;
                            }
                        });
                        
                        if (Object.keys(this.$validator.fields.items).length == success_count) {
                            $.each(this.$validator.fields.items, function(key, field) {
                            var product             = thisthis.grouped_products[field.el.id];
                                product.added_qty   = field.el.value;
                                product.quantity    = thisthis.product.quantity;

                                var productObj = $.grep(productObjs, function(prod_obj){return prod_obj.id === product.id;})[0];
                                
                                product.customerGroupPrices = {};
                                product.customerGroupPrices = productObj.customerGroupPrices;
                                
                                EventBus.$emit('cartAddProduct', product);
                            });
                        }

                        this.$root.toggleButtonDisable(false);
                        EventBus.$emit('hideCommonModal', thisthis.product.type);
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
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
            
            qtyUpdated: function(product_obj) {
                for (var key in this.grouped_products) {
                    if (key ==  product_obj.field ) {
                        this.grouped_products[key].qty = product_obj.qty;
                    }
                }
            }
        }        
    }
</script>