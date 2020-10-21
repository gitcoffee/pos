<template>
    <div class="pos-discount-form" v-if="isUserLogin">
        <form autocomplete="off" @submit.prevent="customerAction" method="POST">
            <div class="page-content">
                <div class="form-container">
                    <input type="hidden" name="customer_email" v-model="customer_email">

                    <div class="pos-customer-fields">
                        <div class="control-group" :class="[this.errors.has('first_name') ? 'has-error' : '']">
                            <label for="first_name" class="required" v-text="$t('pos_customer.menu_content.entry_firstname')"></label>
                            <input type="text" class="control" name="first_name" v-model="first_name" v-validate="'required|alpha_spaces'" v-bind:style="{ 'width': width }" >
                            <span class="control-error" v-if="this.errors.has('first_name')">{{ this.errors.first('first_name') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('last_name') ? 'has-error' : '']">
                            <label for="last_name" class="required" v-text="$t('pos_customer.menu_content.entry_lastname')"></label>
                            <input type="text" class="control" name="last_name" v-model="last_name" v-validate="'required|alpha_spaces'" v-bind:style="{ 'width': width }" >
                            <span class="control-error" v-if="this.errors.has('last_name')">{{ this.errors.first('last_name') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('email') ? 'has-error' : '']">
                            <label for="email" class="required" v-text="$t('pos_customer.menu_content.entry_email')"></label>
                            <input type="email" class="control" name="email" v-model="email" v-validate="'required|email'" v-bind:style="{ 'width': width }">
                            <span class="control-error" v-if="this.errors.has('email')">{{ this.errors.first('email') }}</span>
                        </div>

                        <div class="control-group" :class="[this.errors.has('phone') ? 'has-error' : '']">
                            <label for="phone" v-text="$t('pos_customer.menu_content.entry_phone')"></label>
                            <input type="text" class="control" name="phone" v-model="phone" v-bind:style="{ 'width': width }">
                            <span class="control-error" v-if="this.errors.has('phone')">{{ this.errors.first('phone') }}</span>
                        </div>
                        
                        <div class="control-group">
                            <label for="gender" v-text="$t('pos_customer.menu_content.entry_gender')"></label>

                            <select name="gender" class="control" v-model="gender" v-bind:style="{ 'width': width }">
                                <option value="Male" :selected="gender == 'Male' ? 'selected' : '' ">{{ $t('pos_customer.menu_content.entry_male') }}</option>
                                <option value="Female" :selected="gender == 'Female' ? 'selected' : ''">{{ $t('pos_customer.menu_content.entry_female') }}</option>
                            </select>
                        </div>

                        <div class="control-group">
                            <label for="date_of_birth" v-text="$t('pos_customer.menu_content.entry_dob')"></label>
                            <input type="date" class="control" name="date_of_birth" v-model="date_of_birth" v-bind:style="{ 'width': width }" >
                            <span class="control-error" v-if="this.errors.has('date_of_birth')">{{ this.errors.first('date_of_birth') }}</span>
                        </div>

                        <div class="control-group">
                            <label for="customer_group_id" v-text="$t('pos_customer.menu_content.entry_customer_group')"></label>

                            <select name="customer_group_id" class="control" v-model="customer_group_id" v-bind:style="{ 'width': width }">
                                <option :value="group.id" v-for="(group, index) in customer_groups" :key="index"> {{ group.name }} </option>
                            </select>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_customer.menu_content.button_save')"> {{ $t('pos_customer.menu_content.button_save') }} </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['customer_email', 'customer_data', 'localObject'],
        data() {
            return {
                width: '90%',
                user_id: 0,
                first_name: '',
                last_name: '',
                phone: '',
                email: '',
                gender: 'Male',
                date_of_birth: '',
                customer_group_id: 0,
                formAction: '',
                offline_customers_records: [],
                offline_record: {},
                customer_groups: [],
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();

                return this.user_id;
            }
        },
        mounted() {
            this.fillCustomerData();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
                if ( Object.keys(this.localObject.pos_customer_groups).length > 0 ) {
                    this.customer_groups = this.localObject.pos_customer_groups;
                }
            },
            customerAction () {
                var thisthis = this;
                if (!thisthis.customer_email) {
                    thisthis.formAction = '/api/pos/auth/addCustomer';
                } else {
                    thisthis.formAction = '/api/pos/auth/editCustomer';
                }
                this.$root.toggleButtonDisable(true);
                
                this.$validator.validateAll().then(result => {
                    if (result) {
                        if (this.$root.offline) {
                            this.offline_record = {
                                id: 0,
                                channel_id: window.default_channel.id,
                                user_id: thisthis.user_id,
                                first_name: thisthis.first_name,
                                last_name: thisthis.last_name,
                                name: thisthis.first_name + ' ' + thisthis.last_name,
                                email: thisthis.email,
                                phone: thisthis.phone,
                                gender: thisthis.gender,
                                date_of_birth: thisthis.date_of_birth,
                                customer_group_id: thisthis.customer_group_id,
                                status: 1,
                                is_verified: 1,
                                subscribed_to_news_letter: 0,
                                token: '',
                                addresses: {}
                            };

                            this.offline_customers_records = [];
                            this.offline_customers_records = this.checkOfflineCustomer(this.localObject.pos_offline_customers, this.offline_record);
                            
                            EventBus.$emit('setLocalForage', {'key': 'pos_offline_customers', 'data': JSON.stringify(this.offline_customers_records)});


                            this.$toast.success(this.$t('pos_customer.menu_content.success_customer_added'), {
                                position: 'top-right',
                                duration: 2000,
                            });
                            
                            this.$root.hideCommonModal('addCustomer');
                            this.$root.toggleButtonDisable(false);
                            
                            EventBus.$emit('getAllCustomers');
                            EventBus.$emit('removeCartCustomer');

                            this.$router.push({name: 'pos_customer'});
                        } else {
                            this.$http.post(thisthis.formAction, {
                                    customer_email: thisthis.customer_email,
                                    user_id: thisthis.user_id,
                                    first_name: thisthis.first_name,
                                    last_name: thisthis.last_name,
                                    phone: thisthis.phone,
                                    email: thisthis.email,
                                    gender: thisthis.gender,
                                    date_of_birth: thisthis.date_of_birth,
                                    customer_group_id: thisthis.customer_group_id,
                            })
                            .then((response)  =>  {
                                if (!response.data.status) {
                                    window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                                    this.$root.addFlashMessages();
                                    this.$root.toggleButtonDisable(false);
                                } else {
                                    window.flashMessages = [{'type': 'alert-success', 'message': response.data.message}];
                                    this.$root.addFlashMessages();
                                    this.$root.hideCommonModal('addCustomer');
                                    this.$root.toggleButtonDisable(false);
                                    
                                    EventBus.$emit('getAllCustomers');

                                    this.$router.push({name: response.data.route});
                                }
                            })
                            .catch(function (error) {});
                        }
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            },
            fillCustomerData() {
                if (this.customer_email) {
                    this.first_name = this.customer_data.first_name;
                    this.last_name = this.customer_data.last_name;
                    this.phone = this.customer_data.phone;
                    this.email = this.customer_data.email;
                    this.gender = this.customer_data.gender;
                    this.date_of_birth = this.customer_data.date_of_birth;
                    this.customer_group_id = this.customer_data.customer_group_id;
                }
            },
            checkOfflineCustomer(arr, offline_record) {
                const { length } = arr;
                const id = length + 1;
                const found = arr.some(el => el.email === offline_record.email);
                if (!found) arr.push(offline_record);
                return arr;
            }
        }
    }
</script>