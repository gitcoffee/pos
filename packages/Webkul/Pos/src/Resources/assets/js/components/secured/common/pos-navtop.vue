<template>
     <div class="pos-navbar-top" v-if="isUserLogin">
        <div class="pos-navbar-top-left">
            <div class="pos-navbar-top-heading">
                <router-link to="/pos/home" :title="$t('pos_home.navtop.heading')" >{{ $t("pos_home.navtop.heading") }}</router-link>
            </div>
            
            <navtop-search
                :disableStatus="disable_status"
            ></navtop-search>

            <div class="pos-nav-top-product">
                <span v-if="disable_status" class="custom_img" :title="$t('pos_home.navtop.add_custom_product_tooltip')" @click="showCustomModal('addCustomProduct')"></span>
                <span v-if="!disable_status" class="custom_img" :title="$t('pos_home.navtop.add_custom_product_tooltip')"></span>
            </div>
        </div>
    
        <div class="pos-navbar-top-right">
            <div class="nav-reload" :title="$t('pos_home.navtop.reset_data_tooltip')" @click="reloadPosData">
                <i class="fa fa-sync"></i>
            </div>

            <div class="nav-wifi online_label">
                <i class="fa fa-wifi" :title="$t('pos_home.navtop.offline_mode_tooltip')" @click="offlineManage(true)"></i>
            </div>

            <div class="nav-top-user" v-if="pos_user.id">
                <div class="user-image">
                    <img v-if="pos_user.image" :src="pos_user.img_path" :alt="pos_user.firstname + ' ' + pos_user.lastname" :title="pos_user.firstname + ' ' + pos_user.lastname" />
                    <i v-else class="fa fa-user-circle"></i>
                </div>

                <div class="user-details">
                    <span v-text="pos_user.firstname +' '+ pos_user.lastname"></span>
                    <p>{{ $t("pos_home.navtop.cashier") }}</p>
                </div>

                <div class="user-logout">
                    <div :title="$t('pos_home.navtop.logout_tooltip')"  @click="userLogout(pos_user.id)"> <i class="fa fa-sign-out"></i> </div>
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
                pos_user: {},
                user_id: 0,
                disable_status: false,
            };
        },
        computed: {
            isUserLogin () {
                this.checkPosUserLogin();
                return this.user_id;
            }
        },
        created() {
            this.checkPosUserLogin();
        },
        methods: {
            showCustomModal(modalId) {
                EventBus.$emit('showCommonModal', modalId);
            },
            checkPosUserLogin() {
                if (this.$route.name == 'pos_home') {
                    this.disable_status = true;
                } else {
                    this.disable_status = false;
                }
                if (this.localObject.pos_cashier.id) {
                    this.pos_user = this.localObject.pos_cashier;
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            userLogout(user_id) {
                this.$http.post('/api/pos/auth/destroy', {
                    user_id: user_id
                })
                .then((response)  =>  {
                    if (response.data.route) {
                        this.$router.push({name: response.data.route});
                    }
                })
                .catch(function (error) {});
            },
            reloadPosData() {
                this.localObject.pos_carts[0] = {};
                this.localObject.pos_current_cart = 0;
                
                this.$root.$setItem('pos_carts', JSON.stringify(this.localObject.pos_carts));
                this.$root.$setItem('pos_current_cart', this.localObject.pos_current_cart);
                
                this.localObject.pos_products = [];
                this.$root.$setItem('pos_products', JSON.stringify(this.localObject.pos_products));

                this.localObject.pos_categories = {};
                this.$root.$setItem('pos_categories', JSON.stringify(this.localObject.pos_categories));

                this.localObject.pos_product_categories = {};
                this.$root.$setItem('pos_product_categories', JSON.stringify(this.localObject.pos_product_categories));

                this.localObject.pos_orders = [];
                this.$root.$setItem('pos_sale_history', JSON.stringify(this.localObject.pos_orders));

                this.localObject.pos_customers = [];
                this.$root.$setItem('pos_customers', JSON.stringify(this.localObject.pos_customers));

                setTimeout(() => {
                    location.reload();
                }, 1000);
            },

            offlineManage(onLineStatus) {
                EventBus.$emit('checkWifi', onLineStatus);
            }
        }
    }
</script>
