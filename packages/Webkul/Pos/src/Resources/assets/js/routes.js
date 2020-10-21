import Login            from './components/anonymous/login-form.vue';
import View             from './components/secured/view.vue';
import Home             from './components/secured/home.vue';
import Payment          from './components/secured/payment.vue';
import CashPayment      from './components/secured/payment/cash.vue';
import CreditPayment    from './components/secured/payment/credit.vue';
import Sales            from './components/secured/sales.vue';
import History          from './components/secured/sales/history.vue';
import Hold             from './components/secured/sales/hold.vue';
import Offline          from './components/secured/sales/offline.vue';
import Customer         from './components/secured/customer.vue';
import Cashier          from './components/secured/cashier.vue';
import CloseCounter     from './components/secured/cashier/closecounter.vue';
import TodayCash        from './components/secured/cashier/todaycash.vue';
import SalesHistory     from './components/secured/cashier/saleshistory.vue';
import Reports          from './components/secured/reports.vue';
import Product          from './components/secured/product.vue';
import LowStock         from './components/secured/product/low-stock.vue';
import RequestedProduct from './components/secured/product/requested-product.vue';
import ProductSetting   from './components/secured/product/product-setting.vue';
import Restaurant       from './components/secured/restaurant.vue';
import TableList        from './components/secured/restaurant/table-list.vue';
import TableCreate      from './components/secured/restaurant/table-create.vue';
import TableBooked      from './components/secured/restaurant/table-booked.vue';
import Setting          from './components/secured/setting.vue';
import Discount         from './components/secured/setting/discount.vue';
import Taxes            from './components/secured/setting/taxes.vue';
import Basic            from './components/secured/setting/basic.vue';
import Profile          from './components/secured/setting/profile.vue';

export const routes = [
    { 
        path: '/pos/login',
        component: Login,
        name: 'pos_login'
    },
    {
        path: '/pos/',
        component: View,
        name: 'pos_view',
        children: [
            {
                path: 'home',
                component: Home,
                name: 'pos_home'
            },
            {
                path: 'payment',
                component: Payment,
                children: [
                    {
                        path: '',
                        component: CashPayment,
                        name: 'pos_payment'
                    },
                    {
                        path: 'cash',
                        component: CashPayment,
                        name: 'pos_payment_cash'
                    },
                    {
                        path: 'credit',
                        component: CreditPayment,
                        name: 'pos_payment_credit'
                    },
                ]
            },
            {
                path: 'sales',
                component: Sales,
                children: [
                    {
                        path: '',
                        component: History,
                        name: 'pos_sales'
                    },
                    {
                        path: 'history',
                        component: History,
                        name: 'pos_sales_history'
                    },
                    {
                        path: 'hold',
                        component: Hold,
                        name: 'pos_sales_hold'
                    },
                    {
                        path: 'offline',
                        component: Offline,
                        name: 'pos_sales_offline'
                    }
                ]
            },
            {
                path: 'customer',
                component: Customer,
                name: 'pos_customer'
            },
            {
                path: 'cashier',
                component: Cashier,
                children: [
                    {
                        path: '',
                        component: CloseCounter,
                        name: 'pos_cashier'
                    },
                    {
                        path: 'closecounter',
                        component: CloseCounter,
                        name: 'pos_cashier_closecounter'
                    },
                    {
                        path: 'todaycash',
                        component: TodayCash,
                        name: 'pos_cashier_todaycash'
                    },
                    {
                        path: 'saleshistory',
                        component: SalesHistory,
                        name: 'pos_cashier_saleshistory'
                    }
                ]
            },
            {
                path: 'reports',
                component: Reports,
                name: 'pos_reports'
            },
            {
                path: 'product',
                component: Product,
                children: [
                    {
                        path: '',
                        component: LowStock,
                        name: 'pos_product'
                    },
                    {
                        path: 'lowstock',
                        component: LowStock,
                        name: 'pos_product_low_stock'
                    },
                    {
                        path: 'requested',
                        component: RequestedProduct,
                        name: 'pos_product_requested'
                    },
                    {
                        path: 'setting',
                        component: ProductSetting,
                        name: 'pos_product_setting'
                    }
                ]
            },  {
                path: 'restaurant',
                component: Restaurant,
                children: [
                    {
                        path: '',
                        component: TableList,
                        name: 'pos_restaurant'
                    }, {
                        path: 'table-list',
                        component: TableList,
                        name: 'pos_restaurant_table_list'
                    }, {
                        path: 'table-create',
                        component: TableCreate,
                        name: 'pos_restaurant_table_create'
                    }, {
                        path: 'table-booked',
                        component: TableBooked,
                        name: 'pos_restaurant_table_booked'
                    }
                ]
            }, {
                path: 'setting',
                component: Setting,
                children: [
                    {
                        path: '',
                        component: Discount,
                        name: 'pos_setting'
                    },
                    {
                        path: 'discount',
                        component: Discount,
                        name: 'pos_setting_discount'
                    },
                    {
                        path: 'taxes',
                        component: Taxes,
                        name: 'pos_setting_taxes'
                    },
                    {
                        path: 'basic',
                        component: Basic,
                        name: 'pos_setting_basic'
                    },
                    {
                        path: 'profile',
                        component: Profile,
                        name: 'pos_setting_profile'
                    }
                ]
            }
        ]
    }
];
