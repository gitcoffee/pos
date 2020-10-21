
<template>
    <div class="book-slots" v-if="isUserLogin">
        
        <div class="control-group date" :class="[errors.has('booking[date]') ? 'has-error' : '']" >
            <label class="required">{{ $t('pos_home.pos_products.book_date') }}</label>
            
            <booking-date @onChange="dateSelected($event)">
                <input type="text" v-validate="'required'" name="booking[date]" class="control"  data-vv-as="&quot;Date&quot;" v-model="booking_date" />
            </booking-date>

            <span class="control-error" v-if="errors.has('booking[date]')">{{ errors.first('booking[date]') }}</span>
        </div>

        <div class="control-group slots" :class="[errors.has('booking[slot]') ? 'has-error' : '']" >
            <label class="required">{{ $t('pos_home.pos_products.book_slot') }}</label>
            
            <select v-validate="'required'" name="booking[slot]" class="control" :data-vv-as="&quot;Slot&quot;" v-model="booking_slot" @change="slotSelected" v-if="booking_options.type !== 'rental' ">
                <option v-for="(slot, index) in slots" :value="slot.timestamp" :key="index">{{ slot.from + ' - ' + slot.to }}</option>
            </select>

            <select v-validate="'required'" name="booking[slot]" class="control" :data-vv-as="&quot;Slot&quot;" v-model="booking_slot" @change="slotSelected" v-if="booking_options.type == 'rental' ">
                <option v-for="(slot, index) in slots" :value="index" :key="index">{{ slot.time }}</option>
            </select>

            <span class="control-error" v-if="errors.has('booking[slot]')">{{ errors.first('booking[slot]') }}</span>
        </div>
    </div>
</template>

<script>
    export default {
        inject: ['$validator'],

        props: ['localObject', 'product', 'booking_options'],

        data: function() {
            return {
                user_id: 0,
                slots: [],
                booking_date: '',
                booking_slot: '',
                pos_offline: 0,
            }
        },
        computed: {
            isUserLogin () {
                this.pos_offline = this.$root.$root.offline;

                this.checkUserLogin();
                return this.user_id;
            }
        },

        methods: {
            checkUserLogin() {
                if (this.localObject.pos_cashier.id) {
                    this.user_id = this.localObject.pos_cashier.id;
                }
            },

            dateSelected: function(date) {
                var thisthis                = this;
                var requested_date          = new Date(date);
                var day_index               = requested_date.getDay();
                    thisthis.pos_offline    = this.$root.$root.offline;

                    if ( thisthis.pos_offline ) {
                        switch (thisthis.booking_options.type) {
                            case 'default':
                                var default_slots = thisthis.booking_options.default_slot.slots;
                                if ( default_slots[date] ) {
                                    thisthis.slots    = default_slots[date];
                                }                        
                                break;
                            case 'appointment':
                                thisthis.slots = thisthis.getSlots(requested_date, thisthis.booking_options, thisthis.booking_options.appointment_slot);
                                break;
                            case 'rental':
                                thisthis.slots = thisthis.getSlots(requested_date, thisthis.booking_options, thisthis.booking_options.rental_slot);
                                break;
                            case 'table':
                                thisthis.slots = thisthis.getSlots(requested_date, thisthis.booking_options, thisthis.booking_options.table_slot);
                                break;
                        
                            default:
                                break;
                        }
                    } else {
                        if ( thisthis.booking_options.type == 'appointment' || thisthis.booking_options.type == 'table' ) {
                            if (thisthis.booking_options.type == 'appointment') {
                                thisthis.slots = thisthis.getSlots(requested_date, thisthis.booking_options, thisthis.booking_options.appointment_slot);
                            }
                            if (thisthis.booking_options.type == 'table') {
                                thisthis.slots = thisthis.getSlots(requested_date, thisthis.booking_options, thisthis.booking_options.table_slot);
                            }
                        } else {
                            thisthis.$http.get('/api/pos/auth/slots', {
                                params: {
                                    user_id: thisthis.user_id,
                                    booking_id: thisthis.booking_options.id,
                                    date: date
                                }
                            })
                            .then (function(response) {
                                thisthis.slots = response.data.data;

                                thisthis.errors.clear();
                            })
                            .catch (function (error) {});
                        }
                    }
            },

            slotSelected() {
                this.$emit('onSlotSelection',
                {
                    slots: this.slots,
                    selected_slot: this.booking_slot
                });
            },

            getFullDate(current_date_time) {

                var twoDigitMonth = ((current_date_time.getMonth().length + 1) === 1) ? (current_date_time.getMonth()+1) : '0' + (current_date_time.getMonth() + 1);

                var get_date = current_date_time.getFullYear() + '-' + twoDigitMonth + '-' + current_date_time.getDate();
                
                return get_date;
            },

            getSlots(requested_date, booking_options, bookingProductSlot) {
                var thisthis            = this;
                var available_slots     = [];
                var currentTime         = new Date(); 
                var requested_date_time = new Date(requested_date);
                var requestedDate       = this.getFullDate(requested_date_time);
                var day_index           = requested_date.getDay();
                
                if ( ! booking_options.available_every_week && booking_options.available_from ) {
                    var available_from = new Date(booking_options.available_from);
                    var availableFrom = this.getFullDate(available_from) + ' ' + available_from.getHours() + ':' + available_from.getMinutes() + ':' + available_from.getSeconds();    
                } else {
                    var availableFrom = this.getFullDate(new Date()) + ' 00:00:00';
                }

                if ( ! booking_options.available_every_week && booking_options.available_to ) {
                    var available_to = new Date(booking_options.available_to);
                    var availableTo = this.getFullDate(available_to) + ' ' + available_to.getHours() + ':' + available_to.getMinutes() + ':' + available_to.getSeconds();    
                } else {
                    var availableTo = this.getFullDate(new Date('2080-01-01')) + ' 00:00:00';
                }

                var timeDurations = bookingProductSlot.same_slot_all_days
                                ? bookingProductSlot.slots
                                : (bookingProductSlot.slots[day_index] ? bookingProductSlot.slots[day_index] : []);

                var fromTime    = new Date(availableFrom);
                var toTime      = new Date(availableTo);
                
                if (requested_date_time.getTime() < fromTime.getTime()
                    || requested_date_time.getTime() > toTime.getTime()
                ) {
                    return [];
                }
                
                $.each(timeDurations, function(key, timeDuration) {
                    var fromChunks  = timeDuration['from'].split(":");
                    var toChunks    = timeDuration['to'].split(":");

                    var startDayTime = new Date(requestedDate + ' 00:00:00');
                        startDayTime.setMinutes( startDayTime.getMinutes() + parseInt(fromChunks[0] * 60) + parseInt(fromChunks[1]) );

                    var tempStartDayTime = new Date(startDayTime.getTime());

                    var endDayTime = new Date(requestedDate + ' 00:00:00');
                        endDayTime.setMinutes( endDayTime.getMinutes() + parseInt(toChunks[0] * 60) + parseInt(toChunks[1]) );

                    var isFirstIteration    = true;
                    var all_slots           = [];
                    while (1) {
                        var from = new Date(tempStartDayTime.getTime());
                            
                        if ( booking_options.type == 'rental' ) {
                            tempStartDayTime.setMinutes( tempStartDayTime.getMinutes() + 60 );
                        } else {
                            tempStartDayTime.setMinutes( tempStartDayTime.getMinutes() + parseInt(bookingProductSlot.duration) );
                        }

                        if ( booking_options.type !== 'rental' ) {
                            if (isFirstIteration) {
                                isFirstIteration = false;
                            } else {
                                from.setMinutes( from.getMinutes() + parseInt(bookingProductSlot.break_time) );
                                tempStartDayTime.setMinutes( tempStartDayTime.getMinutes() + parseInt(bookingProductSlot.break_time) );
                            }
                        }
                        
                        var to = new Date(tempStartDayTime.getTime());

                        if (
                            (startDayTime.getTime() <= from.getTime()
                            && from.getTime() <= toTime.getTime())
                            &&
                            (toTime.getTime() >= to.getTime() && to.getTime() >= startDayTime.getTime())
                            &&
                            (startDayTime.getTime() <= from.getTime() && from.getTime() <= endDayTime.getTime())
                            &&
                            (endDayTime.getTime() >= to.getTime() && to.getTime() >= startDayTime.getTime())
                        ) {
                            // Get already ordered qty for this slot
                            var orderedQty = 0;
                            if ( timeDuration['qty'] ) {
                                var qty = timeDuration['qty'] - orderedQty;
                            } else {
                                var qty = 1;
                            }

                            if ( qty && currentTime.getTime() <= from.getTime() ) {
                                var slot_arr    = {};
                                var from_hours  = parseInt(from.getHours());
                                var to_hours    = parseInt(to.getHours());
                                var from_am_pm  = parseInt(from_hours) > 12 ? "PM" : "AM";
                                var to_am_pm    = parseInt(to_hours) > 12 ? "PM" : "AM";

                                if (parseInt(from_hours) > 12) {
                                    from_hours = parseInt(from_hours) - 12;
                                }
                                if (parseInt(from_hours) < 10 ) {
                                    from_hours = '0' + from_hours;
                                }
                                if (parseInt(to_hours) > 12) {
                                    to_hours = parseInt(to_hours) - 12;
                                }
                                if (parseInt(to_hours) < 10 ) {
                                    to_hours = '0' + to_hours;
                                }
                                
                                var from_minutes = from.getMinutes();
                                if ( parseInt(from_minutes) == 0 ) {
                                    from_minutes = '00';
                                } else if (parseInt(from_minutes) < 10 ) {
                                    from_minutes = '0' + from_minutes;
                                }
                                var to_minutes = to.getMinutes();
                                if ( parseInt(to_minutes) == 0 ) {
                                    to_minutes = '00';
                                } else if (parseInt(to_minutes) < 10 ) {
                                    to_minutes = '0' + to_minutes;
                                }
                                
                                slot_arr['from'] = from_hours + ":" + from_minutes + " " + from_am_pm;
                                slot_arr['to']   = to_hours + ":" + to_minutes + " " + to_am_pm;
                                slot_arr['timestamp']   = from.getTime() + '-' + to.getTime();
                                slot_arr['qty']         = qty;
                                slot_arr['from_hours']  = from_hours + ":" + from_minutes;
                                slot_arr['to_hours']    = to_hours + ":" + to_minutes;
                                slot_arr['from_timestamp']  = from.getTime();
                                slot_arr['to_timestamp']    = to.getTime();
                                
                                if ( booking_options.type == 'rental' ) {
                                    all_slots.push(slot_arr);
                                } else {
                                    available_slots.push(slot_arr);
                                }
                            }
                        } else {
                            break;
                        }
                    }

                    if ( booking_options.type == 'rental' ) {
                        var start_hours  = parseInt(startDayTime.getHours());
                        var end_hours    = parseInt(endDayTime.getHours());
                        var start_am_pm  = parseInt(start_hours) > 12 ? "PM" : "AM";
                        var end_am_pm    = parseInt(end_hours) > 12 ? "PM" : "AM";
                        if (parseInt(start_hours) > 12) {
                            start_hours = parseInt(start_hours) - 12;
                        }
                        if (parseInt(start_hours) < 10 ) {
                            start_hours = '0' + start_hours;
                        }
                        if (parseInt(end_hours) > 12) {
                            end_hours = parseInt(end_hours) - 12;
                        }
                        if (parseInt(end_hours) < 10 ) {
                            end_hours = '0' + end_hours;
                        }
                        
                        var start_minutes = startDayTime.getMinutes();
                        if ( parseInt(start_minutes) == 0 ) {
                            start_minutes = '00';
                        } else if (parseInt(start_minutes) < 10 ) {
                            start_minutes = '0' + start_minutes;
                        }
                        var end_minutes = endDayTime.getMinutes();
                        if ( parseInt(end_minutes) == 0 ) {
                            end_minutes = '00';
                        } else if (parseInt(end_minutes) < 10 ) {
                            end_minutes = '0' + end_minutes;
                        }
                        available_slots[key] = {
                            slots: all_slots,
                            time: start_hours + ":" + start_minutes + " " + start_am_pm + ' - ' + end_hours + ":" + end_minutes + " " + end_am_pm
                        };
                    }
                });
                
                return available_slots;
            },
        }    
    }
</script>