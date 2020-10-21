
<template>
    <div>
        <pos-navtop
            :localObject="localObjectData"
        ></pos-navtop>

        <pos-navleft
            :localObject="localObjectData"
        ></pos-navleft>

        <div class="pos-content-container" v-if="localObjectData.pos_cashier.id && localObjectData.pos_carts[0]">
            <router-view
                :localObject="localObjectData"
            ></router-view>
        </div>
    </div>
</template>

<script>

    export default {
        
        data() {
            return {
                localObjectData: {
                    pos_categories: {},
                    pos_product_categories: [],
                    pos_leftmenus: [],
                    pos_cashier: {},
                    pos_drawer: {},
                    pos_products: [],
                    pos_orders: [],
                    pos_today_sales: [],
                    pos_sale_history: [],
                    pos_offline_orders: [],
                    pos_carts: [],
                    pos_discount: {},
                    pos_holds: [],
                    pos_cart_customer: [],
                    pos_customers: [],
                    pos_offline_customers: [],
                    pos_customer_groups: [],
                    pos_current_cart: 0,
                    pos_lowstock_products: [],
                    pos_requested_products: [],
                    pos_discounts: [],
                    pos_banks: [],
                    pos_restaurant_tables: [],
                    pos_restaurant_bookings: [],
                    pos_booking_carts: [],
                    pos_current_booking: {},
                }
            };
        },
        created() {
            Object.keys(this.localObjectData).forEach((key) => {
                this.getKeyLocalForage(key);
            });

            EventBus.$on('setLocalForage', (records) => {
                this.setKeyLocalForage(records.key, records.data);
            });

            EventBus.$on('getLocalForage', (key) => {
                this.getKeyLocalForage(key);
            });

            EventBus.$on('deleteLocalForage', (key) => {
                this.deleteLocalForage(key);
            });

            EventBus.$on('clearLocalForage', () => {
                this.clearAllLocalForage();
            });
        },
        methods: {
            setKeyLocalForage(key, data) {
                this.$setItem(key, data);
            },
            
            getKeyLocalForage(key) {
                this.$getItem(key, (err, value) => {
                    if (value && value !== undefined && value != null) {
                        if (key == 'pos_categories') {
                            this.$set(this.localObjectData, 'pos_categories', JSON.parse(value));
                        }
                        if (key == 'pos_product_categories') {
                            this.$set(this.localObjectData, 'pos_product_categories', JSON.parse(value));
                        }
                        if (key == 'pos_leftmenus') {
                            this.$set(this.localObjectData, 'pos_leftmenus', JSON.parse(value));
                        }
                        if (key == 'pos_cashier') {
                            this.$set(this.localObjectData, 'pos_cashier', JSON.parse(value));
                        }
                        if (key == 'pos_drawer') {
                            this.$set(this.localObjectData, 'pos_drawer', JSON.parse(value));
                        }
                        if (key == 'pos_products') {
                            this.$set(this.localObjectData, 'pos_products', JSON.parse(value));
                        }
                        if (key == 'pos_orders') {
                            this.$set(this.localObjectData, 'pos_orders', JSON.parse(value));
                        }
                        if (key == 'pos_today_sales') {
                            this.$set(this.localObjectData, 'pos_today_sales', JSON.parse(value));
                        }
                        if (key == 'pos_sale_history') {
                            this.$set(this.localObjectData, 'pos_sale_history', JSON.parse(value));
                        }
                        if (key == 'pos_offline_orders') {
                            this.$set(this.localObjectData, 'pos_offline_orders', JSON.parse(value));
                        }
                        if (key == 'pos_carts') {
                            this.$set(this.localObjectData, 'pos_carts', JSON.parse(value));
                        }
                        if (key == 'pos_discount') {
                            this.$set(this.localObjectData, 'pos_discount', JSON.parse(value));
                        }
                        if (key == 'pos_holds') {
                            this.$set(this.localObjectData, 'pos_holds', JSON.parse(value));
                        }
                        if (key == 'pos_cart_customer') {
                            this.$set(this.localObjectData, 'pos_cart_customer', JSON.parse(value));
                        }
                        if (key == 'pos_customers') {
                            this.$set(this.localObjectData, 'pos_customers', JSON.parse(value));
                        }
                        if (key == 'pos_offline_customers') {
                            this.$set(this.localObjectData, 'pos_offline_customers', JSON.parse(value));
                        }
                        if (key == 'pos_customer_groups') {
                            this.$set(this.localObjectData, 'pos_customer_groups', JSON.parse(value));
                        }
                        if (key == 'pos_current_cart') {
                            this.$set(this.localObjectData, 'pos_current_cart', value);
                        }
                        if (key == 'pos_lowstock_products') {
                            this.$set(this.localObjectData, 'pos_lowstock_products', JSON.parse(value));
                        }
                        if (key == 'pos_requested_products') {
                            this.$set(this.localObjectData, 'pos_requested_products', JSON.parse(value));
                        }
                        if (key == 'pos_discounts') {
                            this.$set(this.localObjectData, 'pos_discounts', JSON.parse(value));
                        }
                        if (key == 'pos_banks') {
                            this.$set(this.localObjectData, 'pos_banks', JSON.parse(value));
                        }
                        if (key == 'pos_restaurant_tables') {
                            this.$set(this.localObjectData, 'pos_restaurant_tables', JSON.parse(value));
                        }
                        if (key == 'pos_restaurant_bookings') {
                            this.$set(this.localObjectData, 'pos_restaurant_bookings', JSON.parse(value));
                        }
                        if (key == 'pos_booking_carts') {
                            this.$set(this.localObjectData, 'pos_booking_carts', JSON.parse(value));
                        }
                        if (key == 'pos_current_booking') {
                            this.$set(this.localObjectData, 'pos_current_booking', JSON.parse(value));
                        }
                    }
                });
            },

            deleteLocalForage (key) {
                this.$removeItem(key);

                if (key != 'pos_current_cart') {                    
                    this.localObjectData[key] = [];
                } else {
                    this.localObjectData.pos_current_cart = 0;
                    this.setKeyLocalForage('pos_current_cart', this.localObjectData.pos_current_cart);
                }
            },

            clearAllLocalForage() {
                this.localObjectData.pos_categories = {};
                this.localObjectData.pos_product_categories = [];
                this.localObjectData.pos_leftmenus = [];
                this.localObjectData.pos_cashier = {};
                this.localObjectData.pos_drawer = {};
                this.localObjectData.pos_products = [];
                this.localObjectData.pos_orders = [];
                this.localObjectData.pos_today_sales = [];
                this.localObjectData.pos_sale_history = [];
                this.localObjectData.pos_carts = [];
                this.localObjectData.pos_offline_orders = [];
                this.localObjectData.pos_discount = {};
                this.localObjectData.pos_holds = [];
                this.localObjectData.pos_cart_customer = [];
                this.localObjectData.pos_customers = [];
                this.localObjectData.pos_offline_customers = [];
                this.localObjectData.pos_customer_groups = [];
                this.localObjectData.pos_current_cart = 0;
                this.localObjectData.pos_lowstock_products = [];
                this.localObjectData.pos_requested_products = [];
                this.localObjectData.pos_discounts = [];
                this.localObjectData.pos_banks = [];
                this.localObjectData.pos_restaurant_tables = [];
                this.localObjectData.pos_restaurant_bookings = [];
                this.localObjectData.pos_booking_carts = [];
                this.localObjectData.pos_current_booking = {};
                
                Object.keys(this.localObjectData).forEach((key) => {
                    this.deleteLocalForage(key);
                });
            },
        }
    }

</script>
