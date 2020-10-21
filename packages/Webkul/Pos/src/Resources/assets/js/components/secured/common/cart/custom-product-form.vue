<template>
    <div class="pos-discount-form" v-if="isUserLogin">

        <form autocomplete="off" @submit.prevent="customAction" method="POST">
            <div class="page-content">
                <div class="form-container">

                    <div class="pos-discount-fields">
                        <div class="control-group" :class="[this.errors.has('name') ? 'has-error' : '']">
                            <label for="name" class="required" v-text="$t('pos_home.navtop.entry_product_name')"></label>
                            <input type="text" class="control" name="name" v-validate="'required'" v-bind:style="{ 'width': width }" v-model="name">
                            <span class="control-error" v-if="this.errors.has('name')">{{ this.errors.first('name') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('sku') ? 'has-error' : '']" v-if="config_sku_status == 1 && !pos_offline">
                            <label for="sku" class="required" v-text="$t('pos_home.navtop.entry_sku')"></label>
                            <input type="text" v-validate="'required|alpha_dash'" class="control" name="sku" v-bind:style="{ 'width': width }" v-model="sku" @keyup="validateSku">
                            <span class="control-error" v-if="this.errors.has('sku')">{{ this.errors.first('sku') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('price') ? 'has-error' : '']">
                            <label for="price" class="required" v-text="$t('pos_home.navtop.entry_price')"></label>
                            <input type="text" class="control" name="price" v-validate="'required|decimal:2'" v-bind:style="{ 'width': width }" v-model="price">
                            <span class="control-error" v-if="this.errors.has('price')">{{ this.errors.first('price') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('quantity') ? 'has-error' : '']">
                            <label for="quantity" class="required" v-text="$t('pos_home.navtop.entry_quantity')"></label>
                            <input type="number" class="control" name="quantity" v-validate="'required|numeric'" v-bind:style="{ 'width': width }" v-model="quantity">
                            <span class="control-error" v-if="this.errors.has('quantity')">{{ this.errors.first('quantity') }}</span>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-dark" :disabled="btn_disabled" type="submit" :text="$t('pos_home.navtop.button_cart')"> {{ $t('pos_home.navtop.button_cart') }} </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['pos_current_cart', 'pos_carts', 'localObject'],
        data() {
            return {
                width: '90%',
                name: '',
                price: 0,
                quantity: 1,
                user_id: 0,
                success_custom_product: this.$t('pos_home.navtop.success_add_to_cart'),
                config_sku_status: window.allow_sku,
                sku: '',
                validate_sku: false,
                pos_offline: 0,
                btn_disabled: false,
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
                this.pos_offline = this.$root.$root.offline;

                if (!this.pos_offline && this.config_sku_status == 1 && !this.validate_sku) {
                    // this.btn_disabled = true;
                    this.btn_disabled = false;
                }

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            validateSku() {
                var thisthis = this;
                this.btn_disabled = false;
                if (thisthis.pos_offline) {
                    this.config_sku_status = 0;
                } else {
                    if (thisthis.sku.length > 0) {
                        thisthis.$http.get('/api/pos/auth/validateSku', {
                            params: {
                                name: thisthis.name,
                                sku: thisthis.sku
                            }
                        })
                        .then((response)  =>  {
                            // this.btn_disabled = false;
                        })
                        .catch(function (error) {
                            if (error.response.data.errors) {
                                var formErrors = error.response.data.errors;
                                if (formErrors.name) {
                                    thisthis.$toast.error(formErrors.name[0], {
                                        position: 'top-right',
                                        duration: 2000,
                                    });
                                }
                                if (formErrors.sku) {
                                    thisthis.$toast.error(formErrors.sku[0], {
                                        position: 'top-right',
                                        duration: 2000,
                                    });
                                    thisthis.btn_disabled = true;
                                }
                            }
                        });
                    } else {
                        if (!thisthis.pos_offline && thisthis.config_sku_status == 1 && !thisthis.validate_sku) {
                            // thisthis.btn_disabled = true;
                        } else {
                            // thisthis.btn_disabled = true;
                            thisthis.config_sku_status = 0;
                        }
                    }
                }
            },
            customAction () {
                var thisthis = this;
                
                var cart_products = {};
                this.$root.toggleButtonDisable(true);

                this.$validator.validateAll().then(result => {
                    if (result) {
                        if ( thisthis.config_sku_status ) {
                            this.validateSku();
                        }
                        
                        if ( (parseInt(this.quantity) > 0) && (parseFloat(this.price) >= 0) ) {
                            if (thisthis.pos_carts[thisthis.pos_current_cart]) {
                                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, product) {
                                    product.active = false;
                                    cart_products[key] = product;
                                });
                                this.$set(thisthis.pos_carts, thisthis.pos_current_cart, cart_products);

                                var custom_product = {
                                    active: true,
                                    id: 0,
                                    sku: thisthis.sku,
                                    type: 'simple',
                                    weight: 0,
                                    total_weight: 0,
                                    name: thisthis.name,
                                    tax_category_id: 0,
                                    tax_percent: 0,
                                    tax_amount: 0,
                                    base_tax_amount: 0,
                                    qty_ordered: thisthis.quantity,
                                    price: parseFloat(thisthis.price).toFixed(2),
                                    base_price: parseFloat(thisthis.price).toFixed(2),
                                    
                                    f_price: window.pos_currency_symbol + parseFloat(thisthis.price).toFixed(2),

                                    special_price: 'NaN',
                                    base_special_price: 'NaN',

                                    base_total: parseFloat(thisthis.price * thisthis.quantity).toFixed(2),

                                    total: parseFloat(thisthis.price * thisthis.quantity).toFixed(2),
                                    
                                    f_total: window.pos_currency_symbol + parseFloat(thisthis.price * thisthis.quantity).toFixed(2),
                                }

                                let index = Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length;

                                this.$set(thisthis.pos_carts[thisthis.pos_current_cart], index, custom_product);

                                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});

                                this.$toast.success(this.success_custom_product, {
                                    position: 'top-right',
                                    duration: 2000,
                                });

                                this.$root.hideCommonModal('addCustomProduct');
                                this.$root.toggleButtonDisable(false);
                            } else {
                                this.$toast.error(this.$t('pos_home.pos_custom_product.choose_product'), {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                                this.$root.hideCommonModal('addCustomProduct');
                                this.$root.toggleButtonDisable(false);
                            }
                        } else {
                            this.$toast.error(this.$t('pos_home.pos_custom_product.correct_qty_price'), {
                                position: 'top-right',
                                duration: 2000,
                            });
                            
                            this.$root.toggleButtonDisable(false);
                        }
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            }
        }
    }
</script>