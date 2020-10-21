<template>
    <div class="pos-attributes">

        <div class="product-variation-section">

            <div v-for='(attribute, attribute_index) in childAttributes' :row-id="attribute_index" :key="attribute_index" class="attribute">
                <label class="required" v-text="attribute.label"></label>
                
                <span v-if="! attribute.swatch_type || attribute.swatch_type == '' || attribute.swatch_type == 'dropdown'">
                    <ul class="field-control" :validate="'required'" :id="attribute.id" :option-code="attribute.code" >
                        <li v-for='(option, index) in attribute.options' :id="option.id" :key="index" :class="{'option-value': true, 'active': index === attribute.selectedIndex}" @click="configure(attribute, $event.target.id)">{{ option.label }} </li>
                    </ul>
                </span>

                <span class="pos-swatch-container" v-else>
                    <label class="pos-swatch"
                        v-for='(option, index) in attribute.options'
                        v-if="option.id"
                        :data-id="option.id"
                        :for="['attribute_' + attribute.id + '_option_' + option.id]">

                        <input type="radio"
                            v-validate="'required'"
                            :name="['super_attribute[' + attribute.id + ']']"
                            :id="['attribute_' + attribute.id + '_option_' + option.id]"
                            :value="option.id"
                            :data-vv-as="'&quot;' + attribute.label + '&quot;'"
                            @change="configure(attribute, $event.target.value)"/>

                        <span v-if="attribute.swatch_type == 'color'" :style="{ background: option.swatch_value }"></span>

                        <img v-if="attribute.swatch_type == 'image'" :src="option.swatch_value" />

                        <span v-if="attribute.swatch_type == 'text'">
                            {{ option.label }}
                        </span>

                    </label>

                    <span v-if="! attribute.options.length" class="no-options">{{ $t('pos_home.pos_products.warning_select_product_variation') }}</span>
                </span>
            </div>
        </div>

        <div class="variation-price" v-if="selectedOptionPriceStatus">
            <span class="variation-price-text">Price</span>
            <span class="variation-price-value">{{ selectedOptionPrice }}</span>
        </div>

        <div class="pos-buttons text-center">
            <button type="button" :disabled="disabled_button" class="pos-button button-md button-dark" @click="validateOptions(childAttributes)">{{ $t('pos_home.pos_products.btn_add_to_cart') }}</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['product', 'product_variations'],
        data() {
            return {
                config: JSON.parse(this.product_variations),
                childAttributes: [],
                selected_options: {},
                disabled_button: true,
                product_data: {},
                product_additional: {
                    product_id: this.product.id,
                    quantity: 1,
                    attributes: {},
                    super_attribute: [],
                    selected_configurable_option: 0,
                },
                selectedProductId: '',
                simpleProduct: null,
                galleryImages: [],
                selectedOptionPriceStatus: false,
                selectedOptionPrice: 0,
            }
        },
        
        created () {
            var config = JSON.parse(this.product_variations);

            var childAttributes = this.childAttributes,
                attributes = config.attributes.slice(),
                index = attributes.length,
                attribute;

            while (index--) {
                attribute = attributes[index];
    
                attribute.options = [];

                if (index) {
                    attribute.disabled = true;
                } else {
                    this.fillSelect(attribute);
                }

                attribute = Object.assign(attribute, {
                    childAttributes: childAttributes.slice(),
                    prevAttribute: attributes[index - 1],
                    nextAttribute: attributes[index + 1]
                });

                childAttributes.unshift(attribute);
            }
        },

        methods: {
            validateOptions(attributes) {
                let attr_length = Object.keys(attributes).length;
                let selected_attr_length = Object.keys(this.product_additional.attributes).length;

                if ( attr_length != selected_attr_length ) {
                    this.$toast.warning(this.$t('pos_home.pos_cart.error.error_select_all_attributes'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                    this.$root.toggleButtonDisable(true);
                } else {
                    let selected_opt_product_id = this.product_additional.selected_configurable_option;
                    let opt_product_prices      = this.config.variant_prices;
                    let selected_price          = this.product.formated_price;
                    let converted_price         = this.product.converted_price;
                    let base_price              = this.product.price;

                    if (opt_product_prices[selected_opt_product_id].final_price.formated_price) {
                        selected_price  = opt_product_prices[selected_opt_product_id].final_price.formated_price;
                        converted_price = opt_product_prices[selected_opt_product_id].final_price.price;
                        base_price      = opt_product_prices[selected_opt_product_id].final_price.price;
                    }

                    this.product_data = {
                        id:                 selected_opt_product_id,
                        quantity:           1,
                        pos_qty:            this.product.quantity,
                        sku:                this.product.sku,
                        type:               this.product.type,
                        name:               this.product.name,
                        coupon_code:        '',
                        weight:             parseFloat(this.product.weight),
                        total_weight:       (1 * parseFloat(this.product.weight)),
                        base_total_weight:  (1 * parseFloat(this.product.weight)),
                        
                        price:              converted_price,
                        formated_price:     selected_price,
                        base_price:         base_price,
                        custom_price:       0.00,
                        
                        total:              (1 * parseFloat(converted_price)),
                        base_total:         (1 * parseFloat(base_price)),
                        
                        tax_category_id:    this.product.tax_category_id,
                        tax_percent:        this.product.tax_percent,
                        tax_amount:         (((1 * parseFloat(converted_price)) * this.product.tax_percent) / 100 ),
                        base_tax_amount:    (((1 * parseFloat(base_price)) * this.product.tax_percent) / 100 ),
                        discount_percent:   0.00,
                        discount_amount:    0.00,
                        base_discount_amount: 0.00,
                        
                        customerGroupPrices: [],
                        additional:         this.product_additional,
                    }

                    EventBus.$emit('cartAddProduct', this.product_data);

                    this.$root.toggleButtonDisable(false);
                }
            },
            
            fillSelect (attribute) {
                var options = this.getAttributeOptions(attribute.id),
                    prevOption,
                    index = 0,
                    allowedProducts,
                    i,
                    j;

                this.clearSelect(attribute)
                attribute.options = [];

                if (attribute.prevAttribute) {
                    prevOption = attribute.prevAttribute.options[attribute.prevAttribute.selectedIndex];
                }

                if (options) {
                    for (i = 0; i < options.length; i++) {
                        allowedProducts = [];

                        if (prevOption) {
                            for (j = 0; j < options[i].products.length; j++) {
                                if (prevOption.allowedProducts && prevOption.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                    allowedProducts.push(options[i].products[j]);
                                }
                            }
                        } else {
                            allowedProducts = options[i].products.slice(0);
                        }
    
                        if (allowedProducts.length > 0) {
                            options[i].allowedProducts = allowedProducts;

                            attribute.options[index] = options[i];

                            index++;
                        }
                    }
                }
            },

            getAttributeOptions (attributeId) {
                var this_this = this,
                    options;

                this.config.attributes.forEach(function(attribute, index) {
                    if (attribute.id == attributeId) {
                        options = attribute.options;
                    }
                })

                return options;
            },

            clearSelect: function (attribute) {
                if (! attribute)
                    return;

                if (! attribute.swatch_type || attribute.swatch_type == '' || attribute.swatch_type == 'dropdown') {
                    var element = document.getElementById("attribute_" + attribute.id);

                    if (element) {
                        element.selectedIndex = "0";
                    }
                } else {
                    var elements = document.getElementsByName('super_attribute[' + attribute.id + ']');

                    var this_this = this;

                    elements.forEach(function(element) {
                        element.checked = false;
                    })
                }
            },

            configure (attribute, value) {

                this.simpleProduct = this.getSelectedProductId(attribute, value);

                if (value) {
                    attribute.selectedIndex = this.getSelectedIndex(attribute, value);
                    
                    this.product_additional.attributes[attribute.code] = {
                        option_id: attribute.options[attribute.selectedIndex].id,
                        option_label: attribute.options[attribute.selectedIndex].label,
                        attribute_name: attribute.label,
                    }
                    this.product_additional.super_attribute[attribute.id] = attribute.options[attribute.selectedIndex].id;
                    this.product_additional.selected_configurable_option = this.simpleProduct;

                    if (attribute.nextAttribute) {
                        delete attribute.nextAttribute.selectedIndex;

                        attribute.nextAttribute.disabled = false;

                        this.fillSelect(attribute.nextAttribute);
                        
                        this.resetChildren(attribute.nextAttribute);
                    } else {
                        this.selectedProductId = this.simpleProduct;
                        let opt_product_prices = this.config.variant_prices;
                        
                        if (opt_product_prices[this.simpleProduct].final_price.formated_price) {
                            this.selectedOptionPriceStatus = true;
                            this.selectedOptionPrice = opt_product_prices[this.simpleProduct].final_price.formated_price;
                        }
                        
                        this.disabled_button = false;
                    }
                } else {
                    attribute.selectedIndex = 0;

                    this.resetChildren(attribute);

                    this.clearSelect(attribute.nextAttribute)
                }
            },

            getSelectedProductId (attribute, value) {
                var options = attribute.options,
                    matchedOptions;

                matchedOptions = options.filter(function (option) {
                    return option.id == value;
                });

                if (matchedOptions[0] != undefined && matchedOptions[0].allowedProducts != undefined) {
                    return matchedOptions[0].allowedProducts[0];
                }

                return undefined;
            },

            getSelectedIndex (attribute, value) {
                var selectedIndex = 0;

                attribute.options.forEach(function(option, index) {
                    if (option.id == value) {
                        selectedIndex = index;
                    }
                })

                return selectedIndex;
            },

            resetChildren (attribute) {
                if (attribute.childAttributes) {
                    attribute.childAttributes.forEach(function (set) {
                        set.selectedIndex = 0;
                        set.disabled = true;
                    });
                }
            },

            clearSelect: function (attribute) {
                if (! attribute)
                    return;

                if (! attribute.swatch_type || attribute.swatch_type == '' || attribute.swatch_type == 'dropdown') {
                    var element = document.getElementById("attribute_" + attribute.id);

                    if (element) {
                        element.selectedIndex = "0";
                    }
                } else {
                    var elements = document.getElementsByName('super_attribute[' + attribute.id + ']');

                    var this_this = this;

                    elements.forEach(function(element) {
                        element.checked = false;
                    })
                }
            }
        }        
    }
</script>
