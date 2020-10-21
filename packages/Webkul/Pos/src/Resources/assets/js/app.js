window.jQuery = window.$ = require('jquery');

import Vue from 'vue';
import VueRouter from 'vue-router';
import VeeValidate from 'vee-validate';
import VueI18n from 'vue-i18n';
import axios from 'axios';
import VueLocalForage from 'vue-localforage'

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/index.css';

Vue.prototype.$http = axios;
axios.defaults.baseURL = window.app_base_url;

import { routes } from './routes';
import { messages } from './locales';

Vue.use(VueRouter);
Vue.use(VeeValidate);
Vue.use(VueI18n);
Vue.use(VueLocalForage)
Vue.use(VueToast);

const router = new VueRouter({
    mode: 'history',
    base: window.base_dir_url,
    routes
});

let base_locale = window.pos_default_locale;

router.beforeEach((to, from, next) => {
    if (to.name) {
        if (!messages[base_locale]) {
            document.title = '';
        } else {
            document.title = messages[base_locale][to.name].title;
        }
        next()
    }
})

// Create VueI18n instance with options
const i18n = new VueI18n({
    locale: base_locale, // set locale
    messages, // set locale messages
});

window.EventBus = new Vue();
  
Vue.component("pos-modal", require("./components/pos-modal.vue"));
Vue.component("pos-common-modal", require("./components/secured/common/pos-common-modal.vue"));
Vue.component("bagisto-pos-progress-bar", require("./components/bagisto-pos-progress-bar.vue"));
Vue.component("ajax-loader", require("./components/secured/common/ajax-loader.vue"));
Vue.component("navtop-search", require("./components/secured/common/navtop-search.vue"));
Vue.component("pos-navtop", require("./components/secured/common/pos-navtop.vue"));
Vue.component("pos-navleft", require("./components/secured/common/pos-navleft.vue"));
Vue.component("pos-categories", require("./components/secured/common/categories/pos-categories.vue"));
Vue.component("pos-category-nav", require("./components/secured/common/categories/pos-category-nav.vue"));
Vue.component("pos-category-item", require("./components/secured/common/categories/pos-category-item.vue"));
Vue.component("pos-home-products", require("./components/secured/common/pos-home-products.vue"));
Vue.component("configurable", require("./components/secured/common/type/configurable.vue"));
Vue.component("grouped", require("./components/secured/common/type/grouped.vue"));
Vue.component("bundle", require("./components/secured/common/type/bundle.vue"));
Vue.component("bundle-options", require("./components/secured/common/type/bundle-options.vue"));
Vue.component("downloadable", require("./components/secured/common/type/downloadable.vue"));
Vue.component("booking", require("./components/secured/common/type/booking.vue"));
Vue.component("default-booking", require("./components/secured/common/type/booking/default.vue"));
Vue.component("appointment-booking", require("./components/secured/common/type/booking/appointment.vue"));
Vue.component("event-booking", require("./components/secured/common/type/booking/event.vue"));
Vue.component("rental-booking", require("./components/secured/common/type/booking/rental.vue"));
Vue.component("table-booking", require("./components/secured/common/type/booking/table.vue"));
Vue.component("slots", require("./components/secured/common/type/booking/slots.vue"));
Vue.component("booking-date", require("./components/secured/common/type/booking/booking-date.vue"));
Vue.component("pos-cart", require("./components/secured/common/pos-cart.vue"));
Vue.component("quantity-panel", require("./components/secured/common/quantity-panel.vue"));
Vue.component("discount-form", require("./components/secured/setting/discount-form.vue"));
Vue.component("request-form", require("./components/secured/product/request-form.vue"));
Vue.component("customerview", require("./components/secured/customer/customerview.vue"));
Vue.component("customer-form", require("./components/secured/customer/customer-form.vue"));
Vue.component("drawer-form", require("./components/secured/cashier/drawer-form.vue"));
Vue.component("custom-product-form", require("./components/secured/common/cart/custom-product-form.vue"));
Vue.component("discount-list", require("./components/secured/common/cart/discount-list.vue"));
Vue.component("hold-note", require("./components/secured/common/cart/hold-note.vue"));
Vue.component("invoice-print", require("./components/secured/sales/invoice-print.vue"));
Vue.component("footer-link", require("./components/footer-link.vue"));
Vue.component("booking-form", require("./components/secured/restaurant/booking-form.vue"));
Vue.component("calender-date", require("./components/secured/common/calender-date"));
Vue.component("calender-time", require("./components/secured/common/calender-time"));

require('flatpickr/dist/flatpickr.css');

$(document).ready(function () {

    function onlineWifiStatus() {
        app.browserNetwork();
    }

    window.addEventListener('online', onlineWifiStatus);
    window.addEventListener('offline', onlineWifiStatus);

    const app = new Vue({
        el: "#app",
        router,
        i18n,
        VueLocalForage,
        VueToast,
        data: {
            posModalId: 'modal_1',
            pos_print_status: false,
            posModalStatus: false,
            posCommonModal: {},
            posLoader: {},
            offline: 0,
            current_currency: window.pos_currency_symbol
        },
        created() {
            axios.interceptors.response.use(
                function(response) {
                    return response;
                },
                this.errorResponseHandler
            );

            this.checkPosUserLogin();

            EventBus.$on('closeModal', (id) => {
                this.posModalId = '';
                this.posModalStatus = false;
            });
            EventBus.$on('showCommonModal', (id) => {
                this.showCommonModal(id);
            });
            EventBus.$on('hideCommonModal', (id) => {
                this.hideCommonModal(id);
            });
            EventBus.$on('showLoader', (id) => {
                this.showCommonLoader(id);
            });
            EventBus.$on('hideLoader', (id) => {
                this.hideCommonLoader(id);
            });
            EventBus.$on('posPrintOn', () => {
                this.hideNonPrint();
            });
            EventBus.$on('posPrintOff', () => {
                this.showNonPrint();
            });
            EventBus.$on('checkWifi', (statusWifi) => {
                this.onlineStatus(statusWifi);
            });
        },
        mounted() {
            this.addFlashMessages();
        },
        methods: {
            errorResponseHandler (error) {
                if (error.response.status == 401) {
                    EventBus.$emit('clearLocalForage');

                    location.reload();
                } else {
                    return Promise.reject(error);
                }
            },
    
            checkPosUserLogin() {
                this.$http.get('/api/pos/userlogin')
                .then((response)  =>  {
                    if ( !response.data.status && response.data.route) {
                        if (this.$router.currentRoute.name != 'pos_login') {
                            this.$router.push({name: response.data.route});
                        }
                    } else if (response.data.status && response.data.route) {
                        if (this.$router.currentRoute.name != 'pos_home') {
                            this.$router.push({name: response.data.route});
                        }
                    }
                })
            },

            addFlashMessages() {
                const flashes = this.$refs.flashes;
                flashMessages.forEach(function(flash) {
                    if (flash.type == 'alert-success') {
                        this.$toast.success(flash.message, {
                            position: 'top-right',
                            duration: 2000,
                        });
                    } else if (flash.type == 'alert-warning') {
                        this.$toast.warning(flash.message, {
                            position: 'top-right',
                            duration: 2000,
                        });
                    } else if (flash.type == 'alert-error') {
                        this.$toast.error(flash.message, {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                }, this);
            },

            showModal(id) {
                this.posModalId = id;
                this.posModalStatus = true;
            },

            closeModal(id) {
                this.posModalId = '';
                this.posModalStatus = false;
            },

            showCommonModal(modalId) {
                this.$set(this.posCommonModal, modalId, true);
                var body = document.querySelector("body");
                    body.classList.add("pos-modal-open");
                    $("body").find(".pos-navbar-top").addClass('pos-overlay');
                    $("body").find(".pos-navbar-left").addClass('pos-overlay');
            },

            hideCommonModal(modalId) {
                this.$set(this.posCommonModal, modalId, false);
                var body = document.querySelector("body");
                    body.classList.remove("pos-modal-open");
                $("body").find(".pos-navbar-top").removeClass('pos-overlay');
                $("body").find(".pos-navbar-left").removeClass('pos-overlay');
            },

            showCommonLoader(loaderId) {
                this.$set(this.posLoader, loaderId, true);
                var body = document.querySelector("body");
                    body.classList.add("pos-loader-open");
                    $("body").find(".pos-navbar-top").addClass('pos-overlay');
                    $("body").find(".pos-navbar-left").addClass('pos-overlay');
            },

            hideCommonLoader(loaderId) {
                this.$set(this.posLoader, loaderId, false);
                var body = document.querySelector("body");
                    body.classList.remove("pos-loader-open");
                $("body").find(".pos-navbar-top").removeClass('pos-overlay');
                $("body").find(".pos-navbar-left").removeClass('pos-overlay');
            },

            toggleButtonDisable (value) {
                var buttons = document.getElementsByTagName("button");
                
                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].disabled = value;
                }
            },

            toggleCategoryDisable (status) {
                if (status) {
                    $('.related_category').css('pointer-events', 'none');
                } else {
                    $('.related_category').css('pointer-events', 'unset');
                }
            },

            hideNonPrint() {
                this.pos_print_status = true;
                var body = document.querySelector("body");
                    body.classList.add("pos-order-print");
            },

            showNonPrint() {
                this.pos_print_status = false;
                var body = document.querySelector("body");
                    body.classList.remove("pos-order-print");
            },

            onlineStatus(wifiStatus) {
                var wifi_label = $('.nav-wifi');
                if (wifi_label.hasClass('offline_label')) {
                    if (navigator.onLine) {
                        if (wifiStatus) {
                            this.$toast.success('Success: You have successfully entered in online mode!', {
                                position: 'top-right',
                                duration: 2000,
                            });
                        }
                        wifi_label.removeClass('offline_label').addClass('online_label');
                        this.offline = 0;
                    }
                } else {
                    if (wifiStatus) {
                        this.$toast.warning('Warning: You have successfully entered in offline mode!', {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                    wifi_label.removeClass('online_label').addClass('offline_label');
                    this.offline = 1;
                }
            },
            
            browserNetwork() {
                var wifi_label = $('.nav-wifi');
                if (navigator.onLine) {
                    wifi_label.removeClass('offline_label').addClass('online_label');
                    this.offline = 0;
                    this.$toast.success('Success: You have successfully entered in online mode!', {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    wifi_label.removeClass('online_label').addClass('offline_label');
                    this.offline = 1;
                    this.$toast.warning('Warning: You have entered in offline mode!', {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            }
        }
    }).$mount('#app')
    
});