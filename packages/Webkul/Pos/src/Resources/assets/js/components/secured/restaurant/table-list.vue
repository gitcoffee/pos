<template>
    <div>
        <div class="table-list-panel" v-if="isUserLogin">
            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div> 

            <div class="table-list-header">
                <div class="heading">
                    <h3>{{ $t('pos_restaurant.menu_content.table_list.heading') }}</h3>
                </div>
                <div class="control-group search-bar">
                    <input type="text" name="search_bar" class="control search-table" id="table-search" v-model="filter_name" :placeholder="$t('pos_restaurant.menu_content.table_list.search_table_placeholder')" @keyup="searchPosTable" />
                </div>
            </div>
            <div class="table-row row-grid-4" v-if="pos_table_total">
                <div class="table-record-container row-layout" v-for="(table, index) in pos_restaurant_tables" :key="index">
                    <!-- <div class="full-booked" v-if="!table.no_of_seat"></div> -->
                    <div class="table-name">{{ table.position }} - {{ table.name }}</div>
                    <div class="table-type">
                        <span :class="table.type">{{ table.type|capitalize }}</span>
                    </div>
                    <div class="table-btn-group">
                        <span>{{ $t('pos_restaurant.menu_content.table_list.available_seat') }} {{ table.no_of_seat }}</span>
                        <div class="book-table btn btn-md btn-pos-dark" @click="showBookModal(table)">
                            <i class="fa fa-calendar"></i>
                            {{ $t('pos_restaurant.menu_content.table_list.book_table') }}
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="message-alert danger">
                {{ $t("pos_restaurant.error.no_table") }}
            </div>

            <div v-if="this.$root.posCommonModal.addBooking">
                <pos-common-modal id="addBooking" :is-open="this.$root.posCommonModal.addBooking">
                    <h4 slot="header">{{ $t('pos_restaurant.menu_content.table_list.add_booking', {position: table_data.position, table_name: table_data.name}) }}</h4>>
                    
                    <div slot="body">
                        <booking-form
                            :table_id='table_id'
                            :table_data='table_data'
                            :localObject='localObject'
                        ></booking-form>
                    </div>
                </pos-common-modal>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .pos-modal-container {
        width: 600px;

        .modal-body {
            max-height: 400px;
        }
    }

    .pos-container-wrapper .pos-content-container .pos-restaurant-main .pos-nav-content {
        padding: 0px;
        margin-top: 1px;
    }
</style>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                user_id: 0,
                pos_offline: 0,
                filter_name: '',
                pos_table_total: 0,
                page: 1,
                limit: 10,
                totalPage: 0,
                tableRequests: [],
                table_id: 0,
                table_data: {},
                pos_restaurant_tables: [],
            };
        },

        computed: {
            isUserLogin () {
                this.checkUserLogin();
                return this.user_id;
            }
        },

        filters: {
            capitalize: function (value) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        },

        created() {
            this.getAgentTables();
        },

        methods: {
            checkUserLogin() {
                this.pos_offline = this.$root.$root.offline;

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },

            showBookModal(table) {
                this.table_id = table.id;
                this.table_data = table;
                EventBus.$emit('showCommonModal', 'addBooking');
            },

            searchPosTable(event) {
                if ((event.keyCode > 48 && event.keyCode < 90) || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 46) {
                    this.page = 1;
                    this.pos_table_total = 0;
                    this.pos_restaurant_tables = [];
                    this.getAgentTables();
                }
            },

            getAgentTables() {
                var self = this;
                self.pos_offline = self.$root.$root.offline;
                
                if (self.localObject.pos_cashier.id) {
                    if (self.pos_offline) {
                        if (Object.keys(this.localObject.pos_restaurant_tables).length > 0) {
                            this.pos_table_total = Object.keys(this.localObject.pos_restaurant_tables).length;
                            
                            this.pos_restaurant_tables = this.localObject.pos_restaurant_tables;
                        } else {
                            this.pos_table_total = 0;
                            this.pos_restaurant_tables = [];
                        }
                    } else {
                        self.$http.get('/api/pos/auth/getTables', {
                            params: {
                                page:           self.page,
                                user_id:        self.localObject.pos_cashier.id,
                                filter_name:    self.filter_name,
                            }
                        })
                        .then((response)  =>  {
                            if (response.data.data && response.data.data.length > 0) {
                                self.totalPage  = response.data.meta.last_page;
                                self.pos_table_total = response.data.meta.total;
                                self.pos_restaurant_tables = response.data.data;
    
                                for (self.page = 2; self.page <= self.totalPage; self.page++) {
                                    self.tableRequests.push({
                                        url: '/api/pos/auth/getTables',
                                        method: 'get',
                                        async:   true,
                                        params: {
                                            page:        self.page,
                                            user_id:     self.localObject.pos_cashier.id,
                                            filter_name: self.filter_name,
                                        },
                                    });
                                }
                            } else {
                                self.totalPage = 0;
                                self.pos_table_total = 0;
                                self.tableRequests = {};
                            }
                        })
                        .catch(function (error) {})
                        .finally(() => self.NextRequest());
                    }
                }
            },

            NextRequest() {
                var self = this;
                if (self.tableRequests.length) {
                    self.$root.$http(self.tableRequests.shift())
                    .then(function(response) {
                        if (response.data.data && response.data.data.length > 0) {
                            self.pos_restaurant_tables = self.pos_restaurant_tables.concat(response.data.data);
                        }
                    })
                    .finally(() => self.NextRequest());
                } else {
                    self.localObject.pos_restaurant_tables = self.pos_restaurant_tables;

                    EventBus.$emit('setLocalForage', {'key': 'pos_restaurant_tables', 'data': JSON.stringify(self.pos_restaurant_tables)});
                }
            },
        }
    }
</script>