<template>
    <div>
        <div class="profile-panel">

            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div>
            <div class="pos-setting-list row-grid-2" v-bind:style="{ 'width': containerWidth }">
                <div class="pos-setting row-layout">
                    <h3>{{ $t('pos_setting.menu_content.profile.entry_user_profile') }}</h3>
                    <hr>
                    
                    <div class="pos-profile-form" v-if="isUserLogin">

                        <form autocomplete="off" @submit.prevent="saveUser" method="POST">
                            <div class="page-content">
                                <div class="form-container">
                                    <input type="hidden" name="user_id" v-model="user_id">

                                    <div class="pos-profile-fields">
                                        <div class="control-group" :class="[this.errors.has('firstname') ? 'has-error' : '']">
                                            <label for="firstname" class="required" v-text="$t('pos_setting.menu_content.profile.entry_firstname')"></label>
                                            <input type="text" class="control" name="firstname" v-validate="'required|alpha_spaces'" v-bind:style="{ 'width': width }" v-model="firstname">
                                            <span class="control-error" v-if="this.errors.has('firstname')">{{ this.errors.first('firstname') }}</span>
                                        </div>

                                        <div class="control-group" :class="[this.errors.has('lastname') ? 'has-error' : '']">
                                            <label for="lastname" class="required" v-text="$t('pos_setting.menu_content.profile.entry_lastname')"></label>
                                            <input type="text" class="control" name="lastname" v-validate="'required|alpha_spaces'" v-bind:style="{ 'width': width }" v-model="lastname">
                                            <span class="control-error" v-if="this.errors.has('lastname')">{{ this.errors.first('lastname') }}</span>
                                        </div>

                                        <div class="control-group" :class="[this.errors.has('username') ? 'has-error' : '']">
                                            <label for="username" class="required" v-text="$t('pos_setting.menu_content.profile.entry_username')"></label>
                                            <input type="text" class="control" name="username" v-validate="'required|alpha_spaces'" :disabled="'disabled'" v-bind:style="{ 'width': width }" v-model="username">
                                            <span class="control-error" v-if="this.errors.has('username')">{{ this.errors.first('username') }}</span>
                                        </div>

                                        <div class="control-group" :class="[this.errors.has('email') ? 'has-error' : '']">
                                            <label for="email" class="required" v-text="$t('pos_setting.menu_content.profile.entry_email')"></label>
                                            <input type="email" class="control" name="email" v-validate="'required|email'" v-bind:style="{ 'width': width }" v-model="email">
                                            <span class="control-error" v-if="this.errors.has('email')">{{ this.errors.first('email') }}</span>
                                        </div>

                                        <div class="control-group" :class="[this.errors.has('previous_password') ? 'has-error' : '']">
                                            <label for="previous_password" class="required" v-text="$t('pos_setting.menu_content.profile.entry_pre_password')"></label>
                                            <input type="password" class="control" name="previous_password" v-validate="'required|min:6'" v-bind:style="{ 'width': width }" v-model="previous_password">
                                            <span class="control-error" v-if="this.errors.has('previous_password')">{{ this.errors.first('previous_password') }}</span>
                                        </div>

                                        <div class="control-group" :class="[this.errors.has('password') ? 'has-error' : '']">
                                            <label for="password" class="required" v-text="$t('pos_setting.menu_content.profile.entry_new_password')"></label>
                                            <input type="password" class="control" name="password" v-validate="'required|min:6'" v-bind:style="{ 'width': width }" v-model="password">
                                            <span class="control-error" v-if="this.errors.has('password')">{{ this.errors.first('password') }}</span>
                                        </div>

                                        <div class="control-group" :class="[this.errors.has('confirm_password') ? 'has-error' : '']">
                                            <label for="confirm_password" class="required" v-text="$t('pos_setting.menu_content.profile.entry_confirm_password')"></label>
                                            <input type="password" class="control" name="confirm_password" v-validate="'required|confirmed:password|min:6'" v-bind:style="{ 'width': width }" v-model="confirm_password">
                                            <span class="control-error" v-if="this.errors.has('confirm_password')">{{ this.errors.first('confirm_password') }}</span>
                                        </div>

                                        <div class="pos-action text-left">
                                            <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_setting.menu_content.profile.button_done')" > {{ $t('pos_setting.menu_content.profile.button_done') }} </button>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
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
                containerWidth: '50%',
                width: '90%',
                user_details: {},
                firstname: '',
                lastname: '',
                username: '',
                email: '',
                previous_password: '',
                password: '',
                confirm_password: '',
                user_id: 0,
                pos_offline: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.checkUserLogin();
                this.pos_offline = this.$root.$root.offline;
                return this.user_id;
            }
        },
        mounted() {
            this.fillProfileData();
        },
        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                    this.user_details = this.localObject.pos_cashier;
                }
            },
            saveUser () {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    var thisthis = this;
                    
                    this.$root.toggleButtonDisable(true);
                    
                    this.$validator.validateAll().then(result => {
                        if (result) {
                            this.$http.post('/api/pos/auth/updateUser', {
                                    user_id: thisthis.user_id,
                                    firstname: thisthis.firstname,
                                    lastname: thisthis.lastname,
                                    email: thisthis.email,
                                    previous_password: thisthis.previous_password,
                                    password: thisthis.password,
                                    confirm_password: thisthis.confirm_password
                            })
                            .then((response)  =>  {
                                
                                if (!response.data.status) {
                                    window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                                    this.$root.addFlashMessages();
                                    this.$root.toggleButtonDisable(false);
                                } else {
                                    window.flashMessages = [{'type': 'alert-success', 'message': response.data.message}];
                                    this.$root.addFlashMessages();
                                    this.$root.toggleButtonDisable(false);
                                }
                            })
                            .catch(function (error) {});
                        } else {
                            this.$root.toggleButtonDisable(false);
                        }
                    });
                }
            },
            fillProfileData() {
                if (this.user_id) {
                    this.firstname = this.user_details.firstname;
                    this.lastname = this.user_details.lastname;
                    this.username = this.user_details.username;
                    this.email = this.user_details.email;
                }
            }
        }
    }
</script>