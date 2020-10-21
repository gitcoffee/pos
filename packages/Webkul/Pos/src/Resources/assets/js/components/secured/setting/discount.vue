<template>
    <div>
        <div class="discount-panel" v-if="isUserLogin">

            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div> 
            
            <div class="pos-setting-add" v-if="!pos_offline">
                <div class="setting-add-icon" @click="showDiscountModal('addDiscount')">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="setting-add-text">
                    {{ $t('pos_setting.menu_content.discount.add_discount') }}
                </div>
            </div>
            <div class="pos-setting-list row-grid-5" v-if="pos_total_discount">
                <div class="pos-setting row-layout" v-for="(discount, index) in pos_discounts" :key="index">
                    <div class="setting-list-name">
                        <div class="name">
                            {{ discount.offername }}
                        </div>
                        <div class="setting-list-action" v-if="!pos_offline">
                            <span class="discount-edit" :disabled="pos_offline != 0" @click="editDiscountModal(discount)">
                                <i class="fa fa-pencil"></i>
                            </span>
                            <span class="discount-remove" :disabled="pos_offline != 0" @click="removeDiscount(discount.id)">
                                <i class="fa fa-times"></i>
                            </span>
                        </div>
                    </div>
                    <div class="setting-list-rate">
                        <span v-if="discount.type == 'percentage'">
                            {{ $t('pos_setting.menu_content.discount.entry_percentage') }}
                        </span>
                        <span v-else >
                            {{ $t('pos_setting.menu_content.discount.entry_fix') }}
                        </span>
                    </div>
                    <div class="setting-list-rate">
                        <span>
                            {{ discount.fvalue }}
                        </span>
                    </div>
                    <div class="setting-list-range">
                        {{ $t('pos_setting.menu_content.discount.text_range') }} {{ discount.ffromprice }} {{ $t('pos_setting.menu_content.discount.text_to') }} {{ discount.ftoprice }}
                    </div>
                </div>
            </div>
            <div v-else class="message-alert danger">
                {{ $t("pos_setting.error.no_discount") }}
            </div>

            <div v-if="this.$root.posCommonModal.addDiscount">
                <pos-common-modal id="addDiscount" :is-open="this.$root.posCommonModal.addDiscount">
                    <h4 slot="header" v-if="!discount_id">{{ $t('pos_setting.menu_content.discount.add_discount') }}</h4>
                    <h4 slot="header" v-else >{{ $t('pos_setting.menu_content.discount.edit_discount') }}</h4>
                    
                    <div slot="body">
                        <discount-form
                            :discount_id='discount_id'
                            :discount_data='discount_data'
                            :localObject='localObject'
                        ></discount-form>
                    </div>
                </pos-common-modal>
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
                discount_id: 0,
                discount_data: {},
                pos_discounts: [],
                pos_total_discount: 0,
                pos_offline: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();
                return this.user_id;
            }
        },
        created() {
            this.getDiscounts();
            
            EventBus.$on('getUserDiscounts', () => {
                this.getDiscounts();
            });
        },
        methods: {
            checkUserLogin() {
                this.pos_offline = this.$root.$root.offline;

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            showDiscountModal(modalId) {
                this.discount_id = 0;
                this.discount_data = {};
                EventBus.$emit('showCommonModal', modalId);
            },
            editDiscountModal(discount) {
                this.discount_id = discount.id;
                this.discount_data = discount;
                EventBus.$emit('showCommonModal', 'addDiscount');
            },
            removeDiscount(discount_id) {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    if (this.localObject.pos_cashier.id) {
                        this.$http.post('/api/pos/auth/deleteDiscount', {
                            user_id: this.localObject.pos_cashier.id,
                            discount_id: discount_id
                        })
                        .then((response)  =>  {
                            if (!response.data.status) {
                                this.$toast.warning(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            } else {
                                this.$toast.success(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                                this.getDiscounts();
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },
            getDiscounts() {
                this.pos_offline = this.$root.$root.offline;
                
                if (this.localObject.pos_cashier.id) {
                    if (this.pos_offline) {
                        if (Object.keys(this.localObject.pos_discounts).length > 0) {
                            this.pos_total_discount = Object.keys(this.localObject.pos_discounts).length;
                            this.pos_discounts = this.localObject.pos_discounts;
                            
                        } else {
                            this.pos_total_discount = 0;
                            this.pos_discounts = [];
                        }
                    } else {
                        this.$http.get('/api/pos/auth/getDiscounts', {
                            params: {
                                user_id: this.localObject.pos_cashier.id
                            }
                        })
                        .then((response)  =>  {
                            if ( response.data.data && response.data.data.length > 0) {
                                this.pos_total_discount = response.data.data.length;
                                this.pos_discounts = response.data.data;

                                EventBus.$emit('setLocalForage', {'key': 'pos_discounts', 'data': JSON.stringify(this.pos_discounts)});
                            } else {
                                this.pos_discounts = [];
                                this.pos_total_discount = 0;
                                EventBus.$emit('setLocalForage', {'key': 'pos_discounts', 'data': JSON.stringify(this.pos_discounts)});
                            }
                        });
                    }
                }
            }
        }
    }
</script>