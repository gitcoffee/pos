<template>
    <div>
        <div class="taxes-panel">
            <div v-show="showTaxPanel" class="pos-setting-add">
                <div class="setting-add-icon" @click="showTaxModal('addTax')">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="setting-add-text">
                    {{ $t('pos_setting.menu_content.tax.add_tax') }}
                </div>
            </div>
            <div class="pos-setting-list row-grid-5" v-if="pos_total_taxes">
                <div class="pos-setting row-layout" v-for="(tax, index) in pos_taxes" :key="index">
                    <div class="setting-list-name">
                        <div class="name">
                            {{ tax.name }}
                        </div>
                        <div class="setting-list-action hide">
                            <span class="tax-edit" @click="editDiscountModal(tax)">
                                <i class="fa fa-pencil"></i>
                            </span>
                            <span class="tax-remove" @click="removeDiscount(tax.id)">
                                <i class="fa fa-times"></i>
                            </span>
                        </div>
                    </div>
                    <div class="setting-list-rate">
                        {{ tax.code }}
                    </div>
                    <div class="setting-list-range hide">
                        
                    </div>
                </div>
            </div>
            <div v-else class="message-alert danger">
                {{ $t("pos_setting.error.no_taxes") }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                showTaxPanel: false,
                tax_id: 0,
                tax_data: {},
                pos_taxes: [],
                pos_total_taxes: 0
            };
        },
        created() {
            this.getTaxes();
            
            EventBus.$on('getUserTaxes', () => {
                this.getTaxes();
            });
        },
        methods: {
            showTaxModal(modalId) {
                this.tax_id = 0;
                this.tax_data = {};
                EventBus.$emit('showCommonModal', modalId);
            },
            editTaxModal(tax) {
                this.tax_id = tax.id;
                this.tax_data = tax;
                EventBus.$emit('showCommonModal', 'addTax');
            },
            removeTax(tax_id) {
                if (this.localObject.pos_cashier.id) {
                    this.$http.post('/api/pos/auth/deleteTax', {
                        user_id: this.localObject.pos_cashier.id,
                        tax_id: tax_id
                    })
                    .then((response)  =>  {
                        if (!response.data.status) {
                            window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                            this.$root.addFlashMessages();
                        } else {
                            window.flashMessages = [{'type': 'alert-success', 'message': response.data.message}];
                            this.$root.addFlashMessages();
                            this.getTaxes();
                        }
                    })
                    .catch(function (error) {});
                }
            },
            getTaxes() {
                if (this.localObject.pos_cashier.id) {
                    this.$http.get('/api/pos/getTaxes')
                    .then((response)  =>  {
                        if ( response.data.data && response.data.data.length > 0) {
                            this.pos_total_taxes = response.data.data.length;
                            this.pos_taxes = response.data.data;
                        } else {
                            this.pos_taxes = [];
                            this.pos_total_taxes = 0;
                        }
                    })
                    .catch(function (error) {});
                }
            }
        }
    }
</script>