
<template>
    <div class="pos-sales-main">
        <div class="pos-nav-container">
            <ul class="pos-nav-lists">
                <li v-for="(menu, index) in pos_product_menus" :key="index" :class="{ 'pos-nav': true }" :label="'menu_count_' + index">
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

        <div class="pos_order_component" v-if="active_print">
            <invoice-print
                :localObject="localObject"
                :orderData="order_data"
            ></invoice-print>
        </div>
    </div>
</template>

<script>

    export default {

        props: ['localObject'],
        data() {
            return {
                pos_product_menus: [
                    {
                        'code': 'sales_history',
                        'name': this.$t('pos_sales.top_menu.sales_history'),
                        'route': '/pos/sales/history'
                    },
                    {
                        'code': 'hold_sale',
                        'name': this.$t('pos_sales.top_menu.hold_sale'),
                        'route': '/pos/sales/hold'
                    },
                    {
                        'code': 'offline_sale',
                        'name': this.$t('pos_sales.top_menu.offline_sale'),
                        'route': '/pos/sales/offline'
                    }
                ],
                active_print: false,
                order_data: {},
                container_height: ($(window).height() - 104) + 'px',

                current_hold_cart: {},
                holdCartCount: 0,
                success_move_cart: this.$t('pos_sales.menu_content.hold_sales.success_move_cart'),
                months: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            };
        },
        beforeDestroy () {
            EventBus.$off('printPosOrder');
            EventBus.$off('addBackToCart');
        },
        created() {
            EventBus.$on('printPosOrder', (order) => {
                this.printOrder(order);
            });

            EventBus.$on('addBackToCart', (hold_cart_index) => {
                this.moveToCart(hold_cart_index);
            });
        },
        methods: {
            printOrder(order) {
                if (order) {
                    this.active_print   = true;
                    this.order_data     = order;
                    var body = document.querySelector("body");
                    body.classList.add("pos-order-print");
                    
                    setTimeout(() => {
                        window.print();
                    
                        var body = document.querySelector("body");
                        body.classList.remove("pos-order-print");
                        this.active_print = false;
                    }, 50);
                }
            },

            moveToCart(holdCartIndex) {

                this.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');

                this.current_hold_cart = this.localObject.pos_carts[this.localObject.pos_current_cart];

                let current_hold = {};

                if (this.localObject.pos_holds[holdCartIndex]) {
                    current_hold = this.localObject.pos_holds[holdCartIndex];
                    
                    delete current_hold.hold_status;
                    delete current_hold.hold_data;

                    // 1. remove from hold object
                    this.localObject.pos_holds.splice(holdCartIndex, 1);
                    
                    // 2. add current cart to hold object
                    let length_holds = Object.keys(this.localObject.pos_holds).length;
                    if (this.current_hold_cart && Object.keys(this.current_hold_cart).length > 0) {
                        this.current_hold_cart.hold_status = true;
                        this.current_hold_cart.hold_data = {
                                    note: ' ',
                                    date: this.getFormattedDate(),
                                    time: this.getFormattedTime()
                                };
                        this.localObject.pos_holds = this.localObject.pos_holds.concat(this.current_hold_cart);
                    }
                    
                    this.holdCartCount = Object.keys(this.localObject.pos_holds).length;
                    
                    EventBus.$emit('setLocalForage', {'key': 'pos_holds', 'data': JSON.stringify(this.localObject.pos_holds)});

                    // 3. move current cart to hold object
                    this.localObject.pos_carts.splice(this.localObject.pos_current_cart, 1);

                    this.localObject.pos_carts[this.localObject.pos_current_cart] = current_hold;

                    EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.localObject.pos_carts)});

                    this.$toast.success(this.success_move_cart, {
                        position: 'top-right',
                        duration: 2000,
                    });

                    this.$router.push({name: 'pos_home'});
                }
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
