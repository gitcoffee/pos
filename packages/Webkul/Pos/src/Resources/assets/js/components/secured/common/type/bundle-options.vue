<template>
    <div class="bundle-option-item">
        
        <div class="control-group custom-form mb10" :class="[this.errors.has('bundle_options['+option.product_bundle_option_id+'][]') ? 'has-error' : '']">
            
            <label :class="[option.is_required ? 'required' : '']">{{ option.label }}</label>

            <div v-if="option.type == 'select'">
                <select class="control styled-select" :name="'bundle_options[' + option.product_bundle_option_id + '][]'" v-model="selected_product" v-validate="option.is_required ? 'required' : ''" :data-vv-as="'&quot;' + option.label + '&quot;'" :id="option.product_bundle_option_id">
                    <option value="">{{ $t('pos_home.pos_products.choose_selection') }}</option>
                    <option v-for="(product, index2) in option.option_products" :value="product.id" v-bind:key="index2">
                        <span v-if="product.special_price">
                            {{ product.name + ' + ' + product.formated_special_price }}
                        </span>
                        <span v-else >
                            {{ product.name + ' + ' + product.formated_price }}
                        </span>
                    </option>
                </select>
            </div>

            <div v-if="option.type == 'radio'">                
                <div class="radio" v-if="! option.is_required">
                    <input
                        type="radio"
                        :name="'bundle_options[' + option.product_bundle_option_id + '][]'"
                        :id="option.product_bundle_option_id"
                        v-model="selected_product"
                        value="0" />

                    {{ $t('pos_home.pos_products.none') }}
                </div>

                <div class="radio" v-for="(product, index2) in option.option_products" v-bind:key="index2">
                    <input
                        type="radio"
                        :name="'bundle_options[' + option.product_bundle_option_id + '][]'"
                        :id="option.product_bundle_option_id"
                        v-model="selected_product"
                        v-validate="option.is_required ? 'required' : ''"
                        :data-vv-as="'&quot;' + option.label + '&quot;'"
                        :value="product.id" />

                    {{ product.name }}

                    <span class="price">
                        + {{ product.special_price ? product.formated_special_price : product.formated_price }}
                    </span>
                </div>
            </div>
            
            <div v-if="option.type == 'checkbox'">
                <div class="checkbox" v-for="(product, index2) in option.option_products" v-bind:key="index2">
                    <input type="checkbox" :name="'bundle_options[' + option.product_bundle_option_id + '][]'" :value="product.id" v-model="selected_product" v-validate="option.is_required ? 'required' : ''" :data-vv-as="'&quot;' + option.label + '&quot;'" :id="option.product_bundle_option_id">

                    {{ product.name }}

                    <span class="price">
                        + {{ product.special_price ? product.formated_special_price : product.formated_price }} x {{ product.qty }}
                    </span>
                </div>
            </div>

            <div v-if="option.type == 'multiselect'">
                <select class="control styled-select" :name="'bundle_options[' + option.product_bundle_option_id + '][]'" v-model="selected_product" v-validate="option.is_required ? 'required' : ''" :data-vv-as="'&quot;' + option.label + '&quot;'" multiple :id="option.product_bundle_option_id">
                    <option v-for="(product, index2) in option.option_products" :value="product.id" v-bind:key="index2">
                        <span v-if="product.special_price">
                            {{ product.name + ' + ' + product.formated_special_price }} x {{ product.qty }}
                        </span>
                        <span v-else >
                            {{ product.name + ' + ' + product.formated_price }} x {{ product.qty }}
                        </span>
                    </option>
                </select>
            </div>
            
            <span class="control-error" v-if="errors.has('bundle_options[' + option.product_bundle_option_id + '][]')">
                {{ errors.first('bundle_options[' + option.product_bundle_option_id + '][]') }}
            </span>
        </div>

        <div class="qty" v-if="option.type == 'select' || option.type == 'radio'">
            <quantity-panel
                :control-name="option.product_bundle_option_id"
                :field-required="option.is_required ? '1' : ''"
                :validations="parseInt(selected_product) ? 'required|numeric|min_value:1' : ''"
                :quantity="product_qty"
                @onQtyUpdated="qtyUpdated($event)">
            </quantity-panel>
        </div>
    </div>
</template>

<script>
    export default {
        inject: ['$validator'],

        props: ['index', 'option'],
        
        data: function() {
            return {
                selected_product: (this.option.type == 'checkbox' || this.option.type == 'multiselect')  ? [] : null,

                qty_validations: ''
            }
        },

        computed: {
            product_qty: function() {
                var self = this;
                self.qty = 0;

                $.each(self.option.option_products, function(key, product) {
                    if (self.selected_product == product.id)
                        self.qty =  self.option.option_products[key].qty;
                });

                return self.qty;
            }
        },

        watch: {
            selected_product: function (value) {
                this.qty_validations = this.selected_product ? 'required|numeric|min_value:1' : '';

                this.$emit('onProductSelected', value)
            }
        },

        created: function() {
            for (var key1 in this.option.option_products) {
                if (! this.option.option_products[key1].is_default) {
                    continue;
                }
                if (this.option.type == 'checkbox' || this.option.type == 'multiselect') {
                    this.selected_product.push(this.option.option_products[key1].id)
                } else {
                    this.selected_product = this.option.option_products[key1].id
                }
            }
        },

        methods: {
            qtyUpdated: function(product_obj) {
                if (! this.option.option_products[this.selected_product])
                    return;

                this.option.option_products[this.selected_product].qty = product_obj.qty;
            }
        }
    }
</script>