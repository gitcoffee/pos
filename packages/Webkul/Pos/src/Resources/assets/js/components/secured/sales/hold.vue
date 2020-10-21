<template>
    <div class="sale-hold-panel">
        <div class="sale-hold-list">
            <div class="pos-product-container" v-if="isUserLogin">
                <div class="product-list row-grid-3" v-if="hold_count != 0">
                    <div class="product-layout" v-for="(hold_cart, index) in pos_holds" :key="index">
                        <div class="order-detail" v-if="hold_cart.hold_status">
                            <div class="order_note">
                                <i class="fa fa-info-circle"></i>
                                {{ $t('pos_sales.menu_content.hold_sales.text_note') }}
                            </div>
                            <div class="order_date_time">
                                {{ hold_cart.hold_data.time }} - {{ hold_cart.hold_data.date }}
                            </div>
                        </div>

                        <div class="order_hold_note">
                            {{ hold_cart.hold_data.note }}
                        </div>
                        <div class="order-product-container">
                            <div class="item-heading">
                                {{ $t('pos_sales.menu_content.hold_sales.text_hold_product_details') }}
                            </div>
                            <div class="item-list" v-if="isObject(hold_cart)">
                                <div class="product_info" v-for="product in hold_cart">
                                    <div v-if="product.name" >
                                        <div class="product_name">
                                            {{ product.name }}
                                            <span class="product-attributes" v-if="product.additional" >
                                                <span v-for="attributes in product.additional.attributes">
                                                    <i> <b>{{ attributes.attribute_name }}</b>: {{ attributes.option_label }}, </i>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="product_qty">
                                            x {{ product.qty_ordered }}
                                        </div>
                                        <div class="product_price" >
                                            {{ product.f_price }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hold_footer">
                            <div class="pull-left">
                                <button class="btn btn-md btn-pos-primary" type="button" :text="$t('pos_sales.menu_content.hold_sales.button_cart')" @click="addToCartBack(index)">
                                    <i class="fa fa-cart-plus"></i>
                                    {{ $t('pos_sales.menu_content.hold_sales.button_cart') }} 
                                </button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-md btn-pos-default" type="button" :text="$t('pos_sales.menu_content.hold_sales.button_delete')" @click="removeHoldCart(index)">
                                    <i class="fa fa-trash"></i>
                                    {{ $t('pos_sales.menu_content.hold_sales.button_delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="message-alert danger">
                    {{ $t('pos_sales.menu_content.hold_sales.no_hold_order') }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                pos_holds: [],
                hold_count: 0,
                user_id: 0,
                success_remove_hold: this.$t('pos_sales.menu_content.hold_sales.success_remove_hold'),
            };
        },
        created() {
            this.loadHoldCarts();
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            loadHoldCarts() {
                var hold_length = Object.keys(this.localObject.pos_holds).length;
                if ( hold_length > 0 ) {
                    this.hold_count = hold_length;
                    this.pos_holds = this.localObject.pos_holds;
                }
            },
            isObject(cart_data) {
                return typeof cart_data == "object"
            },
            removeHoldCart(holdCartIndex) {
                if (holdCartIndex != 'null') {
                    
                    if (Object.keys(this.localObject.pos_holds).length > 0) {
                        this.pos_holds = this.localObject.pos_holds;

                        this.pos_holds.splice(holdCartIndex, 1);

                        this.hold_count = Object.keys(this.pos_holds).length;
                        
                        EventBus.$emit('setLocalForage', {'key': 'pos_holds', 'data': JSON.stringify(this.pos_holds)});
                        
                        window.flashMessages = [{'type': 'alert-success', 'message': this.success_remove_hold }];

                        this.$root.addFlashMessages();
                    }
                }
            },
            addToCartBack(holdCartIndex) {
                if (holdCartIndex != 'null') {
                    EventBus.$emit('addBackToCart', holdCartIndex);
                }
            }
        }
    }
</script>