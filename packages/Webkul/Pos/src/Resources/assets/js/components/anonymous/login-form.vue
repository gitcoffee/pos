<template>
    
    <div class="pos-container user-login" v-bind:style="{'height': height}">
        
        <div class="pos-login-content">
            
            <div class="top-head-content">
                {{ configData.top_heading }}
            </div>

            <div class="login-form-content">
                <div class="pos-logo-container" v-if="pos_logo_result">
                    <img :src="pos_logo" class="pos_logo" />
                </div>
                <form @submit.prevent="login" method="POST">
                
                    <div class="pos-login-text">{{ configData.top_sub_heading }}</div>
    
                    <div class="pos-login-fields">
                        <div class="control-group" :class="[errors.has('username') ? 'has-error' : '']">
                            <label for="username" class="required">{{ $t("pos_login.username") }}</label>
                            <input type="text" class="control" name="username" v-model="username" v-validate="'required'">
                            <span class="control-error" v-if="errors.has('username')">{{ errors.first('username') }}</span>
                        </div>

                        <div class="control-group" :class="[errors.has('password') ? 'has-error' : '']">
                            <label for="password" class="required">{{ $t("pos_login.password") }}</label>
                            <input type="password" class="control" name="password" v-validate="'required|min:6'" v-model="password">
                            <span class="control-error" v-if="errors.has('password')">{{ errors.first('password') }}</span>
                        </div>

                        <div class="control-group">
                            <label class="checkbox"  for="input-remember-password">
                                <input type="checkbox" name="remember_password" id="input-remember-password" value="1" v-model="remember">
                                {{ $t("pos_login.remember_password") }}
                            </label>
                        </div>

                        <div class="pos-action text-center">
                            <button class="btn btn-pos-primary btn-lg" type="submit">{{ $t("pos_login.button_title") }}</button>

                            <div class="pos-forgot-password-link text-center">
                                
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>

            <div class="footer-note">
                <div class="bottom-head-content">
                    {{ configData.footer_content }}
                </div>

                <div v-if="configData.footer_note" class="footer-note-link">
                    {{ configData.footer_note }}
                    <a v-if="configData.footer_link" :href="configData.footer_link" target="_blank">{{ configData.footer_link_text }}</a>
                </div>
            </div>

        </div>
    </div>
</template>

<script>

    export default {
        
        data() {
            return {
                username: '',
                password: '',
                remember: 1,
                pos_carts: [],
                pos_current_cart: 0,
                has_error: false,
                configData: Object,
                height: $(window).height() + 'px',
                login_panel_height: $(window).height() + 'px',
                pos_logo: window.pos_logo_image,
                pos_logo_result: window.pos_logo_result,
                pos_saas: window.pos_saas,
            };
        },
        mounted: function() {
            this.getPosConfig();
        }, 
        methods: {
            getPosConfig: function () {
                this.$http.get('/api/pos/config')
                .then((response)  =>  {
                    this.configData = response.data.config;
                }, (error)  =>  {
                    this.has_error = true;
                })
            },

            login: function() { 
                var self = this;               
                self.$validator.validateAll().then(result => {
                    if (result) {
                        self.$root.$setItem('pos_drawer', {});

                        this.$http.post('/api/pos/auth/login', {
                                'username': self.username,
                                'password': self.password,
                                'pos_saas': self.pos_saas,
                                'remember': self.remember
                        })
                        .then((response)  =>  {
                            if (!response.data.status) {
                                this.$toast.error(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            } else {
                                EventBus.$emit('clearLocalForage');

                                var products = [];
                                
                                this.$root.$setItem('pos_product_categories', JSON.stringify(products));
                                this.$root.$setItem('pos_products', JSON.stringify(products));

                                this.pos_carts[0] = {};
                                
                                this.$root.$setItem('pos_carts', JSON.stringify(this.pos_carts));

                                this.$root.$setItem('pos_current_cart', this.pos_current_cart);
                                
                                this.$root.$setItem('pos_cashier', JSON.stringify(response.data.user_data));

                                this.$root.$setItem('pos_drawer', JSON.stringify(response.data.drawer_data));

                                this.$router.push({name: 'pos_home'});

                                this.$toast.success(response.data.message, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            }
                        })
                        .catch(function (error) {});
                    }
                });
            },

            toggleButtonDisable (value) {
                var buttons = document.getElementsByTagName("button");

                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].disabled = value;
                }
            },
        },
    }

</script>
