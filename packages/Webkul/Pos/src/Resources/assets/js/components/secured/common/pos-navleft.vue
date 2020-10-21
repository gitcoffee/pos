<template>
    <div class="pos-navbar-left" v-if="isUserLogin">
        <ul class="pos-menubar" >
            <li v-for='menu in pos_menus' :key="menu.id" :class="{'pos-menu-item': true }">
                <router-link class="nav-link" :to="{ path: menu.route }">
                    <span :class="'icon ' + menu.iconClass"></span>
                    <p>{{ menu.name }}</p>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>

    export default {
        props: ['localObject'],
        data() {
            return {
                pos_menus: [],
                user_id: 0,
            };
        },
        computed: {
            isUserLogin () {
                this.checkPosUserLogin();
                return this.user_id;
            }
        },
        mounted: function() {
            this.getPosNavMenus();
        }, 
        methods: {
            checkPosUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },
            getPosNavMenus() {
                if (Object.keys(this.localObject.pos_leftmenus).length != 0) {
                    this.pos_menus = this.localObject.pos_leftmenus;
                } else {
                    this.$http.get('/api/pos/navmenus')
                    .then((response)  =>  {
                        if (response.data.pos_menus) {
                            this.pos_menus = response.data.pos_menus;

                            EventBus.$emit('setLocalForage', {'key': 'pos_leftmenus', 'data': JSON.stringify(response.data.pos_menus)});
                        }
                    })
                    .catch(function (error) {});
                }
            }
        }
    }
</script>
