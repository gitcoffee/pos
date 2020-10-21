
<template>
    <div class="pos-cashier-main">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li v-for="(menu, index) in pos_cashier_menus" :key="index" :class="{ 'pos-nav': true, 'active': index == pos_current_menu }" :label="'menu_count_' + index"  @click="activeSettingMenu(index)">
                    <router-link class="nav-link" :to="{ path: menu.route }">
                        {{ menu.name }}
                    </router-link>
                </li>
            </ul>
        </div>
        

        <div class="pos-nav-content">
            <router-view
                :localObject="localObject"
            ></router-view>
        </div>
    </div>
    
</template>

<script>

    export default {

        props: ['localObject'],
        data() {
            return {
                pos_cashier_menus: [
                    {
                        'code': 'close_counter',
                        'name': this.$t('pos_cashier.top_menu.close_counter'),
                        'route': '/pos/cashier/closecounter'
                    },
                    {
                        'code': 'today_cash',
                        'name': this.$t('pos_cashier.top_menu.today_cash'),
                        'route': '/pos/cashier/todaycash'
                    },
                    {
                        'code': 'sales_history',
                        'name': this.$t('pos_cashier.top_menu.sales_history'),
                        'route': '/pos/cashier/saleshistory'
                    }
                ],
                pos_current_menu: 0,
            };
        }, 
        methods: {
            activeSettingMenu(menu_index) {
                this.pos_current_menu = menu_index;
            }
        }
    }
</script>
