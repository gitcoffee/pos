
<template>
    <div class="booking-information" v-if="booking_options.type == 'appointment'">
        <div class="heading">{{ $t('pos_home.pos_products.book_appointment') }}</div>
        
        <div class="booking-info-row" v-if="booking_options.appointment_slot.duration">
            <i class="fa fa-calendar"></i>
            <span class="title">
                {{ $t('pos_home.pos_products.slot_duration') }}
                
                {{ $t('pos_home.pos_products.slot_minutes', {'minutes': booking_options.appointment_slot.duration}) }}
            </span>
        </div>
        <div class="booking-info-row">
            <i class="fa fa-calendar"></i>
            <span class="title">
                {{ $t('pos_home.pos_products.today_availability') }}
            </span>

            <span class="value">
                <span v-if="today_slots">
                    {{ today_slots }}
                </span>
                <span class="text-danger" v-else >
                    {{ $t('pos_home.pos_products.booking_closed') }}
                </span>
            </span>

            <div class="toggle" @click="showHidepanel">
                {{ $t('pos_home.pos_products.show_all_days') }}
                <i class="fa" :class="[! show_days_available ? 'fa-sort-down' : 'fa-sort-up']"></i>
            </div>

            <div class="days-availability" v-show="show_days_available">
                <table>
                    <tbody>
                        <tr v-for="(slot, index) in booking_slots" :key="index">
                            <td>{{ slot.name }}</td>

                            <td >
                                <div v-if="slot.slots && slot.slots.length">
                                    <span v-for="(slot_time, time_index) in slot.slots" :key="time_index">
                                        {{ slot_time.from + ' - ' + slot_time.to }}
                                    </span>
                                </div>
                                <div class="text-danger" v-else >
                                    {{ $t('pos_home.pos_products.booking_closed') }}
                                </div>                                
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <slots
            :localObject="localObject"
            :product="product"
            :booking_options="booking_options"
        ></slots>

    </div>
</template>

<script>
    export default {
        inject: ['$validator'],

        props: ['localObject', 'product', 'booking_options'],
        
        data() {
            return {
                today_slots: '',
                show_days_available: 0,
                booking_slots: [],
            }
        },

        created() {
            var current_date    = new Date();
            var day_index       = current_date.getDay();
            if ( Object.keys(this.booking_options.appointment_slot.slots).length > 0 ) {
                this.booking_slots  = this.booking_options.appointment_slot.slot_times;
                var slot_array      = this.booking_slots[day_index];
                
                if ( Object.keys(slot_array.slots).length > 0 ) {
                    for (const key in slot_array.slots) {
                        if ( this.today_slots ) {
                            this.today_slots = this.today_slots + ' | ' + slot_array.slots[key].from + ' - ' + slot_array.slots[key].to;
                        } else {
                            this.today_slots = slot_array.slots[key].from + ' - ' + slot_array.slots[key].to;
                        }
                    }
                }
            }
        },

        methods: {
            showHidepanel() {
                if ( this.show_days_available == 0 ) {
                    this.show_days_available = 1; 
                } else {
                    this.show_days_available = 0; 
                }
            }
        }        
    }
</script>