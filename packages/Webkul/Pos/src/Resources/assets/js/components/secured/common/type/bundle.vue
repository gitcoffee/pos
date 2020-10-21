<template>
    <div class="bundle-options-wrapper" v-if="options">
        <div class="price-container">
            <label>{{ $t('pos_home.pos_products.total_amount') }}</label>

            <div>{{ currency_options + formated_total_price }}</div>
        </div>

        <form id="bundle-app" autocomplete="off" @submit.prevent="validateOptions" method="POST">
            <div class="bundle-option-list">
                <bundle-options
                    v-for="(option, index) in options"
                    :option="option"
                    :key="index"
                    :index="index"
                    @onProductSelected="productSelected(option, $event)">
                </bundle-options>
            </div>

            <div class="pos-buttons text-center">
                <button type="submit" class="pos-button button-md button-dark">{{ $t('pos_home.pos_products.btn_add_to_cart') }}</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['localObject', 'product', 'bundle_options'],
        data() {
            return {
                options: JSON.parse(this.bundle_options),
                currency_options: window.pos_currency_symbol,
                product_data: {},
                product_additional: {
                    product_id: this.product.id,
                    quantity: 1,
                    bundle_options: {},
                    bundle_option_qty: {},
                    attributes: {},
                },
                base_price: 0,
                converted_price: 0,
                formatted_converted_price: 0,
            }
        },

        computed: {
            formated_total_price: function() {
                var total               = 0;
                    this.base_price     = 0;
                    
                for (var key in this.options) {
                    for (var key1 in this.options[key].option_products) {
                        if (! this.options[key].option_products[key1].is_default) {
                            continue;
                        }
                        
                        if ( this.options[key].option_products[key1].special_price ) {
                            total += this.options[key].option_products[key1].qty * this.options[key].option_products[key1].converted_special_price;

                            this.base_price += this.options[key].option_products[key1].qty * this.options[key].option_products[key1].special_price;
                        } else {
                            total += this.options[key].option_products[key1].qty * this.options[key].option_products[key1].converted_price;

                            this.base_price += this.options[key].option_products[key1].qty * this.options[key].option_products[key1].price;
                        }
                    }
                }

                return parseFloat(total).toFixed(2);
            }
        },

        methods: {
            validateOptions() {
                var thisthis = this;
                this.$root.toggleButtonDisable(true);
                
                this.$validator.validateAll().then(result => {
                    if (result) {
                            var option_product_ids  = {};
                            var option_product_qtys = {};
                            var attributes          = {};
                            thisthis.product_additional.bundle_options = {};
                            
                            $.each(this.$validator.fields.items, function(key, field) {
                                var currentOption           = thisthis.options[field.el.id];
                                var currentOptionProducts   = currentOption.option_products;
                                var option_value            = field.el.value;
                                var option_label            = '';

                                    if ( attributes[field.el.id] && attributes[field.el.id].option_label ) {
                                        option_label = attributes[field.el.id].option_label;
                                    }
                                    attributes[field.el.id] = {
                                        attribute_name: currentOption.label,
                                        option_id: field.el.id,
                                        option_label: option_label,
                                        option_detail: thisthis.options[field.el.id]
                                    };
                                    
                                if (field.el.name == 'bundle_options['+field.el.id+'][]') {
                                    var associated_product = currentOptionProducts[option_value];
                                    var price = 0;
                                    if ( associated_product.special_price ) {
                                        price = associated_product.formated_special_price;
                                    } else {
                                        price = associated_product.formated_price;
                                    }

                                    if (! option_product_ids[field.el.id] ) {
                                        option_product_ids[field.el.id] = [];
                                    }
                                    if (! option_product_qtys[field.el.id] ) {
                                        option_product_qtys[field.el.id] = null;
                                    }
                                    if ( field.el.type == 'radio' || field.el.type == 'checkbox' ) {
                                        if ( field.el.checked === true) {
                                            option_product_ids[field.el.id].push(option_value);

                                            if ( field.el.type == 'checkbox' ) {
                                                option_product_qtys[field.el.id] = currentOptionProducts[option_value].qty;
                                                
                                                if ( attributes[field.el.id].option_label ) {
                                                    attributes[field.el.id].option_label =  attributes[field.el.id].option_label + ', ' +currentOptionProducts[option_value].qty + ' x ' + associated_product.name + ' ' + price;
                                                } else {
                                                    attributes[field.el.id].option_label = currentOptionProducts[option_value].qty + ' x ' + associated_product.name + ' ' + price;
                                                }                                                
                                            } else {
                                                attributes[field.el.id].option_label = ' x ' + associated_product.name + ' ' + price;
                                            }
                                        }
                                    } else {
                                        if ( field.el.type == 'select-multiple' ) {
                                            $.each(field.el.options, function(key, element) {
                                                if ( element.selected == true) {
                                                    var associated_product = currentOptionProducts[element.value];
                                                    var element_price = 0;
                                                    if ( associated_product.special_price ) {
                                                        element_price = associated_product.formated_special_price;
                                                    } else {
                                                        element_price = associated_product.formated_price;
                                                    }
                                                    
                                                    option_product_ids[field.el.id].push(element.value);
                                                    option_product_qtys[field.el.id] = associated_product.qty;

                                                    if ( attributes[field.el.id].option_label ) {
                                                        attributes[field.el.id].option_label =  attributes[field.el.id].option_label + ', ' + associated_product.qty + ' x ' + associated_product.name + ' ' + element_price;
                                                    } else {
                                                        attributes[field.el.id].option_label = associated_product.qty + ' x ' + associated_product.name + ' ' + element_price;
                                                    }
                                                }
                                            });
                                        } else {
                                            option_product_ids[field.el.id].push(option_value);
                                            
                                            attributes[field.el.id].option_label = ' x ' + associated_product.name + ' ' + price;
                                        }
                                    }
                                    thisthis.product_additional.bundle_options = option_product_ids;
                                } else {
                                    if (field.el.name == 'qty['+field.el.id+']') {
                                        option_product_qtys[field.el.id] = option_value;
                                        
                                        attributes[field.el.id].option_label = option_value + attributes[field.el.id].option_label;
                                    }
                                }
                                thisthis.product_additional.bundle_option_qty = option_product_qtys;
                                thisthis.product_additional.attributes = attributes;
                            });
                            
                            thisthis.prepareForCart(thisthis.product, thisthis.product_additional);     
                            
                            this.$root.toggleButtonDisable(false);
                            EventBus.$emit('hideCommonModal', thisthis.product.type);
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            },

            productSelected: function(option, value) {
                var selectedProductIds = Array.isArray(value) ? value : [value];

                for (var key in option.option_products) {
                    option.option_products[key].is_default = selectedProductIds.indexOf(option.option_products[key].id) > -1 ? 1 : 0;
                }
            },

            prepareForCart: function(product, additional) {
                var self                = this;
                var pos_qty             = {};
                var product_id          = product.id;
                pos_qty[product_id]     = 1;

                self.product_data       = {
                    id:                 product_id,
                    quantity:           1,
                    pos_qty:            JSON.stringify(pos_qty),
                    sku:                product.sku,
                    type:               product.type,
                    name:               product.name,
                    coupon_code:        '',
                    weight:             parseFloat(product.weight),
                    total_weight:       (1 * parseFloat(product.weight)),
                    base_total_weight:  (1 * parseFloat(product.weight)),
                    price:              self.formated_total_price,
                    formated_price:     self.currency_options + self.formated_total_price,
                    base_price:         self.base_price,
                    custom_price:       0.00,
                    
                    total:              (1 * parseFloat(self.formated_total_price)),
                    base_total:         (1 * parseFloat(self.base_price)),
                    
                    tax_category_id:    product.tax_category_id,
                    tax_percent:        product.tax_percent,
                    tax_amount:         (((1 * parseFloat(self.formated_total_price)) * product.tax_percent) / 100 ),
                    base_tax_amount:    (((1 * parseFloat(self.base_price)) * product.tax_percent) / 100 ),

                    discount_percent:   0.00,
                    discount_amount:    0.00,
                    base_discount_amount: 0.00,

                    additional:         additional,
                };
                
                EventBus.$emit('cartAddProduct', self.product_data);
            }
        }
    }
</script>