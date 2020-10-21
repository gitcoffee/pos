<template>
    <div>
        <div class="table-create-panel" v-if="isUserLogin">
            <div class="message-alert warning" v-if="pos_offline">
                {{ $t('pos_view.error_offline_mode') }}
            </div>

            <div class="table-list-header">
                <div class="heading">
                    <h3>{{ $t('pos_restaurant.menu_content.table_create.heading') }}</h3>
                </div>
            </div>
            
            <div class="pos-table-form">
                <div class="message-alert warning" v-if="total_tables >= allow_table_create">
                    {{ $t('pos_restaurant.menu_content.table_create.error_table_create_access', {
                        table_count: allow_table_create
                    }) }}
                </div>

                <form autocomplete="off" @submit.prevent="tableAction" method="POST">
                    <div class="page-content">
                        <div class="form-container">

                            <div class="pos-table-fields">
                                <div class="control-group" :class="[this.errors.has('name') ? 'has-error' : '']">
                                    <label for="name" class="required" v-text="$t('pos_restaurant.menu_content.table_create.table_name')"></label>
                                    <input type="text" class="control" name="name" v-model="table_name" v-validate="'required|max:50'"  >
                                    <span class="control-error" v-if="this.errors.has('name')">{{ this.errors.first('name') }}</span>
                                </div>

                                <div class="control-group" :class="[this.errors.has('type') ? 'has-error' : '']">
                                    <label for="type" v-text="$t('pos_restaurant.menu_content.table_create.table_type')"></label>

                                    <select name="type" v-validate="'required'" class="control" v-model="table_type" >
                                        <option :value="tableType" v-for="(tableType, index) in table_types" :key="index"> {{ tableType|capitalize }} </option>
                                    </select>
                                    <span class="control-error" v-if="this.errors.has('type')">{{ this.errors.first('type') }}</span>
                                </div>

                                <div class="control-group">
                                    <label for="type" v-text="$t('pos_restaurant.menu_content.table_create.table_status')"></label>

                                    <select name="status" class="control" v-model="table_status" >
                                        <option value="1" >{{ $t('pos_restaurant.menu_content.table_create.active') }}</option>
                                        <option value="0" >{{ $t('pos_restaurant.menu_content.table_create.inactive') }}</option>
                                    </select>
                                </div>

                                <div class="control-group" :class="[this.errors.has('position') ? 'has-error' : '']">
                                    <label for="position" class="required" v-text="$t('pos_restaurant.menu_content.table_create.table_position')"></label>
                                    <input type="text" class="control" name="position" v-model="position" v-validate="'required|numeric'" >
                                    <span class="control-error" v-if="this.errors.has('position')">{{ this.errors.first('position') }}</span>
                                </div>

                                <div class="control-group" :class="[this.errors.has('no_of_seat') ? 'has-error' : '']">
                                    <label for="no_of_seat" class="required" v-text="$t('pos_restaurant.menu_content.table_create.no_of_seat')"></label>
                                    <input type="text" class="control" name="no_of_seat" v-model="seat" v-validate="'required|numeric|min_value:1|max_value:20'" >
                                    <span class="control-error" v-if="this.errors.has('no_of_seat')">{{ this.errors.first('no_of_seat') }}</span>
                                </div>

                                <div class="pos-action text-center">
                                    <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_restaurant.menu_content.table_create.button_save')"  v-if="allow_table_create > total_tables"> {{ $t('pos_restaurant.menu_content.table_create.button_save') }} </button>
                                    
                                    <button class="btn btn-lg btn-pos-primary" type="submit" :text="$t('pos_restaurant.menu_content.table_create.button_save')" disabled="disabled" v-else > {{ $t('pos_restaurant.menu_content.table_create.button_save') }} </button>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['localObject'],
        data() {
            return {
                user_id: 0,
                pos_offline: 0,
                table_name: '',
                table_type: '',
                table_status: 1,
                position: null,
                seat: null,
                table_types: window.config_restaurant.table_shape.split(","),
                allow_table_create: window.config_restaurant.agent_table_max,
                total_tables: 0,
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

        mounted() {
            this.getAgentTotalTables();
        },

        methods: {
            checkUserLogin() {
                this.pos_offline = this.$root.$root.offline;

                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },

            getAgentTotalTables() {
                var self = this;
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    if ( this.localObject.pos_restaurant_tables ) {
                        this.total_tables = Object.keys(this.localObject.pos_restaurant_tables).length;
                    }
                } else {
                    self.$http.get('/api/pos/auth/getTables', {
                        params: {
                            page:       1,
                            user_id:    self.localObject.pos_cashier.id
                        }
                    })
                    .then((response)  =>  {
                        if (response.data.data && response.data.data.length > 0) {
                            self.total_tables = response.data.meta.total;
                        } else {
                            self.total_tables = 0;
                        }
                    })
                    .catch(function (error) {});
                }
            },

            tableAction () {
                this.pos_offline = this.$root.$root.offline;
                if (this.pos_offline) {
                    this.$toast.error(this.$t('pos_view.error_offline_action'), {
                        position: 'top-right',
                        duration: 2000,
                    });
                } else {
                    var self = this;
                    if ( this.allow_table_create > this.total_tables) {
                        this.$root.toggleButtonDisable(true);
                        
                        this.$validator.validateAll().then(result => {
                            if (result) {
                                this.$http.post('/api/pos/auth/addTable', {
                                    agent_id: self.user_id,
                                    name: self.table_name,
                                    type: self.table_type,
                                    status: self.table_status,
                                    position: self.position,
                                    no_of_seat: self.seat,
                                })
                                .then((response)  =>  {
                                    if (! response.data.status) {
                                        this.$toast.error(response.data.message, {
                                            position: 'top-right',
                                            duration: 2000,
                                        });
                                        this.$root.toggleButtonDisable(false);
                                    } else {
                                        this.$toast.success(response.data.message, {
                                            position: 'top-right',
                                            duration: 2000,
                                        });
                                        
                                        this.$root.toggleButtonDisable(false);
                                        this.$router.push({name: 'pos_restaurant_table_list'});
                                    }
                                })
                                .catch(function (error) {
                                    if (error.response.data.errors) {
                                        var table_errors = error.response.data.errors;
                                        if ( Object.keys(table_errors).length > 0) {
                                            $.each(table_errors, (key, data) => {
                                                $.each(data, (key, value) => {
                                                    self.$toast.error(value, {
                                                        position: 'top-right',
                                                        duration: 2000,
                                                    });
                                                    
                                                    self.$root.toggleButtonDisable(false);
                                                });
                                            });
                                        }
                                    }
                                });
                            } else {
                                this.$root.toggleButtonDisable(false);
                            }
                        });
                    } else {
                        self.$root.toggleButtonDisable(true);

                        this.$toast.error(this.$t('pos_restaurant.menu_content.table_create.error_table_create_access', { table_count: self.allow_table_create }), {
                            position: 'top-right',
                            duration: 2000,
                        });
                    }
                }
            },
        }
    }
</script>