<template>
    <div class="product-list product-grid-5" v-if="pos_product_count">
        <div v-for="(product, index) in pos_products" :key="index" v-if="qtyCheck(product)">
            <div class="product-layout" @click="updateCart(product)">
                <div class="product-thumb">
                    <img v-if="product.base_image.small_image_url" :src="product.base_image.small_image_url"  />
                    <label v-else class="product-placeholder"></label>
                    <span class="product-discount" v-if="product.formated_special_price" :title="$t('pos_home.pos_products.product_discount_tooltip')">
                        <i class="fa fa-star"></i>
                    </span>
                    <span class="product-variations" v-if="product.type != 'simple' && product.type != 'virtual'" :title="$t('pos_home.pos_products.product_variation_tooltip')">
                        <i class="fa fa-plus"></i>
                    </span>
                </div>
                <div class="product-name">
                    {{ product.name }}
                </div>
                <div class="product-price" v-if="product.type != 'configurable' && product.type != 'bundle'">
                    <span :class="{'price': true, 'price-cross': product.formated_special_price}">
                        {{ product.formated_price }}
                    </span>
                    <span v-if="product.formated_special_price" class="special-price">
                        {{ product.formated_special_price }}
                    </span>
                </div>
                <div class="product-price" v-else >
                    <span class="price">
                        {{ product.formated_minimal_price }}
                    </span>
                </div>
            </div>
        </div>

        <div v-if="this.$root.posCommonModal.configurable">
            <pos-common-modal id="configurable" :is-open="this.$root.posCommonModal.configurable">
                <h4 slot="header">{{ $t('pos_home.pos_products.product_variation') }}</h4>
                
                <div slot="body" v-if="product_details.id">
                    <configurable
                        :product="product_details"
                        :product_variations="product_details.variants"
                    ></configurable>
                </div>
            </pos-common-modal>
        </div>
        <div v-if="this.$root.posCommonModal.grouped">
            <pos-common-modal id="grouped" :is-open="this.$root.posCommonModal.grouped">
                <h4 slot="header" v-if="product_details.id">{{ product_details.name }}</h4>
                
                <div slot="body" v-if="product_details.id">
                    <grouped
                        :localObject="localObject"
                        :product="product_details"
                        :grouped_associated="product_details.grouped_associated"
                    ></grouped>
                </div>
            </pos-common-modal>
        </div>
        <div v-if="this.$root.posCommonModal.bundle">
            <pos-common-modal id="bundle" :is-open="this.$root.posCommonModal.bundle">
                <h4 slot="header">{{ $t('pos_home.pos_products.customize_options') }}</h4>
                
                <div slot="body" v-if="product_details.id">
                    <bundle
                        :localObject="localObject"
                        :product="product_details"
                        :bundle_options="product_details.bundle_option_products"
                    ></bundle>
                </div>
            </pos-common-modal>
        </div>
        <div v-if="this.$root.posCommonModal.downloadable">
            <pos-common-modal id="downloadable" :is-open="this.$root.posCommonModal.downloadable">
                <h4 slot="header">{{ $t('pos_home.pos_products.downloadable_options') }}</h4>
                
                <div slot="body" v-if="product_details.id">
                    <downloadable
                        :localObject="localObject"
                        :product="product_details"
                        :downloadable_options="product_details.downloadable_links"
                    ></downloadable>
                </div>
            </pos-common-modal>
        </div>
        <div v-if="this.$root.posCommonModal.booking">
            <pos-common-modal id="booking" :is-open="this.$root.posCommonModal.booking">
                <h4 slot="header">{{ $t('pos_home.pos_products.booking_details') }}</h4>
                
                <div slot="body" v-if="product_details.id">
                    <booking
                        :localObject="localObject"
                        :product="product_details"
                        :booking_options="product_details.booking_options"
                    ></booking>
                </div>
            </pos-common-modal>
        </div>        
    </div>
    <div v-else class="message-alert danger">
        {{ $t("pos_home.pos_products.error.no_product") }}
    </div>
</template>

<script>

    export default {
        props: ['pos_products', 'pos_product_count', 'localObject'],
        data() {
            return {
                product_details: Object,
                posModalId: '',
                showModel: false,
                idModal: '',
            }
        },
        created () {
            this.closeModal();

            EventBus.$on('showVariantModal', (product) => {
                this.product_details = product;
                EventBus.$emit('showCommonModal', 'productVariationModal');
            });
        },
        computed: {
            isModalOpen () {
                this.addClassToBody();

                return this.showModel;
            }
        },
        methods: {
            closeModal (modal_id) {
                this.showModel = false,
                this.idModal = '';
            },

            addClassToBody () {
                var body = document.querySelector("body");
                if(this.showModel) {
                    body.classList.add("pos-modal");
                } else {
                    body.classList.remove("pos-modal");
                }
            },
            
            updateCart(product) {
                if ( product.type == 'simple' || product.type == 'virtual' ) {
                    EventBus.$emit('cartAddProduct', product);
                } else {
                    this.product_details = {};
                    this.product_details = product;
                    EventBus.$emit('showCommonModal', product.type);
                }
            },

            qtyCheck(product) {
                var thisthis = this;
                var product_type = ['simple', 'virtual'];
                let qty = JSON.parse(product.quantity);

                if ( product_type.indexOf(product.type) != -1) {
                    let remaining_quantity = qty[product.id];

                    if ( Object.keys(thisthis.localObject.pos_offline_orders).length > 0 ) {
                        var totalOrderProductQty = 0;
                        
                        $.each(thisthis.localObject.pos_offline_orders, function(key, offlineOrder) {
                            totalOrderProductQty = totalOrderProductQty + thisthis.checkOrderProduct(offlineOrder.offline.items, product);
                        });
                        remaining_quantity = remaining_quantity - totalOrderProductQty;
                    }
                    
                    if (parseInt(remaining_quantity) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    let flag_qty = 0;
                    if ( product.type == 'configurable' || product.type == 'grouped' ) {
                        $.each(qty, (key, product_qty) => {
                            if (product_qty > 0) {
                                flag_qty = 1;
                                
                                return false;
                            }
                        });
                    } else {
                        flag_qty = 1;
                    }

                    if (flag_qty) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return true;
            },

            checkOrderProduct(orderItems, product) {
                var orderProductQty = 0;
                $.each(orderItems, function(key, orderProduct) {
                    if ( parseInt(orderProduct.id) == parseInt(product.id) ) {
                        orderProductQty = orderProductQty + orderProduct.qty_ordered;
                    }
                });
                return orderProductQty;
            }
        }
    }
</script>
