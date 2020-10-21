<template>
    <div class="download-product-container" v-if="downloadable_links">
        <div class="price-container">
            <label>{{ $t('pos_home.pos_products.total_amount') }}</label>

            <div>{{ currency_options + converted_price }}</div>
        </div>
        <form id="downloadable-app" autocomplete="off" @submit.prevent="validateOptions" method="POST">

            <ul class="download-sample-list" v-if="downloadable_samples">
                <li class="heading">
                    <div>{{ $t('pos_home.pos_products.download_samples') }}</div>
                </li>
                <li v-for="(sample, index) in downloadable_samples" :key="index">
                    <a :href="sample.file_url" target="_blank" :alt="sample.name">{{ sample.name }}</a>
                </li>
            </ul>

            <ul class="download-sample-list">
                <li class="heading">
                    <div>{{ $t('pos_home.pos_products.download_links') }}</div>
                </li>
                
                <div class="control-group custom-form mb10" :class="[errors.has('links[]') ? 'has-error' : '']">
                    <li v-for="(download_link, link_index) in downloadable_links" :key="link_index">
                        <input
                            type="checkbox"
                            :name="'links[]'"
                            :id="download_link.id"
                            v-validate="'required'"
                            :data-vv-as="'&quot;Links&quot;'"
                            :value="download_link.id" @change="updatePrice($event)" />

                        {{ download_link.name + ' + ' + download_link.formated_price }} 

                        <div v-if="download_link.sample_type == 'url'">
                            <a
                                target="_blank"
                                class="remove-decoration"
                                :href="download_link.sample_url">
                                {{ $t('pos_home.pos_products.sample') }}
                            </a>
                        </div>

                        <div v-if="download_link.sample_type == 'file'">
                            <a
                                target="_blank"
                                class="remove-decoration"
                                :href="download_link.sample_file_url">
                                {{ $t('pos_home.pos_products.sample') }}
                            </a>
                        </div>
                    </li>
    
                    <span class="control-error" v-if="errors.has('links[]')">
                        {{ errors.first('links[]') }}
                    </span>
                </div>
            </ul>

            <div class="pos-buttons text-center">
                <button type="submit" class="pos-button button-md button-dark">{{ $t('pos_home.pos_products.btn_add_to_cart') }}</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        inject: ['$validator'],

        props: ['localObject', 'product', 'downloadable_options'],
        
        data() {
            return {
                downloadable_samples: JSON.parse(this.downloadable_options).samples,
                downloadable_links: JSON.parse(this.downloadable_options).links,
                currency_options: window.pos_currency_symbol,
                product_data: {},
                product_additional: {
                    product_id: this.product.id,
                    quantity: 1,
                    links: {},
                    attributes: {},
                },
                base_price: this.product.special_price ? this.product.special_price : this.product.price,
                converted_price: this.product.special_price ? this.product.converted_special_price : this.product.converted_price,
            }
        },

        methods: {
            updatePrice(event) {
                var thisthis = this;

                    if (event.target.checked == true) {
                        thisthis.base_price = parseFloat(thisthis.base_price) + parseFloat(this.downloadable_links[event.target.value].price);

                        thisthis.converted_price = parseFloat(thisthis.converted_price) + parseFloat(this.downloadable_links[event.target.value].converted_price);
                    } else {
                        thisthis.base_price = parseFloat(thisthis.base_price) - parseFloat(this.downloadable_links[event.target.value].price);

                        thisthis.converted_price = parseFloat(thisthis.converted_price) - parseFloat(this.downloadable_links[event.target.value].converted_price);
                    }
            },

            validateOptions() {
                var thisthis = this;
                this.$root.toggleButtonDisable(true);
                this.$validator.validateAll().then(result => {
                    if (result) {
                            var download_links  = {};
                            var links           = {};
                            var attributes      = {};
                                attributes[thisthis.product.id] = {
                                    attribute_name: 'Downloads',
                                    option_id:      thisthis.product.id,
                                    option_label:   null,
                                    download_links: {}
                                };
                            
                            $.each(this.$validator.fields.items, function(key, field) {
                                var option_label    = '';
                                
                                if ( field.el.checked === true) {
                                    links[field.el.value]           = field.el.value;
                                    download_links[field.el.value]  = thisthis.downloadable_links[field.el.id];

                                    if ( attributes[thisthis.product.id].option_label ) {
                                        attributes[thisthis.product.id].option_label    = attributes[thisthis.product.id].option_label + ', ' + thisthis.downloadable_links[field.el.id].name;
                                    } else {
                                        attributes[thisthis.product.id].option_label    = thisthis.downloadable_links[field.el.id].name;
                                    }
                                }
                            });
                            attributes[thisthis.product.id].download_links  = download_links;
                            thisthis.product_additional.links               = links;
                            thisthis.product_additional.attributes          = attributes;
                            
                            thisthis.prepareForCart(thisthis.product, thisthis.product_additional);

                            this.$root.toggleButtonDisable(false);
                            EventBus.$emit('hideCommonModal', thisthis.product.type);
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            },

            prepareForCart: function(product, additional) {
                var self                = this;
                var pos_qty             = {};
                var product_id          = product.id;
                pos_qty[product_id]     = 1;

                self.product_data   = {
                    id:                 product_id,
                    quantity:           1,
                    pos_qty:            JSON.stringify(pos_qty),
                    sku:                product.sku,
                    type:               product.type,
                    name:               product.name,
                    coupon_code:        '',
                    weight:             0,
                    total_weight:       0,
                    base_total_weight:  0,
                    price:              parseFloat(this.converted_price),
                    formated_price:     this.currency_options + parseFloat(this.converted_price),
                    base_price:         parseFloat(this.base_price),
                    custom_price:       0,
                    
                    total:              (1 * parseFloat(this.converted_price)),
                    base_total:         (1 * parseFloat(this.base_price)),
                    
                    tax_category_id:    product.tax_category_id,
                    tax_percent:        product.tax_percent,
                    tax_amount:         (((1 * parseFloat(this.converted_price)) * product.tax_percent) / 100 ),
                    base_tax_amount:    (((1 * parseFloat(this.base_price)) * product.tax_percent) / 100 ),

                    discount_percent:   0,
                    discount_amount:    0,
                    base_discount_amount: 0,

                    cg_base_price:      product.price,
                    cg_converted_price: product.converted_price,
                    cg_formated_price:  product.formated_price,
                    customerGroupPrices: product.customerGroupPrices,

                    additional:         additional,
                };
                
                EventBus.$emit('cartAddProduct', self.product_data);
            }
        }        
    }
</script>