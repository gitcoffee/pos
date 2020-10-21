<template>
    <div class="pos-discount-list" v-if="isUserLogin">
        <form autocomplete="off" @submit.prevent="drawerAction" method="POST">
            <div class="page-content">
                <div class="form-container">
                    <input type="hidden" name="cart_id" v-model="pos_current_cart">
                    <input type="hidden" name="currency" v-model="currency">

                    <div class="pos-customer-fields">
                        <div class="control-group" :class="[this.errors.has('discount') ? 'has-error' : '']">
                            <label for="discount" v-text="$t('pos_home.pos_cart.entry_available_discount')"></label>
                            <select class="control" name="discount" @change="showDiscount" v-model="discount" v-bind:style="{ 'width': width }">
                                <option value="">{{ $t('pos_home.pos_cart.entry_select_discount') }}</option>
                                <option :value="discount.id" :data-name="discount.offername" v-for="(discount, index) in pos_discounts" :key="index">{{ discount.offername }}</option>
                            </select>
                            <span class="control-error" v-if="this.errors.has('discount')">{{ this.errors.first('discount') }}</span>
                        </div>

                        <div v-if="discount_details_status" class="pos-setting-list">
                            <div class="pos-setting row-layout">
                                <div class="setting-list-name">
                                    <div class="name">
                                        {{ discount_details.offername }}
                                    </div>
                                </div>
                                <div class="setting-list-rate">
                                    <span v-if="discount_details.type == 'percentage'">
                                        {{ $t('pos_setting.menu_content.discount.entry_percentage') }}
                                    </span>
                                    <span v-else >
                                        {{ $t('pos_setting.menu_content.discount.entry_fix') }}
                                    </span>
                                </div>
                                <div class="setting-list-rate">
                                    <span>
                                        {{ discount_details.fvalue }}
                                    </span>
                                    
                                </div>
                                <div class="setting-list-range">
                                    Range {{ discount_details.ffromprice }} to {{ discount_details.ftoprice }}
                                </div>
                            </div>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_home.pos_cart.button_apply_discount')"> {{ $t('pos_home.pos_cart.button_apply_discount') }} </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</template>

<script>
    export default {
        props: ['pos_current_cart', 'pos_carts', 'pos_discounts', 'localObject'],
        data() {
            return {
                pos_offline: 0,
                width: '90%',
                user_id: 0,
                discount: '',
                discount_details_status: false,
                discount_details: {},
                currency: this.$root.current_currency,
            };
        },
        computed: {
            isUserLogin () {
                this.pos_offline = this.$root.$root.offline;

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
            showDiscount(e) {
                var thisthis = this;
                if (thisthis.user_id && e.target.options.selectedIndex > 0) {
                    
                    if (thisthis.pos_offline) {
                        if (Object.keys(thisthis.localObject.pos_discounts).length > 0) {
                            
                            if (thisthis.discount) {
                                $.each(thisthis.localObject.pos_discounts, (key, discount_detail) => {
                                    if ( discount_detail.id === thisthis.discount ) {
                                        thisthis.discount_details = discount_detail;thisthis.discount_details_status = true;
                                    }
                                });
                            } else {
                                thisthis.discount_details = {};
                                thisthis.discount_details_status = false
                            }
                        } else {
                            thisthis.discount_details = {};
                            thisthis.discount_details_status = false;
                        }
                    } else {
                        thisthis.$http.get('/api/pos/auth/getDiscounts', {
                            params: {
                                user_id: thisthis.user_id,
                                discount_id: thisthis.discount
                            }
                        })
                        .then((response)  =>  {
                            if ( response.data.data && response.data.data.length == 1) {
                                if (response.data.data[0].id == thisthis.discount) {
                                    thisthis.discount_details = response.data.data[0];
                                    thisthis.discount_details_status = true;
                                }
                            } else {
                                thisthis.discount_details = {};
                                thisthis.discount_details_status = false;
                            }
                        })
                        .catch(function (error) {});
                    }
                } else {
                    thisthis.discount = '';
                    thisthis.discount_details = {};
                    thisthis.discount_details_status = false;
                }
            },
            formatPrice(value) {
                let val = (value/1).toFixed(2).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            },
            drawerAction() {
                if (this.discount == this.discount_details.id) {
                    this.applyDiscount(this.discount_details);
                }
            },

            applyDiscount(discountDetails) {
                if (discountDetails) {
                    this.localObject.pos_discount = discountDetails;
                    
                    EventBus.$emit('setLocalForage', {'key': 'pos_discount', 'data': JSON.stringify(discountDetails)});

                    this.$toast.success(this.$t('pos_setting.menu_content.discount.success_discount_apply'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                    
                    EventBus.$emit('onCartUpdate');

                    EventBus.$emit('hideCommonModal', 'addDiscountToCart');
                }
            },
        }
    }
</script>