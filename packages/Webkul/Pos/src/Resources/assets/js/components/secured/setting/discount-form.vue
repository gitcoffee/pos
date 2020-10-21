<template>
    <div class="pos-discount-form" v-if="isUserLogin">

        <form autocomplete="off" @submit.prevent="discountAction" method="POST">
            <div class="page-content">
                <div class="form-container">
                    <input type="hidden" name="discount_id" v-model="discount_id">

                    <div class="pos-discount-fields">
                        <div class="control-group" :class="[this.errors.has('offername') ? 'has-error' : '']">
                            <label for="offername" class="required" v-text="$t('pos_setting.menu_content.discount.entry_offer_name')"></label>
                            <input type="text" class="control" name="offername" v-validate="'required'" v-bind:style="{ 'width': width }" v-model="offername">
                            <span class="control-error" v-if="this.errors.has('offername')">{{ this.errors.first('offername') }}</span>
                        </div>

                        <div class="pos-group-fields">
                            <div class="pull-left">
                                <div class="control-group" :class="[this.errors.has('fromprice') ? 'has-error' : '']">
                                    <label for="fromprice" class="required" v-text="$t('pos_setting.menu_content.discount.entry_from_price')"></label>
                                    <div class="pos-input-group">
                                        <span class="pos-input-group-btn">
                                            <div class="span-btn span-btn-sm" :title="currency">{{ currency }}</div>
                                        </span>
                                        <input type="text" class="control" name="fromprice" v-validate="'required|decimal:4'" v-model="fromprice">
                                    </div>
                                    <span class="control-error" v-if="this.errors.has('fromprice')">{{ this.errors.first('fromprice') }}</span>
                                </div>
                            </div>

                            <div class="pull-right">
                                <div class="control-group" :class="[this.errors.has('fromprice') ? 'has-error' : '']">
                                    <label for="toprice" class="required" v-text="$t('pos_setting.menu_content.discount.entry_to_price')"></label>
                                    <div class="pos-input-group">
                                        <span class="pos-input-group-btn">
                                            <div class="span-btn span-btn-sm" :title="currency">{{ currency }}</div>
                                        </span>
                                        <input type="text" class="control" name="toprice" v-validate="'required|decimal:4'" v-model="toprice">
                                    </div>
                                    
                                    <span class="control-error" v-if="this.errors.has('toprice')">{{ this.errors.first('toprice') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pos-group-fields">
                            <div class="pull-left">
                                <div class="control-group">
                                    <label for="type" v-text="$t('pos_setting.menu_content.discount.entry_discount_type')"></label>

                                    <select name="type" class="control" v-model="type">
                                        <option value="percentage" :selected="type == 'percentage' ? 'selected' : '' ">{{ $t('pos_setting.menu_content.discount.entry_percentage') }}</option>
                                        <option value="fix" :selected="type == 'fix' ? 'selected' : ''">{{ $t('pos_setting.menu_content.discount.entry_fix') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pull-right">
                                <div class="control-group" :class="[this.errors.has('value') ? 'has-error' : '']">
                                    <label for="value" class="required" v-text="$t('pos_setting.menu_content.discount.entry_discount_value')"></label>
                                    <div class="pos-input-group">
                                        <span class="pos-input-group-btn">
                                            <div class="span-btn span-btn-sm" :title="currency">{{ currency }}</div>
                                        </span>
                                        <input type="text" class="control" name="value" v-validate="'required|decimal:4'" v-model="value">
                                    </div>
                                    
                                    <span class="control-error" v-if="this.errors.has('value')">{{ this.errors.first('value') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_setting.menu_content.discount.button_done')"> {{ $t('pos_setting.menu_content.discount.button_done') }} </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['discount_id', 'discount_data', 'localObject'],
        data() {
            return {
                width: '90%',
                offername: '',
                fromprice: 0,
                toprice: 0,
                type: 'percentage',
                value: 0,
                user_id: 0,
                formAction: '',
                currency: window.pos_currency_symbol,
                pos_offline: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        mounted() {
            this.fillDiscountData();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            discountAction () {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    window.flashMessages = [{'type': 'alert-error', 'message': "Warning: You can not perform this action in offline mode!"}];
                    this.$root.addFlashMessages();
                } else {
                    var thisthis = this;
                    if (!thisthis.discount_id) {
                        thisthis.formAction = '/api/pos/auth/addDiscount';
                    } else {
                        thisthis.formAction = '/api/pos/auth/editDiscount';
                    }
                    this.$root.toggleButtonDisable(true);
                    
                    this.$validator.validateAll().then(result => {
                        if (result) {
                            this.$http.post(thisthis.formAction, {
                                    discount_id: thisthis.discount_id,
                                    offername: thisthis.offername,
                                    fromprice: thisthis.fromprice,
                                    toprice: thisthis.toprice,
                                    type: thisthis.type,
                                    value: thisthis.value,
                                    user_id: thisthis.user_id
                            })
                            .then((response)  =>  {
                                if (!response.data.status) {
                                    window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                                    this.$root.addFlashMessages();
                                    this.$root.toggleButtonDisable(false);
                                } else {
                                    window.flashMessages = [{'type': 'alert-success', 'message': response.data.message}];
                                    this.$root.addFlashMessages();
                                    this.$root.hideCommonModal('addDiscount');
                                    this.$root.toggleButtonDisable(false);

                                    this.$router.push({name: response.data.route});
                                    EventBus.$emit('getUserDiscounts');
                                }
                            })
                            .catch(function (error) {});
                        } else {
                            this.$root.toggleButtonDisable(false);
                        }
                    });
                }
            },
            fillDiscountData() {
                if (this.discount_id) {
                    this.offername = this.discount_data.offername;
                    this.fromprice = parseFloat(this.discount_data.converted_fromprice).toFixed(4);
                    this.toprice = parseFloat(this.discount_data.converted_toprice).toFixed(4);
                    this.type = this.discount_data.type;

                    if ( this.type == "percentage") {
                        this.value = parseFloat(this.discount_data.value).toFixed(4);
                    } else {
                        this.value = parseFloat(this.discount_data.converted_value).toFixed(4);
                    }
                }
            }
        }
    }
</script>