
<template>
    <div class="pos-restaurant-main" v-if="pos_setting_menus">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li v-for="(menu, index) in pos_setting_menus" :key="index" :class="{ 'pos-nav': true }" :label="'menu_count_' + index">
                    <router-link class="nav-link" :to="{ path: menu.route }">
                        {{ menu.name }}
                    </router-link>
                </li>
            </ul>
        </div>

        <div class="pos-nav-content" v-bind:style="{'height': container_height}">
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
                config_restaurant: window.config_restaurant,
                warning_restaurant_access: this.$t('pos_restaurant.error.no_access'),
                pos_setting_menus: [
                    {
                        'code': 'table_list',
                        'name': this.$t('pos_restaurant.top_menu.table_list'),
                        'route': '/pos/restaurant/table-list'
                    },  {
                        'code': 'table_booked',
                        'name': this.$t('pos_restaurant.top_menu.table_booked'),
                        'route': '/pos/restaurant/table-booked'
                    }
                ],
                container_height: ($(window).height() - 110) + 'px',
            };
        },

        mounted: function() {
            this.checkCreateTable();
        },

        methods: {
            checkCreateTable() {
                var self = this;
                if (self.localObject.pos_cashier.id) {
                    if ( self.config_restaurant.status == 1 ) {
                        if ( self.config_restaurant.agent_table_status == 1 ) {
                            self.pos_setting_menus.push({
                                'code': 'table_create',
                                'name': this.$t('pos_restaurant.top_menu.table_create'),
                                'route': '/pos/restaurant/table-create'
                            });
                        }
                    } else {
                        self.$router.push({name: 'pos_home'});
                        self.$toast.error(this.warning_restaurant_access, {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                }
            }
        }
    }
</script>
