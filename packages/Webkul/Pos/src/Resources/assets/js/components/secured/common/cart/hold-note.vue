<template>
    <div class="pos-hold-note" v-if="isUserLogin">
        <form autocomplete="off" @submit.prevent="holdCartAction" method="POST">
            <div class="page-content">
                <div class="form-container">

                    <div class="pos-customer-fields">
                        <div class="control-group" :class="[this.errors.has('note') ? 'has-error' : '']">
                            <label for="note" class="required" v-text="$t('pos_home.pos_cart.entry_order_note')"></label>
                            
                            <textarea class="control" name="note" v-model="note" v-validate="'required|max:250'" v-bind:style="{ 'width': '100%' }" :placeholder="$t('pos_home.pos_cart.placeholder_note')">
                            </textarea>

                            <span class="control-error" v-if="this.errors.has('note')">{{ this.errors.first('note') }}</span>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_home.pos_cart.button_hold_order')"> {{ $t('pos_home.pos_cart.button_hold_order') }} </button>
                            
                            <button class="btn btn-lg btn-pos-default" type="button" :text="$t('pos_home.pos_cart.button_cancel')" @click="hideHoldNoteModal('holdCart')" > {{ $t('pos_home.pos_cart.button_cancel') }} </button>
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
                width: '90%',
                user_id: 0,
                months: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                current_date: '',
                current_time: '',
                note: '',
                currency: this.$root.current_currency,
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
            },
            holdCartAction() {
                var thisthis = this;
                
                this.$root.toggleButtonDisable(true);
                
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.current_date = this.getFormattedDate();
                        this.current_time = this.getFormattedTime();

                        var hold_order = {
                            note: this.note,
                            date: this.current_date,
                            time: this.current_time
                        };

                        EventBus.$emit('applyHoldCart', hold_order);
                        
                        EventBus.$emit('hideCommonModal', 'holdCart');
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            },
            hideHoldNoteModal(modalId) {
                EventBus.$emit('hideNoteModal', modalId);
            },
            getFormattedDate() {
                var d = new Date();
                return this.months[d.getMonth()] + ' ' + d.getDate() + ' ' + d.getFullYear();
            },
            getFormattedTime() {
                var t = new Date();
                return t.getHours() + ':' + t.getMinutes() + ':' + t.getSeconds();
            }
        }
    }
</script>