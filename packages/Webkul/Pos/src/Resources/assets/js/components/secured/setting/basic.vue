<template>
    <div>
        <div class="basic-panel" v-if="isUserLogin">

            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div> 

            <div class="pos-setting-list row-grid-5">
                <div class="pos-setting row-layout">
                    <div class="setting-list-group">
                        <div class="label-name">
                            {{ $t('pos_setting.menu_content.basic.entry_select_locale') }}
                        </div>
                        <div class="label-field-control">
                            <select class="label-field" :disabled="pos_offline != 0" name="locale" id="pos_locale" v-model="current_locale" @change="changeLocale($event.target.value)">
                                <option v-for='(locale, index) in locales' :key="index"  :selected="locale.code == current_locale ? 'selected' : '' " :value="locale.code">{{ locale.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pos-setting row-layout">
                    <div class="setting-list-group">
                        <div class="label-name">
                            {{ $t('pos_setting.menu_content.basic.entry_select_currency') }}
                        </div>
                        <div class="label-field-control">
                            <select class="label-field" :disabled="pos_offline != 0" name="currency" id="pos_currency" v-model="current_currency" @change="changeCurrency($event.target.value)">
                                <option v-for='(currency, index) in currencies' :key="index" :selected="currency.code == current_currency ? 'selected' : '' " :value="currency.code">{{ currency.name }}</option>
                            </select>
                        </div>
                    </div>
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
                user_id: 0,
                locales: {},
                currencies: {},
                current_locale: '',
                current_currency: '',
                pos_offline: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.pos_offline = this.$root.$root.offline;

                this.checkUserLogin();
                return this.user_id;
            }
        },
        created() {
            this.locales = window.pos_locales;
            this.currencies = window.pos_currencies;
            
            this.current_locale = window.pos_default_locale;
            this.current_currency = window.pos_default_currency.code;
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            changeLocale(selectedLocale) {
                this.pos_offline = this.$root.$root.offline;

                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                
                    if (this.localObject.pos_cashier.id) {
                        this.$http.post('/api/pos/auth/changeLocale', {
                            user_id: this.localObject.pos_cashier.id,
                            locale_code: selectedLocale
                        })
                        .then((response)  =>  {
                            if ( response.data.status && response.data.route) {
                                
                                window.pos_default_locale = selectedLocale;
                                this.current_locale = selectedLocale;

                                this.localObject.pos_carts[0] = {};
                                this.localObject.pos_current_cart = 0;
                                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.localObject.pos_carts)});
                                EventBus.$emit('deleteLocalForage', 'pos_current_cart');

                                this.localObject.pos_products = {};
                                EventBus.$emit('deleteLocalForage', 'pos_products');

                                this.localObject.pos_leftmenus = [];
                                EventBus.$emit('deleteLocalForage', 'pos_leftmenus');

                                this.localObject.pos_discount = {};
                                EventBus.$emit('deleteLocalForage', 'pos_discount');

                                this.localObject.pos_discounts = [];
                                EventBus.$emit('deleteLocalForage', 'pos_discounts');

                                this.localObject.pos_leftmenus = [];
                                EventBus.$emit('deleteLocalForage', 'pos_leftmenus');

                                this.$toast.success(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });

                                setTimeout(() => {
                                    location.reload();
                                }, 500);
                            } else {
                                this.$toast.warning(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },

            changeCurrency(selectedCurrency) {

                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                
                    if (this.localObject.pos_cashier.id) {
                        this.$http.post('/api/pos/auth/changeCurrency', {
                            user_id: this.localObject.pos_cashier.id,
                            currency_code: selectedCurrency
                        })
                        .then((response)  =>  {
                            if ( response.data.status && response.data.route) {
                                if (response.data.currency_symbol) {
                                    window.pos_currency_symbol = response.data.currency_symbol;
                                    window.pos_currency_code = response.data.currency_code;
                                }
                                $(this.currencies).each((key, value) => {
                                    if (value.code == selectedCurrency) {
                                        window.pos_default_currency = value;
                                    }
                                });
                                this.current_currency = selectedCurrency;
                                
                                this.localObject.pos_carts[0] = {};
                                this.localObject.pos_current_cart = 0;
                                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.localObject.pos_carts)});
                                EventBus.$emit('deleteLocalForage', 'pos_current_cart');

                                this.localObject.pos_products = {};
                                EventBus.$emit('deleteLocalForage', 'pos_products');

                                this.$router.push({name: response.data.route});

                                this.$toast.success(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            } else {
                                this.$toast.warning(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            }
        }
    }
</script>