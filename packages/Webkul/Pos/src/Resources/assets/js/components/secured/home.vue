
<template>
    <div>
        <div class="pos-home-main" v-if="user_id">
            <div class="pos-categories">
                <pos-categories
                    :localObject='localObject'
                    @onCategoryChange="getOutletProducts"
                ></pos-categories>
            </div>
            <div class="pos-product-container" v-if="total_product" v-bind:style="{'height': home_container_height}">
                <pos-home-products
                    :localObject='localObject'
                    :pos_products='pos_products'
                    :pos_product_count='total_product'
                ></pos-home-products>

                <div class="pos-buttons-container hide">
                    <div>
                        <button type="button" class="btn btn-lg btn-pos-primary">
                            <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            <b>{{ $t('pos_home.button_container.btn_quantity') }}</b>
                        </button>
                        <button type="button" class="btn btn-lg btn-pos-primary">
                            <i class="fa fa-percent" aria-hidden="true"></i>
                            <b>{{ $t('pos_home.button_container.btn_discount') }}</b>
                        </button>
                        <button type="button" class="btn btn-lg btn-pos-primary" @click="emptyCurrentCart">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                            <b>{{ $t('pos_home.button_container.btn_empty_cart') }}</b>
                        </button>    
                    </div>
                </div>
            </div>
            <div  class="pos-product-container" v-bind:style="{'height': home_container_height}" v-else >
                <div class="message-alert danger">
                    {{ $t("pos_home.pos_products.error.no_product") }}
                </div>
            </div>
        </div>
        
        <div class="pos-cart-container" v-if="user_id" v-bind:style="{'height': cart_container_height}">
            <pos-cart
                :pos_carts='pos_carts'
                :pos_current_cart='pos_current_cart'
                :showMinusBtn='showMinusBtn'
                :cartCustomer='cartCustomer'
                :hold_count='holdCartCount'
                :localObject='localObject'
            ></pos-cart>
        </div>

        <pos-common-modal
            v-if="drawer_modal_status"
            id="addDrawer"
            :showClose='true'
            :is-open="this.$root.posCommonModal.addDrawer"
        >
            <h4 slot="header">{{ $t('pos_cashier.menu_content.text_opening_amount') }}</h4>
            
            <div slot="body">
                <drawer-form
                    :drawer_data='drawer_data'
                    :localObject='localObject'
                ></drawer-form>
            </div>
        </pos-common-modal>

        <div v-if="this.$root.posCommonModal.barcodeModal">
            <pos-common-modal id="barcodeModal" :is-open="this.$root.posCommonModal.barcodeModal">
                <h4 slot="header">{{ $t('pos_home.navtop.entry_product_search') }}</h4>
                            
                <div slot="body">
                    <div class="control-group">
                        <input type="text" class="control" name="search_barcode" id="search_barcode" v-model="barcode_text" :placeholder="$t('pos_home.navtop.search_barcode_placeholder')" v-validate="'alpha_spaces'" @keyup="barcodeSearch">
                    </div>
                </div>
            </pos-common-modal>
        </div>
        
        <ajax-loader v-if="show_loader" id="dataLoader"
            :is-open="this.$root.posLoader.dataLoader"
            :isActive=isActive
            :progressWidth=progressWidth
            :progressData=progressData
        ></ajax-loader>
    </div>
</template>

<script>

    export default {
        props: ['localObject'],
        data() {
            return {
                user_id: 0,
                customer_groups: [],
                pos_banks: [],
                pos_products: [],
                pos_cashier: this.localObject.pos_cashier,
                total_product: 0,
                page: 1,
                limit: 2,
                totalPage: 0,
                filter_name: '',
                outlet_id: 0,
                category_id: null,
                pos_carts: [],
                pos_current_cart: 0,
                current_hold_cart: {},
                holdCartCount: 0,
                success_hold_cart: this.$t('pos_home.pos_cart.success_hold_cart'),
                error_plus_cart: this.$t('pos_home.pos_cart.error.error_plus_cart', { cart_count: 3 }),
                showMinusBtn: false,
                cartCustomer: [],
                drawer_modal_status: 0,
                drawer_data: {},
                home_container_height: ($(window).height() - 106) + 'px',
                cart_container_height: ($(window).height() - 60) + 'px',
                barcode_text: '',
                search_opt: window.search_option,
                no_barcode_product: this.$t('pos_home.pos_products.error.no_barcode_product'),
                no_barcode_provided: this.$t('pos_home.pos_products.error.no_barcode_provided'),
                success_add_to_cart: this.$t('pos_home.pos_products.success_add_to_cart'),
                
                show_loader: 0,
                productRequests: [],
                isActive: 0,
                progressWidth: 0,
                progressData: {
                    noteInfo: 'Note: Please don\'t refresh the page/redirect till the sync process have been compeleted!',
                    textContent: '',
                    textContentType: '',
                    messageStatus: false,
                },
                current_currency_symbol: window.pos_currency_symbol,
                base_symbol: window.base_currency_symbol,
                current_booking: {},
            };
        },
        beforeDestroy () {
            EventBus.$off('cartAddProduct');
            EventBus.$off('barcodeFilter');
            EventBus.$off('addNewCart');
        },
        created() {
            EventBus.$on('searchPosProduct', (keyword) => {
                this.page = 1;
                this.total_product = 0;
                this.pos_products = [];
                this.filter_name = keyword;
                this.loadProducts();
            });

            EventBus.$on('barcodeFilter', (object) => {
                if (object.search_keyword.length > 0) {
                    this.barcode_text = object.search_keyword;
                    this.barcodeSearch(object.event);
                } else {
                    this.barcode_text = '';
                    $('#nav-search').val('');
                    $('#nav-search').focus();
                }
            });

            EventBus.$on('cartAddProduct', (product) => {
                this.addProductToCart(product);
            });

            EventBus.$on('resetDrawerStatus', (updatedDrawer) => {
                this.drawer_modal_status = 0;
                this.localObject.pos_drawer = updatedDrawer;
            });

            EventBus.$on('addNewCart', () => {
                this.current_booking = {};
                this.addNewCart();
            });

            EventBus.$on('cartActive', (cartIndex) => {
                this.pos_current_cart = cartIndex;
                this.localObject.pos_current_cart = cartIndex;
                EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': this.pos_current_cart});
            });

            EventBus.$on('changeMinusBtn', (status) => {
                this.showMinusBtn = status;
            });

            EventBus.$on('applyHoldCart', (hold_order) => {
                this.holdCurrentCart(hold_order);
            });
            
            EventBus.$on('loadCartContent', () => {
                this.loadCarts();
            });

            EventBus.$on('addProductTable', (booking) => {
                this.addProductToBooking(booking);
            });
        },

        mounted() {
            this.countHoldTotal();
            this.getOutletProducts();
            this.loadCarts();
            this.getBanks();
            this.getCustomerGroups();
        },

        methods: {
            checkPosUserLogin() {
                if (this.localObject.pos_cashier.id) {
                
                    this.user_id = this.localObject.pos_cashier.id;

                    if ( !this.localObject.pos_drawer.id || (this.localObject.pos_drawer.id && this.localObject.pos_drawer.modal_open) ) {
                        EventBus.$emit('showCommonModal', 'addDrawer');
                        this.drawer_modal_status = 1;
                        if (this.localObject.pos_drawer.id) {
                            this.drawer_data = this.localObject.pos_drawer;
                        } else {
                            this.drawer_data = {};
                        }
                    }
                }
            },

            countHoldTotal() {
                if (this.localObject.pos_holds) {
                    this.holdCartCount = Object.keys(this.localObject.pos_holds).length;
                }
            },

            getOutletProducts(categoryId = null) {
                this.page = 1;
                this.total_product = 0;
                
                EventBus.$emit('getLocalForage', 'pos_cashier');
                if (this.localObject.pos_cashier.outlet_id) {
                    this.pos_products = [];
                    this.outlet_id  = this.localObject.pos_cashier.outlet_id;
                    this.category_id = categoryId;
                    this.loadProducts();
                }
            },

            loadProducts() {
                var thisthis = this;

                if (thisthis.$root.offline) {
                    this.checkPosUserLogin();
                    if (Object.keys(thisthis.localObject.pos_products).length > 0) {
                        thisthis.total_product = Object.keys(thisthis.localObject.pos_products).length;
                        thisthis.pos_products = thisthis.localObject.pos_products;

                        let getFilterProducts = [];
                        thisthis.total_product = 0;

                        if (thisthis.category_id) {
                            var categoryFilterArray = [];

                            if (Object.keys(thisthis.localObject.pos_product_categories).length > 0) {

                                $.each(thisthis.localObject.pos_product_categories, (key, product_category) => {
                                    if (parseInt(product_category.category_id) === parseInt(thisthis.category_id) ) {
                                        $.each(this.localObject.pos_products, (key, product) => {
                                            if (product.id === product_category.product_id) {
                                                var foundStatus = categoryFilterArray.includes(product.id);

                                                if (thisthis.filter_name.length > 0) {
                                                    if ((product.name.toLowerCase().indexOf(thisthis.filter_name.toLowerCase()) !== -1) || (product.sku.toLowerCase() == thisthis.filter_name.toLowerCase()) ) {
                                                        getFilterProducts = getFilterProducts.concat(product);
                                                        thisthis.total_product += 1;    
                                                    }
                                                } else {
                                                    if (!foundStatus) {
                                                        categoryFilterArray.push(product.id);
                                                        getFilterProducts = getFilterProducts.concat(product);
                                                        thisthis.total_product += 1;
                                                    }
                                                }
                                            }
                                        });
                                    }
                                });
                                thisthis.pos_products = getFilterProducts;
                            }
                        } else {
                            if (thisthis.filter_name.length > 0) {
                                $.each(thisthis.localObject.pos_products, (key, product) => {
                                    if ((product.name.toLowerCase().indexOf(thisthis.filter_name.toLowerCase()) !== -1) || (product.sku.toLowerCase() == thisthis.filter_name.toLowerCase()) ) {
                                        getFilterProducts = getFilterProducts.concat(product);
                                        thisthis.total_product += 1;    
                                    }
                                });
                                thisthis.pos_products = getFilterProducts;
                            } else {
                                thisthis.pos_products = thisthis.localObject.pos_products;
                                thisthis.total_product = Object.keys(thisthis.localObject.pos_products).length;
                            }
                        }
                    } else {
                        thisthis.pos_products = [];
                        thisthis.total_product = 0;
                    }
                } else {
                    if (Object.keys(thisthis.localObject.pos_products).length > 0) {
                        this.checkPosUserLogin();

                        let getFilterProducts = [];
                        thisthis.total_product = 0;

                        if (thisthis.category_id) {
                            var categoryFilterArray = [];

                            if (Object.keys(thisthis.localObject.pos_product_categories).length > 0) {

                                $.each(thisthis.localObject.pos_product_categories, (key, product_category) => {
                                    if (parseInt(product_category.category_id) === parseInt(thisthis.category_id) ) {
                                        $.each(thisthis.localObject.pos_products, (key, product) => {
                                            if (product.id === product_category.product_id) {
                                                
                                                var foundStatus = categoryFilterArray.includes(product.id);

                                                if (thisthis.filter_name.length > 0) {
                                                    if ((product.name.toLowerCase().indexOf(thisthis.filter_name.toLowerCase()) !== -1) || (product.sku.toLowerCase() == thisthis.filter_name.toLowerCase()) ) {
                                                        getFilterProducts = getFilterProducts.concat(product);
                                                        thisthis.total_product += 1;    
                                                    }
                                                } else {
                                                    if (!foundStatus) {
                                                        categoryFilterArray.push(product.id);
                                                        getFilterProducts = getFilterProducts.concat(product);
                                                        thisthis.total_product += 1;
                                                    }
                                                }
                                            }
                                        });
                                    }
                                });
                                thisthis.pos_products = getFilterProducts;
                            }
                        } else {
                            if (thisthis.filter_name.length > 0) {
                                $.each(thisthis.localObject.pos_products, (key, product) => {
                                    if ((product.name.toLowerCase().indexOf(thisthis.filter_name.toLowerCase()) !== -1) || (product.sku.toLowerCase() == thisthis.filter_name.toLowerCase()) ) {
                                        getFilterProducts = getFilterProducts.concat(product);
                                        thisthis.total_product += 1;    
                                    }
                                });
                                thisthis.pos_products = getFilterProducts;
                            } else {
                                thisthis.pos_products = thisthis.localObject.pos_products;
                                thisthis.total_product = Object.keys(thisthis.localObject.pos_products).length;
                            }
                        }
                    } else {
                        if (thisthis.localObject.pos_cashier.id) {
                            thisthis.show_loader = 1;
                            thisthis.isActive = 1;
                            EventBus.$emit('showLoader', 'dataLoader');
                            
                            thisthis.$http.get('/api/pos/auth/outletProducts', {
                                params: {
                                    page: thisthis.page,
                                    inventory_source_id: thisthis.localObject.pos_cashier.inventory_source_id,
                                    category_id: thisthis.category_id,
                                    outlet_id: thisthis.outlet_id,
                                    filter_name: thisthis.filter_name,
                                    filter_barcode: thisthis.barcode_text,
                                }
                            })
                            .then((response)  =>  {
                                if (response.data.data && response.data.data.length > 0) {
                                    thisthis.totalPage = response.data.meta.last_page;
                                    thisthis.total_products = response.data.meta.total;
                                    
                                    thisthis.progressData.textContent = thisthis.$t('pos_home.pos_products.total_product_info', { total_product: thisthis.total_products});
                                    thisthis.progressData.textContentType = 'success';
                                    
                                    for (thisthis.page = 1; thisthis.page <= thisthis.totalPage; thisthis.page++) {
                                    
                                        thisthis.productRequests.push({
                                            url: '/api/pos/auth/outletProducts',
                                            method: 'get',
                                            async:   true,
                                            params: {
                                                page: thisthis.page,
                                                inventory_source_id: thisthis.localObject.pos_cashier.inventory_source_id,
                                                category_id: thisthis.category_id,
                                                outlet_id: thisthis.outlet_id,
                                                filter_name: thisthis.filter_name,
                                                filter_barcode: thisthis.barcode_text,
                                            },
                                        });
                                    }
                                } else {
                                    thisthis.total_product = 0;
                                    thisthis.totalPage = 0;
                                    thisthis.productRequests = {};
                                }
                            })
                            .catch(function (error) {})
                            .finally(() => thisthis.NextRequest());
                        }                        
                    }
                }
            },

            NextRequest() {
                var thisthis = this;
                
                if (thisthis.productRequests.length) {
                    thisthis.progressWidth = (100 - (thisthis.productRequests.length / thisthis.totalPage) * 100);
                    
                    thisthis.$root.$http(thisthis.productRequests.shift())
                    .then(function(response) {
                        if (response.data.data && response.data.data.length > 0) {
                            thisthis.total_product = parseInt(thisthis.total_product) + parseInt(response.data.data.length);

                            thisthis.progressData.textContentType = 'success';
                            thisthis.progressData.textContent = thisthis.$t('pos_home.pos_products.total_product_loaded', { total_loaded: thisthis.total_product , outof_total: thisthis.total_products});
                            thisthis.pos_products = thisthis.pos_products.concat(response.data.data);
                        }
                    })
                    .finally(() => thisthis.NextRequest());
                } else {
                    thisthis.progressWidth = 100;
                    thisthis.show_loader = 0;
                    thisthis.isActive = 0;
                    EventBus.$emit('hideLoader', 'dataLoader');

                    EventBus.$emit('setLocalForage', {'key': 'pos_products', 'data': JSON.stringify(thisthis.pos_products)});

                    thisthis.localObject.pos_products = thisthis.pos_products;

                    this.checkPosUserLogin();
                }
            },

            loadCarts() {
                var thisthis = this;
                    thisthis.current_booking = {};

                EventBus.$emit('getLocalForage', 'pos_current_cart');
                
                if (thisthis.localObject.pos_carts.length > 0) {
                    if (thisthis.localObject.pos_carts.length > 1) {
                        thisthis.showMinusBtn = true;
                    }
                    thisthis.pos_carts = thisthis.localObject.pos_carts;
                    
                    if (Object.keys(thisthis.pos_carts).length <= thisthis.localObject.pos_current_cart) {
                        thisthis.localObject.pos_current_cart = 0;
                    }
                    thisthis.pos_current_cart = thisthis.localObject.pos_current_cart;
                } else {
                    if (thisthis.pos_carts.length == 0) {
                        thisthis.pos_carts[0] = {};
                        thisthis.pos_current_cart = 0;
                        
                        EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
                        
                        EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': thisthis.pos_current_cart});
                    }
                }
                
                if (thisthis.localObject.pos_cart_customer && thisthis.localObject.pos_cart_customer.email) {
                    thisthis.cartCustomer = thisthis.localObject.pos_cart_customer;
                }
            },

            searchBooking(bookedId, cartProducts) {
                for (var i=0; i < Object.keys(cartProducts).length; i++) {
                    if (cartProducts[i].booking_id && cartProducts[i].booking_id === bookedId) {
                        return cartProducts[i];
                    }
                }
            },

            getBanks() {
                var self = this;
                if (self.$root.offline) {
                    if (Object.keys(self.localObject.pos_banks).length > 0) {
                        self.pos_banks = self.localObject.pos_banks;
                    }
                } else {
                    if (self.localObject.pos_cashier.id) {
                        this.$http.get('/api/pos/auth/getBanks', {
                            params: {
                                user_id: self.localObject.pos_cashier.id
                            }
                        })
                        .then((response)  =>  {
                            if (!response.data.status) {
                                window.flashMessages = [{'type': 'alert-error', 'message': response.data.message}];
                                self.$root.addFlashMessages();
                            } else {
                                self.pos_banks = response.data.data;
                                
                                self.localObject.pos_banks = self.pos_banks;
                                EventBus.$emit('setLocalForage', {'key': 'pos_banks', 'data': JSON.stringify(response.data.data)});
                            }
                        })
                        .catch(function (error) {});
                    }
                }
            },

            getCustomerGroups() {
                var self = this;
                if (self.$root.offline) {
                    self.offline_status = true;

                    if ( Object.keys(self.localObject.pos_customer_groups).length > 0 ) {
                        self.customer_groups = self.localObject.pos_customer_groups;
                    } else {
                        self.customer_groups = [];
                    }
                } else {
                    self.offline_status = false;
                    self.$http.get('/api/pos/getGroups')
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length > 0) {
                            this.customer_groups = response.data.data;

                            EventBus.$emit('setLocalForage', {'key': 'pos_customer_groups', 'data': JSON.stringify(self.customer_groups)});
                        } else {
                            self.customer_groups = [];
                        }
                    })
                    .catch(function (error) {});
                }
            },

            addProductToCart(product) {
                var thisthis    = this;
                var added_qty   = 1;
                if ( product.added_qty ) {
                    added_qty = product.added_qty;
                }
                
                EventBus.$emit('resetCartDiscount');
                thisthis.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');

                if (thisthis.pos_current_cart != 'null' && thisthis.pos_carts[thisthis.pos_current_cart]) {
                    var procuct_exist = false;
                    var cart_product_count = Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length;

                    if ( product.type == 'configurable'
                    || product.type == 'bundle'
                    || product.type == 'downloadable'
                    || product.type == 'booking') {
                        var remaining_quantity = thisthis.validateProductRemainingQty(product);
                        
                        if ( ( typeof remaining_quantity == 'number' && parseInt(remaining_quantity) > 0 ) ) {
                            if (cart_product_count != null && cart_product_count) {
                                
                                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, cart_product) {
                                    cart_product.active = false;

                                    if ( product.id == cart_product.id ) {
                                        procuct_exist = true;
                                        if ( product.type == 'bundle' ) {
                                            procuct_exist = thisthis.compareBundleOptions(product.additional, cart_product.additional);
                                        } else if ( product.type == 'downloadable' ) {
                                            procuct_exist = thisthis.compareDownloadOptions(product.additional, cart_product.additional);
                                        } else if ( product.type == 'booking' ) {
                                            procuct_exist = thisthis.compareBookingOptions(product.additional, cart_product.additional);
                                        }

                                        if ( (parseInt(cart_product.qty_ordered + added_qty) > parseInt(remaining_quantity)) && product.type == 'configurable' ) {
                                            thisthis.$toast.error(thisthis.$t('pos_home.pos_products.error.no_quantity'), {
                                                position: 'top-right',
                                                duration: 2000,
                                            });
                                            return false;
                                        } else {
                                            if ( procuct_exist == true) {
                                                cart_product.active         = true;
                                                cart_product.qty_ordered    = cart_product.qty_ordered + added_qty;
                                                cart_product.weight         = product.weight;
                                                cart_product.total_weight   = (cart_product.qty_ordered * product.weight);

                                                if (thisthis.localObject.pos_current_booking && Object.keys(thisthis.localObject.pos_current_booking).length > 0 ) {
                                                    cart_product.booking_id = thisthis.localObject.pos_current_booking.booking_id;
                                                }
                                                
                                                if ( product.type == 'downloadable') {
                                                    // Product Customer Group Price
                                                    var option_price = {
                                                        price:              (product.base_price - product.cg_base_price),
                                                        converted_price:    (product.price - product.cg_converted_price),
                                                    };

                                                    var copy_product = product;
                                                        copy_product.price              = product.cg_base_price;
                                                        copy_product.converted_price    = product.cg_converted_price;

                                                    var customer_prices = {
                                                            price:              product.cg_base_price,
                                                            converted_price:    product.cg_converted_price,
                                                        };

                                                    customer_prices = thisthis.calculateCustomerGroupPrice(copy_product, cart_product.qty_ordered);
                                                    
                                                    product.price       = parseFloat(customer_prices.converted_price) + parseFloat(option_price.converted_price);
                                                    
                                                    product.base_price  = parseFloat(customer_prices.price) + parseFloat(option_price.price);
                                                    
                                                    product.formated_price  = thisthis.current_currency_symbol + parseFloat(product.price).toFixed(2);
                                                    cart_product.f_price    = product.formated_price;
                                                }
                                                
                                                cart_product.total          = parseFloat(cart_product.qty_ordered * product.price).toFixed(2);
                                                
                                                cart_product.base_total     = parseFloat(cart_product.qty_ordered * product.base_price).toFixed(2);
                                                
                                                cart_product.tax_amount     = parseFloat((cart_product.qty_ordered * product.tax_amount)).toFixed(2);
                                                cart_product.base_tax_amount = parseFloat((cart_product.qty_ordered * product.base_tax_amount)).toFixed(2);
                                                
                                                cart_product.f_total        = window.pos_currency_symbol + parseFloat(cart_product.qty_ordered * product.price).toFixed(2);

                                                thisthis.$toast.success(thisthis.success_add_to_cart, {
                                                    position: 'top-right',
                                                    duration: 2000,
                                                });
                                            }
                                        }
                                    }
                                });
                            }

                            if (!procuct_exist) {
                            const cart_product               = {};
                                cart_product.active          = true;
                                cart_product.id              = product.id;
                                cart_product.sku             = product.sku;
                                cart_product.type            = product.type;
                                cart_product.weight          = product.weight;
                                cart_product.total_weight    = product.total_weight;
                                cart_product.name            = product.name;
                                cart_product.tax_category_id = product.tax_category_id;
                                cart_product.tax_percent     = product.tax_percent;
                                cart_product.tax_amount      = product.tax_amount;
                                cart_product.base_tax_amount = product.base_tax_amount;
                                cart_product.qty_ordered     = product.quantity;

                                if ( product.type == 'downloadable') {
                                    // Product Customer Group Price
                                    var option_price = {
                                        price:              (product.base_price - product.cg_base_price),
                                        converted_price:    (product.price - product.cg_converted_price),
                                    };

                                    var copy_product = product;
                                        copy_product.price              = product.cg_base_price;
                                        copy_product.converted_price    = product.cg_converted_price;

                                    var customer_prices = {
                                            price:              product.cg_base_price,
                                            converted_price:    product.cg_converted_price,
                                        };

                                    customer_prices = thisthis.calculateCustomerGroupPrice(copy_product, added_qty);
                                    product.price       = parseFloat(customer_prices.converted_price) + parseFloat(option_price.converted_price);
                                    
                                    product.base_price  = parseFloat(customer_prices.price) + parseFloat(option_price.price);
                                    
                                    product.formated_price  = thisthis.current_currency_symbol + parseFloat(product.price).toFixed(2);
                                }

                                cart_product.price           = parseFloat(product.price).toFixed(2);
                                cart_product.f_price         = product.formated_price;
                                cart_product.total           = parseFloat(product.quantity * product.price).toFixed(2);
                                
                                cart_product.base_price      = parseFloat(product.base_price).toFixed(2);
                                cart_product.base_total      = parseFloat(product.quantity * product.base_price).toFixed(2);
                                
                                cart_product.tax_amount      = product.tax_amount;
                                cart_product.base_tax_amount = product.base_tax_amount;

                                cart_product.f_total         = window.pos_currency_symbol + parseFloat(product.quantity * product.price).toFixed(2);
                                cart_product.additional      = product.additional;
                                

                                if (thisthis.localObject.pos_current_booking && Object.keys(thisthis.localObject.pos_current_booking).length > 0 ) {
                                    cart_product.booking_id = thisthis.localObject.pos_current_booking.booking_id;
                                }

                                if (cart_product_count == 0) {
                                    thisthis.$set(thisthis.pos_carts[thisthis.pos_current_cart], cart_product_count, cart_product);
                                } else {
                                    let new_index = (parseInt(Object.keys(thisthis.pos_carts[thisthis.pos_current_cart])[cart_product_count-1]) + parseInt(1));
                                    
                                    thisthis.$set(thisthis.pos_carts[thisthis.pos_current_cart], new_index, cart_product);
                                }

                                this.$toast.success(thisthis.success_add_to_cart, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            }
                            
                            EventBus.$emit('hideCommonModal', product.type);

                            EventBus.$emit('onshowCartContent');
                            EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
                        } else {
                            this.$toast.error(this.$t('pos_home.pos_products.error.no_quantity'), {
                                position: 'top-right',
                                duration: 2000,
                            });
                        }

                        if (this.search_opt == 0) {
                            this.barcode_text = '';
                            $('#nav-search').val('');
                            $('#nav-search').focus();
                        }
                    } else {
                        var remaining_quantity = thisthis.validateProductRemainingQty(product);
                        if ( ( typeof remaining_quantity == 'number' && parseInt(remaining_quantity) > 0 ) ) {
                            
                            // If cart contains at least single product
                            if (cart_product_count != null && cart_product_count) {

                                $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, cart_product) {
                                    cart_product.tax_amount         = 0;
                                    cart_product.base_tax_amount    = 0;
                                    cart_product.active             = false;

                                    if ( product.id == cart_product.id ) {
                                        var ordered_qty = parseInt(cart_product.qty_ordered) + parseInt(added_qty);
                                        procuct_exist   = true;
                                        cart_product.active         = true;
                                        cart_product.qty_ordered    = ordered_qty;
                                        cart_product.weight         = product.weight;
                                        cart_product.total_weight   = (cart_product.qty_ordered * product.weight);

                                        if (thisthis.localObject.pos_current_booking && Object.keys(thisthis.localObject.pos_current_booking).length > 0 ) {
                                            cart_product.booking_id = thisthis.localObject.pos_current_booking.booking_id;
                                        }

                                        if (product.special_price) {
                                            cart_product.price          = parseFloat(product.converted_special_price).toFixed(2);
                                            cart_product.f_price        = product.formated_special_price;
                                            cart_product.base_total     = parseFloat(cart_product.qty_ordered * product.special_price).toFixed(2);
                                            cart_product.total          = parseFloat(cart_product.qty_ordered * product.converted_special_price).toFixed(2);

                                            if ( product.tax_category_id ) {
                                                cart_product.tax_amount = parseFloat(((cart_product.qty_ordered * product.converted_special_price) * product.tax_percent) / 100).toFixed(2);

                                                cart_product.base_tax_amount = parseFloat(((cart_product.qty_ordered * product.special_price) * product.tax_percent) / 100).toFixed(2);
                                            }                                        

                                            cart_product.f_total    = window.pos_currency_symbol + parseFloat(cart_product.qty_ordered * product.converted_special_price).toFixed(2);
                                        } else {
                                            // Product Customer Group Price
                                            var customer_prices = {
                                                    price:              product.price,
                                                    converted_price:    product.converted_price,
                                                    formated_price:     product.formated_price,
                                                };
                                            
                                            customer_prices = thisthis.calculateCustomerGroupPrice(product, cart_product.qty_ordered);

                                            cart_product.price      = parseFloat(customer_prices.converted_price).toFixed(2);
                                            cart_product.f_price    = customer_prices.formated_price;

                                            cart_product.base_total = parseFloat(cart_product.qty_ordered * customer_prices.price).toFixed(2);
                                            cart_product.total      = parseFloat(cart_product.qty_ordered * customer_prices.converted_price).toFixed(2);

                                            if ( product.tax_category_id ) {
                                                cart_product.tax_amount = parseFloat(((cart_product.qty_ordered * customer_prices.converted_price) * product.tax_percent) / 100).toFixed(2);

                                                cart_product.base_tax_amount = parseFloat(((cart_product.qty_ordered * customer_prices.price) * product.tax_percent) / 100).toFixed(2);
                                            }
                                            
                                            cart_product.f_total    = window.pos_currency_symbol + parseFloat(cart_product.qty_ordered * customer_prices.converted_price).toFixed(2);
                                        }
                                        thisthis.$toast.success(thisthis.success_add_to_cart, {
                                            position: 'top-right',
                                            duration: 2000,
                                        });
                                    }
                                });
                            }

                            if ( !procuct_exist ) {
                                const cart_product              = {};
                                cart_product.active             = true;
                                cart_product.id                 = product.id;
                                cart_product.sku                = product.sku;
                                cart_product.type               = product.type;
                                cart_product.weight             = product.weight;
                                cart_product.total_weight       = (added_qty * product.weight);
                                cart_product.name               = product.name;
                                cart_product.tax_category_id    = product.tax_category_id;
                                cart_product.tax_percent        = product.tax_percent;
                                cart_product.tax_amount         = 0;
                                cart_product.base_tax_amount    = 0;
                                cart_product.qty_ordered        = added_qty;
                                
                                if ( thisthis.localObject.pos_current_booking && Object.keys(thisthis.localObject.pos_current_booking).length > 0 ) {
                                    cart_product.booking_id = thisthis.localObject.pos_current_booking.booking_id;
                                }

                                if (product.special_price && product.special_price != 'NaN') {
                                    cart_product.base_price = parseFloat(product.special_price).toFixed(2);
                                    cart_product.price      = parseFloat(product.converted_special_price).toFixed(2);
                                    cart_product.f_price    = product.formated_special_price;
                                    cart_product.base_total = parseFloat(added_qty * product.special_price).toFixed(2);
                                    cart_product.total      = parseFloat(added_qty * product.converted_special_price).toFixed(2);

                                    if ( product.tax_category_id ) {
                                        cart_product.tax_amount         = parseFloat(((added_qty * product.converted_special_price) * product.tax_percent) / 100).toFixed(2);
                                        cart_product.base_tax_amount    = parseFloat(((added_qty * product.special_price) * product.tax_percent) / 100).toFixed(2);
                                    }

                                    cart_product.f_total = window.pos_currency_symbol + parseFloat(added_qty * product.converted_special_price).toFixed(2);
                                } else {
                                    // Product Customer Group Price
                                    var customer_prices = {
                                            price:              product.price,
                                            converted_price:    product.converted_price,
                                            formated_price:     product.formated_price,
                                        };
                                        
                                    customer_prices = thisthis.calculateCustomerGroupPrice(product, added_qty);

                                    cart_product.base_price = parseFloat(customer_prices.price).toFixed(2);
                                    cart_product.price      = parseFloat(customer_prices.converted_price).toFixed(2);
                                    cart_product.f_price    = customer_prices.formated_price;

                                    cart_product.base_total = parseFloat(added_qty * customer_prices.price).toFixed(2);
                                    cart_product.total      = parseFloat(added_qty * customer_prices.converted_price).toFixed(2);

                                    if (product.tax_category_id) {
                                        cart_product.tax_amount         = parseFloat(((added_qty * customer_prices.converted_price) * product.tax_percent) / 100).toFixed(2);
                                        cart_product.base_tax_amount    = parseFloat(((added_qty * customer_prices.price) * product.tax_percent) / 100).toFixed(2);
                                    }

                                    cart_product.f_total    = window.pos_currency_symbol + parseFloat(added_qty * customer_prices.converted_price).toFixed(2);
                                }

                                let index = Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length;

                                if (index == 0) {
                                    thisthis.$set(thisthis.pos_carts[thisthis.pos_current_cart], index, cart_product);
                                } else {
                                    let new_index = (parseInt(Object.keys(thisthis.pos_carts[thisthis.pos_current_cart])[index-1]) + parseInt(1));
                                    
                                    thisthis.$set(thisthis.pos_carts[thisthis.pos_current_cart], new_index, cart_product);
                                }

                                this.$toast.success(thisthis.success_add_to_cart, {
                                    position: 'top-right',
                                    duration: 2000,
                                });
                            }
                            EventBus.$emit('onshowCartContent');
                            EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(thisthis.pos_carts)});
                        } else {
                            this.$toast.error(this.$t('pos_home.pos_products.error.no_quantity'), {
                                position: 'top-right',
                                duration: 2000,
                            });
                        }

                        if (this.search_opt == 0) {
                            this.barcode_text = '';
                            $('#nav-search').val('');
                            $('#nav-search').focus();
                        }
                    }
                }
            },

            addProductToBooking(booking) {
                var self = this;
                var card_exists = null;
                
                $.each(self.pos_carts, (cardIndex, cartProducts) => {
                    var resultObject = this.searchBooking(booking.booking_id, cartProducts);
                        if (typeof  resultObject !== 'undefined') {
                            card_exists = cardIndex;
                        }
                });

                if ( card_exists === null ) {
                    this.current_booking = booking;
                    this.addNewCart();
                } else {
                    this.pos_current_cart = card_exists;
                    this.localObject.pos_current_cart = card_exists;
                    EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': this.pos_current_cart});
                }
            },

            calculateCustomerGroupPrice(product, qty) {
                var self = this;
                var lastValueObj = {
                    qty:                1,
                    customerGroupId:    null,
                    price:              product.price,
                    converted_price:    product.converted_price,
                    formated_price:     product.formated_price,
                };

                var customer_group_id = 0;
                if ( self.localObject.pos_cart_customer.email ) {
                    customer_group_id = self.localObject.pos_cart_customer.customer_group_id;
                } else {
                    $.each(self.localObject.pos_customer_groups, function(key, customer_group) {
                        if ( customer_group.code == 'guest' ) {
                            customer_group_id = customer_group.id;
                        }
                    });
                }

                if (! qty || qty == 0) {
                    qty = 1;
                }

                var group_price_count = Object.keys(product.customerGroupPrices).length;
                if ( group_price_count <= 0) {
                    return lastValueObj;
                }

                $.each(product.customerGroupPrices, function(key, group_price) {
                    if ( (group_price.customer_group_id == customer_group_id) || !group_price.customer_group_id ) {


                        if ( (group_price.customer_group_id && group_price.customer_group_id != customer_group_id)
                            || (qty < group_price.qty)
                            || (group_price.qty < lastValueObj.qty)
                        ) {
                            return true;
                        }

                        if ( group_price.qty == lastValueObj.qty
                            && lastValueObj.customerGroupId != null
                            && group_price.customer_group_id == null
                        ) {
                            return true;
                        }

                        if ( group_price.value <= lastValueObj.price && group_price.value_type == 'fixed' ) {
                            lastValueObj.price = group_price.value;
                            lastValueObj.converted_price = group_price.converted_value;
                            lastValueObj.formated_price = self.current_currency_symbol + parseFloat(lastValueObj.converted_price).toFixed(2);
                            lastValueObj.qty = group_price.qty;
                            lastValueObj.customerGroupId = group_price.customer_group_id;
                        } else {
                            if ( group_price.value_type == 'discount' && group_price.value <= 100 ) {
                                var discounted_price = product.price - (product.price * group_price.value) / 100;

                                if ( discounted_price <= lastValueObj.price ) {

                                    lastValueObj.price = discounted_price;
                                    lastValueObj.converted_price = product.converted_price - (product.converted_price * group_price.value) / 100;
                                    lastValueObj.formated_price = self.current_currency_symbol + parseFloat(lastValueObj.converted_price).toFixed(2);
                            
                                    lastValueObj.qty = group_price.qty;
                                    lastValueObj.customerGroupId = group_price.customer_group_id;
                                }
                            }
                        }
                    }
                });

                return lastValueObj;
            },

            validateProductRemainingQty(product) {
                var thisthis            = this;
                var total_quantities    = [];
                var bundle_quantities   = [];
                var product_qty         = JSON.parse(product.quantity);
                if ( product.pos_qty ) {
                    var product_qty     = JSON.parse(product.pos_qty);
                }
                var remaining_quantity  = product_qty[product.id];

                if ( product.type == 'bundle' ) {
                    total_quantities    = thisthis.getBundleProductQty(product, 'total');
                    bundle_quantities   = thisthis.getBundleProductQty(product, 'ordered');
                }

                if ( Object.keys(thisthis.pos_carts[thisthis.pos_current_cart]).length > 0 ) {
                    $.each(thisthis.pos_carts[thisthis.pos_current_cart], function(key, cart_product) {

                        if ( product.type != 'bundle' ) {
                            if ( cart_product.type == 'simple' || cart_product.type == 'virtual' || cart_product.type == 'configurable' || cart_product.type == 'booking' ) {
                                if ( cart_product.id == product.id ) {
                                    if ( cart_product.type == 'booking' ) {
                                        var getStatus = thisthis.compareBookingOptions(product.additional, cart_product.additional);
                                        if ( getStatus == true ) {
                                            remaining_quantity = remaining_quantity - parseInt(cart_product.qty_ordered);    
                                        }
                                    } else {
                                        remaining_quantity = remaining_quantity - parseInt(cart_product.qty_ordered);
                                    }
                                }
                            } else if (cart_product.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(cart_product, 'ordered');
                                
                                if ( quantities[product.id] ) {
                                    remaining_quantity = remaining_quantity - (parseInt(quantities[product.id]) * parseInt(cart_product.qty_ordered));
                                }
                            }   
                        } else {
                            if ( cart_product.type == 'simple' || cart_product.type == 'virtual' || cart_product.type == 'configurable' ) {
                                if ( bundle_quantities[cart_product.id] ) {
                                    bundle_quantities[cart_product.id] = parseInt(bundle_quantities[cart_product.id]) + parseInt(cart_product.qty_ordered);
                                }
                            } else if (cart_product.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(cart_product, 'ordered');

                                for (const product_id in bundle_quantities) {
                                    if ( bundle_quantities[product_id] && quantities[product_id] ) {
                                        bundle_quantities[product_id] = parseInt(bundle_quantities[product_id]) + (parseInt(quantities[product_id]) * parseInt(cart_product.qty_ordered));
                                    }
                                }
                            }
                        }
                    });
                }

                if (Object.keys(thisthis.localObject.pos_offline_orders).length > 0 && parseInt(remaining_quantity) > 0 ) {
                    $.each(thisthis.localObject.pos_offline_orders, function(key, offlineOrder) {
                        
                        if ( product.type != 'bundle' ) {
                            if ( offlineOrder.type == 'simple' || offlineOrder.type == 'virtual' || offlineOrder.type == 'configurable' || offlineOrder.type == 'booking' ) {
                                if ( offlineOrder.id == product.id ) {
                                    if ( offlineOrder.type == 'booking' ) {
                                        var getStatus = thisthis.compareBookingOptions(product.additional, offlineOrder.additional);
                                        if ( getStatus == true ) {
                                            remaining_quantity = remaining_quantity - parseInt(offlineOrder.qty_ordered);    
                                        }
                                    } else {
                                        remaining_quantity = remaining_quantity - parseInt(offlineOrder.qty_ordered);
                                    }
                                }
                            } else if (offlineOrder.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(offlineOrder, 'ordered');
                                
                                if ( quantities[product.id] ) {
                                    remaining_quantity = remaining_quantity - (parseInt(quantities[product.id]) * parseInt(offlineOrder.qty_ordered));
                                }
                            }   
                        } else {
                            if ( offlineOrder.type == 'simple' || offlineOrder.type == 'virtual' || offlineOrder.type == 'configurable' ) {
                                if ( bundle_quantities[offlineOrder.id] ) {
                                    bundle_quantities[offlineOrder.id] = parseInt(bundle_quantities[offlineOrder.id]) + parseInt(offlineOrder.qty_ordered);
                                }
                            } else if (offlineOrder.type == 'bundle') {
                                var quantities = thisthis.getBundleProductQty(offlineOrder, 'ordered');

                                for (const product_id in bundle_quantities) {
                                    if ( bundle_quantities[product_id] && quantities[product_id] ) {
                                        bundle_quantities[product_id] = parseInt(bundle_quantities[product_id]) + parseInt(quantities[product_id]);
                                    }
                                }
                            }
                        }
                    });
                }

                if ( product.type == 'bundle' ) {
                    for (const product_id in total_quantities) {
                        if ( parseInt(total_quantities[product_id]) > 0 ) {
                            if ( parseInt(bundle_quantities[product_id]) <= 0 || parseInt(bundle_quantities[product_id]) > parseInt(total_quantities[product_id]) ) {
                                return remaining_quantity = 0;
                            }
                        } else {
                            return remaining_quantity = 0;
                        }
                    }
                }
                
                return remaining_quantity;
            },

            getBundleProductQty(cart_product, action_type) {
                var quantities = [];
                var additional = cart_product.additional;
                for (const optionId in additional.bundle_options ) {
                    if ( additional.bundle_options[optionId] ) {
                        
                        const optionProductIds = additional.bundle_options[optionId];
                        for (const key in optionProductIds) {
                            const optionProductId   = optionProductIds[key];
                            var attribte_option     = additional.attributes[optionId];
                            var cart_qty            = additional.bundle_option_qty[optionId];
                            var option_detail       = attribte_option.option_detail;
                            var option_products     = option_detail.option_products;
                            var option_product      = option_products[optionProductId];
                            var total_qty           = option_product.total_qty[option_product.product_id];

                            if ( option_detail.type == 'checkbox' || option_detail.type == 'multiselect' ) {
                                cart_qty = option_product.qty;
                            }
                            if ( option_product.product_id && quantities[option_product.product_id] ) {
                                if (action_type == 'total') {
                                    quantities[option_product.product_id] = parseInt(total_qty);
                                } else {
                                    quantities[option_product.product_id] = parseInt(quantities[option_product.product_id]) + parseInt(cart_qty);
                                }
                            } else {
                                if ( option_product.product_id ) {
                                    if (action_type == 'total') {
                                        quantities[option_product.product_id] = parseInt(total_qty);
                                    } else {
                                        quantities[option_product.product_id] = parseInt(cart_qty);
                                    }
                                }
                            }
                        }
                    }
                }
                return quantities;
            },

            addNewCart() {
                var self = this;
                
                self.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');
                
                if (self.pos_carts.length > 0 && self.pos_carts.length < 3 ) {
                    
                    var totalProducts = Object.keys(self.pos_carts[self.pos_current_cart]);

                    if (totalProducts.length > 0) {
                        
                        var maxCartKey  = 0;
                        var promiseReturn =  new Promise( (resolve, reject) => {
                            $(self.pos_carts).each( (key, val) => {
                                if ( key > maxCartKey ) {
                                    maxCartKey = key;
                                }
                            });
                            resolve(1);
                        });

                        promiseReturn.then( (resolveStatus) => {
                            if (maxCartKey != null) {
                                self.pos_current_cart = (maxCartKey + 1);
                                self.pos_carts[self.pos_current_cart] = {};
                                
                                this.$set(self.pos_carts, self.pos_current_cart, {});
                                
                                // show cart remove button
                                if (self.pos_carts.length > 1) {
                                    self.showMinusBtn = true;
                                }

                                self.localObject.pos_current_cart = self.pos_current_cart;
                                
                                EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(self.pos_carts)});
                                
                                EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': self.pos_current_cart});
                                
                                if (Object.keys(this.current_booking).length > 0) {
                                    self.localObject.pos_current_booking = this.current_booking;
                                    EventBus.$emit('setLocalForage', {'key': 'pos_current_booking', 'data': JSON.stringify(this.current_booking)});
                                }
                            }
                        });
                    } else {
                        if (Object.keys(this.current_booking).length > 0) {
                            self.localObject.pos_current_booking = this.current_booking;
                            EventBus.$emit('setLocalForage', {'key': 'pos_current_booking', 'data': JSON.stringify(this.current_booking)});
                        }
                    }
                } else {
                    this.$toast.warning(this.error_plus_cart, {
                        position: 'top-right',
                        duration: 2000,
                    });
                }
            },

            holdCurrentCart(hold_order) {
                EventBus.$emit('resetCartDiscount');
                this.localObject.pos_discount = {};
                EventBus.$emit('deleteLocalForage', 'pos_discount');

                let length_holds = Object.keys(this.localObject.pos_holds).length;

                this.current_hold_cart = this.pos_carts[this.pos_current_cart];
                
                if (this.current_hold_cart && !this.current_hold_cart.hold_status && Object.keys(this.current_hold_cart).length > 0) {

                    this.current_hold_cart.hold_status = true;
                    this.current_hold_cart.hold_data = hold_order;

                    this.localObject.pos_holds[length_holds] = this.current_hold_cart;
                    this.pos_carts.splice(this.pos_current_cart, 1);

                    let length_carts = Object.keys(this.pos_carts).length;

                    if (length_carts == 0) {
                        this.pos_carts[0] = {};
                        this.pos_current_cart = 0;
                    } else {
                        this.pos_current_cart = 0;
                    }

                    this.countHoldTotal();

                    this.holdCartCount = Object.keys(this.localObject.pos_holds).length;
                    
                    EventBus.$emit('customerHoldCart');

                    EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(this.pos_carts)});

                    EventBus.$emit('setLocalForage', {'key': 'pos_current_cart', 'data': this.pos_current_cart});
                    
                    EventBus.$emit('setLocalForage', {'key': 'pos_holds', 'data': JSON.stringify(this.localObject.pos_holds)});

                    this.$root.toggleButtonDisable(false);

                    this.$toast.success(this.success_hold_cart, {
                        position: 'top-right',
                        duration: 2000,
                    });

                    EventBus.$emit('hideNoteModal', 'holdCart');
                }
            },
            
            barcodeSearch(event) {
                var thisthis = this;
                let found_status = false;
                if (event.keyCode == 13) {
                    if (thisthis.barcode_text.length > 0 && (Object.keys(thisthis.localObject.pos_products).length > 0)) {

                        $.each(thisthis.localObject.pos_products, (key, product) => {
                            if (product.product_barcode && product.product_barcode === thisthis.barcode_text) {
                                found_status = true;

                                EventBus.$emit('hideCommonModal', 'barcodeModal');

                                if ( product.type == 'configurable' ) {
                                    EventBus.$emit('showVariantModal', product);
                                } else {
                                    thisthis.addProductToCart(product);
                                }
                            }
                        });

                        if (!found_status) {
                            thisthis.barcode_text = '';
                            $('#nav-search').val('');
                            $('#nav-search').focus();
                            thisthis.$toast.error(thisthis.no_barcode_product, {
                                position: 'top-right',
                                duration: 2000,
                            });
                        }
                    } else {
                        thisthis.$toast.error(thisthis.no_barcode_provided, {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                }
                
            },

            checkOrderProduct(orderItems, product) {
                var orderProductQty = 0;
                $.each(orderItems, function(key, orderProduct) {
                    if ( parseInt(orderProduct.id) == parseInt(product.id) ) {
                        orderProductQty = orderProductQty + orderProduct.qty_ordered;
                    }
                });
                return orderProductQty;
            },

            emptyCurrentCart() {
                var self = this;
                EventBus.$emit('getLocalForage', 'pos_current_cart');
                if (Object.keys(self.pos_carts).length > 0) {
                    // self.pos_carts.splice(self.pos_current_cart, 1);

                    // EventBus.$emit('setLocalForage', {'key': 'pos_carts', 'data': JSON.stringify(self.pos_carts)});
                } 
            },

            compareBundleOptions(options1, options2) {
                if (! options1 || !options2 ) {
                    return false;
                }
                if ( options1.product_id != options2.product_id ) {
                    return false;
                }

                var is_bundle_options_same  = false;
                if ( Object.keys(options1.bundle_options).length == Object.keys(options2.bundle_options).length ) {
                    
                    $.each(options1.bundle_options, function(index, element) {
                        var element2 = options2.bundle_options[index];

                        if ( element2 ) {
                            var is_option_product_id_same = false;
                            if ( Object.keys(element).length === Object.keys(element2).length ) {
                                
                                $.each(element, function (key, option_product_id) {
                                    if ( option_product_id === element2[key] ) {
                                        return is_option_product_id_same = true;
                                    }
                                });
                            }

                            if ( is_option_product_id_same == true) {
                                return is_bundle_options_same  = true;
                            } else {
                                return is_bundle_options_same  = false;
                            }
                        } else {
                            return is_bundle_options_same  = false;
                        } 
                    });

                    // options1.bundle_options.every(function(element, index) {
                    //     var element2 = options2.bundle_options[index];
                        
                    //     if ( element2 ) {
                    //         var is_option_product_id_same = element.length === element2.length  && element.every(function(option_product_id, key) {
                    //             return option_product_id === element2[key];
                    //         });

                    //         if ( is_option_product_id_same == true) {
                    //             return true;
                    //         } else {
                    //             return false;
                    //         }
                    //     } else {
                    //         return false;
                    //     } 
                    // });
                }
                
                var is_bundle_options_same  = false;
                if ( Object.keys(options1.bundle_options).length == Object.keys(options2.bundle_options).length ) {
                    $.each(options1.bundle_options, function(index, element) {
                        var element2 = options2.bundle_options[index];

                        if ( element2 ) {
                            var is_option_product_id_same = false;
                            if ( Object.keys(element).length === Object.keys(element2).length ) {
                                
                                $.each(element, function(key, option_product_id) {
                                    if ( option_product_id === element2[key] ) {
                                        return is_option_product_id_same = true;
                                    }
                                });
                            }

                            if ( is_option_product_id_same == true) {
                                return is_bundle_options_same  = true;
                            } else {
                                return is_bundle_options_same  = false;
                            }
                        } else {
                            return is_bundle_options_same  = false;
                        } 
                    });
                }

                // var is_bundle_options_same  = (options1.bundle_options.length == options2.bundle_options.length) && options1.bundle_options.every(function(element, index) {
                //     var element2 = options2.bundle_options[index];
                    
                //     if ( element2 ) {
                //         var is_option_product_id_same = element.length === element2.length  && element.every(function(option_product_id, key) {
                //             return option_product_id === element2[key];
                //         });

                //         if ( is_option_product_id_same == true) {
                //             return true;
                //         } else {
                //             return false;
                //         }
                //     } else {
                //         return false;
                //     } 
                // });
                
                if ( is_bundle_options_same == true ) {
                    var is_bundle_options_qty_same = false;
                    
                    if ( Object.keys(options1.bundle_option_qty).length == Object.keys(options2.bundle_option_qty).length ) {
                        
                        $.each(options1.bundle_option_qty, function(index, element) {
                            var element2 = options2.bundle_option_qty[index];

                            if ( element2 ) {
                                var is_option_product_qty_same = false;
                                
                                if ( typeof element === 'object' && element !== null ) {
                                    if ( Object.keys(element).length === Object.keys(element2).length ) {
                                        
                                        $.each(element, function(key, option_product_qty) {
                                            if ( option_product_qty === element2[key] ) {
                                                return is_option_product_qty_same = true;
                                            }
                                        });
                                    }
                                } else {
                                    if ( element === element2 ) {
                                        is_option_product_qty_same = true;
                                    }
                                }

                                if ( is_option_product_qty_same == true) {
                                    return is_bundle_options_qty_same = true;
                                } else {
                                    return is_bundle_options_qty_same = false;
                                }
                            } else {
                                return is_bundle_options_qty_same = false;
                            } 
                        });
                    }

                    // var is_bundle_options_qty_same = (options1.bundle_option_qty.length == options2.bundle_option_qty.length) && options1.bundle_option_qty.every(function(element, index) {
                    //     var element2 = options2.bundle_option_qty[index];
                    //     if ( element2 ) {
                    //         var is_option_product_qty_same = element.length === element2.length  && element.every(function(option_product_qty, key) {
                    //             return option_product_qty === element2[key];
                    //         });
                    //         if ( is_option_product_qty_same == true) {
                    //             return true;
                    //         } else {
                    //             return false;
                    //         }
                    //     } else {
                    //         return false;
                    //     }
                    // });

                    return is_bundle_options_qty_same;
                } else {
                    return false;
                }
            },

            compareDownloadOptions(options1, options2) {
                var result = true;
                if (! options1 || !options2 ) {
                    return result = false;
                }
                if ( options1.product_id != options2.product_id ) {
                    return result = false;
                }
                if (Object.keys(options1.links).length == Object.keys(options2.links).length) {
                    $.each(options1.links, function(element1, index) {
                        var element2 = options2.links[index];
                        
                        if ( parseInt(element1) != parseInt(element2) ) {
                            result = false;
                        } 
                    });
                } else {
                    result = false;
                }

                return result;
            },

            compareBookingOptions(option1, option2) {
                var result = true;

                if (! option1 || !option2 ) {
                    return result = false;
                }
                if ( option1.product_id != option2.product_id ) {
                    return result = false;
                }
                if (Object.keys(option1.booking).length == Object.keys(option2.booking).length) {
                    // rental booking check
                    if ( option1.booking.renting_type ) {
                        if ( option1.booking.renting_type === option2.booking.renting_type ) {
                            if ( option1.booking.renting_type == 'daily' ) {
                                if ( (option1.booking.date_from !== option2.booking.date_from) || (option1.booking.date_to !== option2.booking.date_to)) {
                                    result = false;
                                }
                            } else {
                                if ( option1.booking.date === option2.booking.date ) {
                                    if ( (option1.booking.slot.from !== option2.booking.slot.from) || (option1.booking.slot.to !== option2.booking.slot.to) ) {
                                        result = false;
                                    }
                                } else {
                                    result = false;
                                }
                            }
                        } else {
                            result = false;
                        }
                    } else {                            
                        if ( option1.booking.slot ) {
                            // default & appointment booking check
                            if ( option1.booking.slot !== option2.booking.slot) {
                                result = false;
                            } else {
                                // table booking check
                                if ( option1.booking.date && (option1.booking.date !== option2.booking.date)) {
                                    result = false;
                                }
                            }
                        }
                        // event booking check
                        if ( option1.booking.ticket_id ) {
                            if ( option1.booking.ticket_id !== option2.booking.ticket_id) {
                                result = false;
                            }
                        }
                    }
                } else {
                    result = false;
                }

                return result;
            }
        }
    }
</script>
