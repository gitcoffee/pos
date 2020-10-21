
<template>
    <div class="booking-information rental-booking" v-if="booking_options.type == 'rental'">
        <div class="heading">{{ $t('pos_home.pos_products.rent_an_item') }}</div>        
        <div class="book-slots">

            <div v-if="renting_type == 'daily_hourly'">
                <div class="control-group">
                    <label class="required">{{ $t('pos_home.pos_products.chosse_rent_options') }}</label>
                    
                    <span class="radio">
                        <input type="radio" id="daily-renting-type" v-validate="'required'" name="booking[renting_type]" value="daily" v-model="sub_renting_type" />
                        <label class="radio-view" for="daily-renting-type"></label>
                        {{ $t('pos_home.pos_products.daily_basis') }}
                    </span>

                    <span class="radio">
                        <input type="radio" id="hourly-renting-type" v-validate="'required'" name="booking[renting_type]" value="hourly" v-model="sub_renting_type" />
                        <label class="radio-view" for="hourly-renting-type"></label>
                        {{ $t('pos_home.pos_products.hourly_basis') }}
                    </span>
                    
                </div>
            </div>

            <div v-if="renting_type != 'daily' && sub_renting_type == 'hourly'">
                <div class="control-group-container">
                    <slots
                        :localObject="localObject"
                        :product="product"
                        :booking_options="booking_options"
                        @onSlotSelection="slotSelected($event)"
                    ></slots>

                    <div v-if="selected_slots.slots">
                        <div class="control-group slots" :class="[errors.has('booking[slot][from]') ? 'has-error' : '']">
                            <label class="required">{{ $t('pos_home.pos_products.rent_time_from') }}</label>
                            <select v-validate="'required'" name="booking[slot][from]" v-model="slot_from" class="control" data-vv-as="&quot;Rent Time From&quot;">
                                <option value="">{{ $t('pos_home.pos_products.select_rent_time') }}</option>
                                <option v-for="slot in selected_slots.slots" :value="slot.from_timestamp">
                                    {{ slot.from }}
                                </option>
                            </select>

                            <span class="control-error" v-if="errors.has('booking[slot][from]')">{{ errors.first('booking[slot][from]') }}</span>
                        </div>
                        
                        <div class="control-group slots" :class="[errors.has('booking[slot][to]') ? 'has-error' : '']">
                            <label class="required">{{ $t('pos_home.pos_products.rent_time_to') }}</label>
                            <select v-validate="'required'" name="booking[slot][to]" class="control" data-vv-as="&quot;Rent Time To&quot;">
                                <option value="">{{ $t('pos_home.pos_products.select_rent_time') }}</option>

                                <option v-for="slot in selected_slots.slots" :value="slot.to_timestamp" v-if="slot_from < slot.to_timestamp">
                                    {{ slot.to }}
                                </option>
                            </select>

                            <span class="control-error" v-if="errors.has('booking[slot][to]')">{{ errors.first('booking[slot][to]') }}</span>
                        </div>
                    </div>

                </div>
            </div>

            <div v-else>
                <div class="control-group-container">
                    <div class="control-group date" :class="[errors.has('booking[date_from]') ? 'has-error' : '']">
                        <label class="required">{{ $t('pos_home.pos_products.select_from_date') }}</label>

                        <booking-date>
                            <input type="text" v-validate="'required|before_or_equal:date_to'" name="booking[date_from]" v-model="date_from" class="control" :data-vv-as="&quot;From Date&quot;" :placeholder="$t('pos_home.pos_products.placeholder_from')" ref="date_from" data-min-date="today"/>
                        </booking-date>

                        <span class="control-error" v-if="errors.has('booking[date_from]')">{{ errors.first('booking[date_from]') }}</span>
                    </div>

                    <div class="control-group date" :class="[errors.has('booking[date_to]') ? 'has-error' : '']">
                        <label class="required">{{ $t('pos_home.pos_products.select_to_date') }}</label>
                        
                        <booking-date>
                            <input type="text" v-validate="'required|after_or_equal:date_from'" name="booking[date_to]" v-model="date_to" class="control" :data-vv-as="&quot;To Date&quot;" :placeholder="$t('pos_home.pos_products.placeholder_to')" ref="date_to" data-min-date="today"/>
                        </booking-date>

                        <span class="control-error" v-if="errors.has('booking[date_to]')">{{ errors.first('booking[date_to]') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        inject: ['$validator'],

        props: ['localObject', 'product', 'booking_options'],
        
        data() {
            return {
                renting_type: '',
                sub_renting_type: 'hourly',
                slots: [],
                selected_slot: '',
                slot_from: '',
                date_from: '',
                date_to: '',
                selected_slots: [],
            }
        },

        created() {
            var self = this;
            this.renting_type = this.booking_options.rental_slot.renting_type;

            this.$validator.extend('after_or_equal', {
                getMessage(field, val) {
                    return 'The "To" must be equal or after "From"';
                },

                validate(value, field) {
                    if (! self.date_from) {
                        return true;
                    }

                    var from = new Date(self.date_from);
                    var to = new Date(self.date_to);

                    return from <= to;
                }
            });

            this.$validator.extend('before_or_equal', {
                getMessage(field, val) {
                    return 'The "From must be equal or before "To"';
                },

                validate(value, field) {
                    if (! self.date_to) {
                        return true;
                    }

                    var from = new Date(self.date_from);
                    var to = new Date(self.date_to);

                    return from <= to;
                }
            });
        },

        methods: {
            slotSelected: function(slot_details) {
                if ( Object.keys(slot_details.slots).length > 0 ) {
                    this.slots = slot_details.slots;
                    if ( (slot_details.selected_slot >= 0) && slot_details.slots[slot_details.selected_slot] ) {
                        this.selected_slot = slot_details.selected_slot;
                        this.selected_slots = slot_details.slots[slot_details.selected_slot];
                    }
                }
            }
        }        
    }
</script>