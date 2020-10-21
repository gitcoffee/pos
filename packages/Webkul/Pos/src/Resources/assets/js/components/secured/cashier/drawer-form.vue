<template>
    <div class="pos-discount-form" v-if="isUserLogin">
        <form autocomplete="off" @submit.prevent="drawerAction" method="POST">
            <div class="page-content">
                <div class="form-container">

                    <div class="pos-customer-fields">
                        <div class="control-group" :class="[this.errors.has('amount') ? 'has-error' : '']">
                            <label for="amount" v-text="$t('pos_cashier.menu_content.entry_amount')"></label>
                            <div class="pos-input-group">
                                <span class="pos-input-group-btn">
                                    <div class="span-btn span-btn-sm" :title="currency">{{ currency }}</div>
                                </span>
                                <input type="text" class="control" name="amount" v-model="amount" v-validate="'decimal:2'" v-bind:style="{ 'width': width }" />

                            </div>
                            <span class="control-error" v-if="this.errors.has('amount')">{{ this.errors.first('amount') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('remark') ? 'has-error' : '']">
                            <label for="remark" v-text="$t('pos_cashier.menu_content.entry_remark')"></label>
                            <textarea class="control" name="remark" v-model="remark" v-bind:style="{ 'width': width }" v-validate="'max:250'"></textarea>
                            <span class="control-error" v-if="this.errors.has('remark')">{{ this.errors.first('remark') }}</span>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_cashier.menu_content.button_open_store')"> {{ $t('pos_cashier.menu_content.button_open_store') }} </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['drawer_data', 'localObject'],
        data() {
            return {
                user_id: 0,
                width: '90%',
                amount: 0,
                remark: '',
                drawer_status: 1,
                currency: this.$root.current_currency,
                front_currency_code: window.pos_currency_code
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
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }

                if (this.drawer_data.id) {
                    if (window.pos_currency_code == this.drawer_data.base_currency) {
                        this.amount = this.drawer_data.opening_amount;
                        this.currency = this.drawer_data.base_symbol;
                    } else {
                        this.amount = this.drawer_data.converted_opening_amount;
                        this.currency = window.pos_currency_symbol;
                    }
                }
            },
            drawerAction () {
                var thisthis = this;
                
                thisthis.formAction = '/api/pos/auth/updateDrawer';
                this.$root.toggleButtonDisable(true);
                
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.$http.post(thisthis.formAction, {
                            user_id: thisthis.user_id,
                            amount: thisthis.amount,
                            currency_code: thisthis.front_currency_code,
                            remark: thisthis.remark,
                            status: '1',
                        })
                        .then((response)  =>  {
                            if (!response.data.status) {
                                this.$toast.error(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                                this.$root.toggleButtonDisable(false);
                            } else {
                                this.$toast.success(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                                
                                EventBus.$emit('hideCommonModal', 'addDrawer');
                                this.$root.toggleButtonDisable(false);

                                EventBus.$emit('setLocalForage', {'key': 'pos_cashier', 'data': JSON.stringify(response.data.user_data)});

                                EventBus.$emit('setLocalForage', {'key': 'pos_drawer', 'data': JSON.stringify(response.data.drawer_data)});

                                EventBus.$emit('resetDrawerStatus', response.data.drawer_data);
                            }
                        })
                        .catch(function (error) {});
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            }
        }
    }
</script>