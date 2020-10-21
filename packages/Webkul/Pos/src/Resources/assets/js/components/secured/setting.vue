
<template>
    <div class="pos-setting-main">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li v-for="(menu, index) in pos_setting_menus" :key="index" :class="{ 'pos-nav': true }" :label="'menu_count_' + index">
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
                pos_setting_menus: [
                    {
                        'code': 'discount',
                        'name': this.$t('pos_setting.top_menu.discount'),
                        'route': '/pos/setting/discount'
                    },
                    {
                        'code': 'basic',
                        'name': this.$t('pos_setting.top_menu.basic_setting'),
                        'route': '/pos/setting/basic'
                    },
                    {
                        'code': 'profile',
                        'name': this.$t('pos_setting.top_menu.profile_setting'),
                        'route': '/pos/setting/profile'
                    }
                ]
            };
        }
    }

</script>
