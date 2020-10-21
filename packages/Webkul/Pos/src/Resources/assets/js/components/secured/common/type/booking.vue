<template>
    <div class="booking-product-container" v-if="booking_type">
        <div class="location-information" v-if="booking_details.location">
            <div class="location-icon">
                <i class="fa fa-map-marker"></i>
                <span class="title">{{ $t('pos_home.pos_products.location') }}</span>
            </div>
            <div class="booking-location">
                <span class="value">{{ booking_details.location }}</span>
                <a :href="booking_location" target="_blank">{{ $t('pos_home.pos_products.view_on_map') }}</a>
            </div>
        </div>
        <form id="booking-app" autocomplete="off" @submit.prevent="validateOptions" method="POST">

            <div v-if="booking_details.type == 'default'">
                <default-booking
                    :localObject="localObject"
                    :product="product"
                    :booking_options="booking_details"
                ></default-booking>
            </div>
            <div v-if="booking_details.type == 'appointment'">
                <appointment-booking
                    :localObject="localObject"
                    :product="product"
                    :booking_options="booking_details"
                ></appointment-booking>
            </div>
            <div v-if="booking_details.type == 'event'">
                <event-booking
                    :localObject="localObject"
                    :product="product"
                    :booking_options="booking_details"
                ></event-booking>
            </div>
            <div v-if="booking_details.type == 'rental'">
                <rental-booking
                    :localObject="localObject"
                    :product="product"
                    :booking_options="booking_details"
                ></rental-booking>
            </div>
            <div v-if="booking_details.type == 'table'">
                <table-booking
                    :localObject="localObject"
                    :product="product"
                    :booking_options="booking_details"
                ></table-booking>
            </div>
        

            <div class="pos-buttons text-center">
                <button type="submit" class="pos-button button-md button-dark">{{ $t('pos_home.pos_products.btn_add_to_cart') }}</button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        inject: ['$validator'],

        props: ['localObject', 'product', 'booking_options'],
        
        data() {
            return {
                booking_details: {},
                booking_location: '',
                currency_options: window.pos_currency_symbol,
                product_data: {},
                product_additional: {
                    product_id: this.product.id,
                    quantity: 1,
                    booking: {},
                    attributes: {},
                },
                base_price: this.product.special_price ? this.product.special_price : this.product.price,
                converted_price: this.product.special_price ? this.product.converted_special_price : this.product.converted_price,
                months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            }
        },

        created() {
            this.booking_location   = 'https://maps.google.com/maps?q='+ this.booking_details.location;
        },

        computed: {
            booking_type () {
                return this.manageBooking();
            }
        },

        methods: {
            manageBooking() {
                var thisthis = this;
                    thisthis.booking_details = JSON.parse(thisthis.booking_options);
                return thisthis.booking_details.type;
            },

            updatePrice(event) {
                // var thisthis = this;
                //     if (event.target.checked == true) {
                //         thisthis.base_price = parseFloat(thisthis.base_price) + parseFloat(this.downloadable_links[event.target.value].price);

                //         thisthis.converted_price = parseFloat(thisthis.converted_price) + parseFloat(this.downloadable_links[event.target.value].converted_price);
                //     } else {
                //         thisthis.base_price = parseFloat(thisthis.base_price) - parseFloat(this.downloadable_links[event.target.value].price);

                //         thisthis.converted_price = parseFloat(thisthis.converted_price) - parseFloat(this.downloadable_links[event.target.value].converted_price);
                //     }
            },

            validateOptions() {
                var thisthis = this;
                this.$root.toggleButtonDisable(true);
                this.$validator.validateAll().then(result => {
                    if (result) {
                            var booking     = {};
                            var attributes  = [];
                            var event_booking = {};
                            var rental_booking= {};
                            var rental_slot   = {};
                            var check_qty   = 0;
                            
                            $.each(this.$validator.fields.items, function(key, field) {
                                switch (thisthis.booking_details.type) {
                                    case 'default':
                                        var default_slots = thisthis.booking_details.default_slot.slots;
                                        if ( field.el.name == 'booking[date]' ) {
                                            booking['date'] = field.el.value;
                                        }
                                        
                                        if ( field.el.name == 'booking[slot]' ) {
                                            booking['slot'] = field.el.value;

                                            var current_date_time = new Date(booking['date']);
                                            var formatted_date = current_date_time.getDate() +' '+ thisthis.months[current_date_time.getMonth()] + ', ' + current_date_time.getFullYear();

                                            var findSlot = $.grep(default_slots[booking['date']], function(obj){return obj.timestamp === field.el.value;});
                                            
                                            if ( Object.keys(findSlot).length == 1 ) {
                                                var resulted_slot = findSlot[0];
                                                if ( thisthis.booking_details.default_slot.booking_type == 'many' ) {
                                                    resulted_slot.from = formatted_date +' '+ resulted_slot.from;
                                                    resulted_slot.to = formatted_date +' '+ resulted_slot.to;
                                                }
                                                attributes = [{
                                                    attribute_name: thisthis.$t('pos_home.pos_products.booking_from'),
                                                    option_id: 0,
                                                    option_label: resulted_slot.from
                                                },  {
                                                    attribute_name: thisthis.$t('pos_home.pos_products.booking_till'),
                                                    option_id: 0,
                                                    option_label: resulted_slot.to
                                                }];
                                                
                                                thisthis.product.quantity = 1;
                                            }
                                        }
                                        break;
                                    case 'appointment':
                                        if ( field.el.name == 'booking[date]' ) {
                                            booking['date'] = field.el.value;
                                        }
                                        
                                        if ( field.el.name == 'booking[slot]' ) {
                                            booking['slot'] = field.el.value;
                                            var split_date = field.el.value.split('-');
                                            var from_date = new Date(parseInt(split_date[0]));
                                            var end_date = new Date(parseInt(split_date[1]));
                                            
                                            var from_formatted_date = from_date.getDate() +' '+ thisthis.months[from_date.getMonth()] + ', ' + from_date.getFullYear() + ' ' + thisthis.getFormattedTime(from_date);
                                            
                                            var to_formatted_date = end_date.getDate() +' '+ thisthis.months[end_date.getMonth()] + ', ' + end_date.getFullYear() + ' ' + thisthis.getFormattedTime(end_date);
                                            
                                            attributes = [{
                                                attribute_name: thisthis.$t('pos_home.pos_products.booking_from'),
                                                option_id: 0,
                                                option_label: from_formatted_date
                                            },  {
                                                attribute_name: thisthis.$t('pos_home.pos_products.booking_till'),
                                                option_id: 0,
                                                option_label: to_formatted_date
                                            }];
                                            
                                            thisthis.product.quantity = 1;
                                        }
                                        break;
                                    case 'event':
                                        if ( field.el.name === 'qty['+field.el.id+']' ) {
                                            if ( parseInt(field.el.value) > 0) {
                                                check_qty = parseInt(check_qty) + 1;
                                            }
                                            event_booking[field.el.id] = field.el.value;
                                        }
                                        break;
                                    case 'rental':
                                        if (field.el.name === 'booking[renting_type]' && field.el.checked == true) {
                                            rental_booking['renting_type'] = field.el.value;
                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.rent_type'),
                                                option_id:  0,
                                                option_label:  field.el.value[0].toUpperCase() + field.el.value.slice(1)
                                            });
                                        }
                                        if ( field.el.name === 'booking[date_from]' ) {
                                            rental_booking['date_from'] = field.el.value;
                                            var from_date = new Date(field.el.value);
                                            var formatted_from_date = from_date.getDate() +' '+ thisthis.months[from_date.getMonth()] + ', ' + from_date.getFullYear();
                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.rent_from'),
                                                option_id:  0,
                                                option_label:  formatted_from_date
                                            });
                                        }
                                        if ( field.el.name === 'booking[date_to]' ) {
                                            rental_booking['date_to'] = field.el.value;
                                            var to_date = new Date(field.el.value);
                                            var formatted_to_date = to_date.getDate() +' '+ thisthis.months[to_date.getMonth()] + ', ' + to_date.getFullYear();
                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.rent_till'),
                                                option_id:  0,
                                                option_label:  formatted_to_date
                                            });
                                        }
                                        if ( field.el.name === 'booking[slot]' ) {
                                            rental_booking['slot_index'] = field.el.value;
                                        }
                                        if ( field.el.name === 'booking[date]' ) {
                                            rental_booking['date'] = field.el.value;
                                        }
                                        if ( field.el.name === 'booking[slot][from]' ) {
                                            rental_slot['from']   = field.el.value;
                                            var booking_date = new Date(parseInt(field.el.value));
                                            var formatted_booking_date = booking_date.getDate() +' '+ thisthis.months[booking_date.getMonth()] + ', ' + booking_date.getFullYear() + ' ' + thisthis.getFormattedTime(booking_date);
                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.rent_from'),
                                                option_id:  0,
                                                option_label:  formatted_booking_date
                                            });
                                        }
                                        if ( field.el.name === 'booking[slot][to]' ) {
                                            rental_slot['to']   = field.el.value;
                                            var booking_date = new Date(parseInt(field.el.value));
                                            var formatted_booking_date = booking_date.getDate() +' '+ thisthis.months[booking_date.getMonth()] + ', ' + booking_date.getFullYear() + ' ' + thisthis.getFormattedTime(booking_date);
                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.rent_till'),
                                                option_id:  0,
                                                option_label:  formatted_booking_date
                                            });
                                        }
                                        break;
                                    case 'table':
                                        if ( field.el.name === 'booking[date]' ) {
                                            booking['date'] = field.el.value;
                                        }
                                        if ( field.el.name === 'booking[slot]' ) {
                                            booking['slot'] = field.el.value;
                                            var get_slot = field.el.value.split('-');
                                            var booking_from    = new Date(parseInt(get_slot[0]));
                                            var booking_to      = new Date(parseInt(get_slot[1]));
                                            
                                            var formatted_booking_from = booking_from.getDate() +' '+ thisthis.months[booking_from.getMonth()] + ', ' + booking_from.getFullYear() + ' ' + thisthis.getFormattedTime(booking_from);

                                            var formatted_booking_to = booking_to.getDate() +' '+ thisthis.months[booking_to.getMonth()] + ', ' + booking_to.getFullYear() + ' ' + thisthis.getFormattedTime(booking_to);

                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.booking_from'),
                                                option_id:  0,
                                                option_label:   formatted_booking_from
                                            },  {
                                                attribute_name:  thisthis.$t('pos_home.pos_products.booking_till'),
                                                option_id:  0,
                                                option_label:   formatted_booking_to
                                            });
                                        }
                                        if ( field.el.name === 'booking[note]' ) {
                                            booking['note'] = field.el.value;
                                            thisthis.product.quantity = 1;
                                            attributes.push({
                                                attribute_name:  thisthis.$t('pos_home.pos_products.special_note'),
                                                option_id:  0,
                                                option_label:   field.el.value.substring(0, 20) + '...'
                                            });
                                        }
                                        break;
                                
                                    default:
                                        break;
                                }
                            });
                            
                            if ( thisthis.booking_details.type == 'rental' ) {
                                var rental_slots    = thisthis.booking_details.rental_slot;
                                    thisthis.product.quantity = 1;
                                if ( rental_booking['renting_type'] == 'hourly' ) {
                                    rental_booking['slot']    = rental_slot;
                                    var from_date   = new Date(parseInt(rental_slot.from));
                                    var till_date   = new Date(parseInt(rental_slot.to));
                                    var total_hours = Math.abs(from_date - till_date) / 36e5;

                                    thisthis.base_price = parseFloat(thisthis.product.price) + (parseFloat(rental_slots.hourly_price) * total_hours);

                                    thisthis.converted_price = parseFloat(thisthis.product.converted_price) + (parseFloat(rental_slots.hourly_price) * total_hours);
                                } else {
                                    var from_date   = new Date(rental_booking.date_from + ' 00:00:00');
                                    var till_date   = new Date(rental_booking.date_to + ' 24:00:00');
                                    
                                    var total_days = (till_date.getTime() - from_date.getTime()) / (1000 * 3600 * 24);
                                    
                                    thisthis.base_price = parseFloat(thisthis.product.price) + (parseFloat(rental_slots.daily_price) * total_days);

                                    thisthis.converted_price = parseFloat(thisthis.product.converted_price) + (parseFloat(rental_slots.daily_price) * total_days);
                                }
                                booking  = rental_booking;
                            }
                            
                            if ( thisthis.booking_details.type == 'event' ) {
                                if ( check_qty == 0 ) {
                                    thisthis.$toast.error(thisthis.$t('pos_home.pos_products.error.select_atleast_one'), {
                                        position: 'top-right',
                                        duration: 2000,
                                        });
                                        
                                    this.$root.toggleButtonDisable(false);
                                    return false;
                                } else {
                                    var event_tickets = thisthis.booking_details.event_tickets;
                                    $.each(event_tickets, function(key, ticket) {
                                        if ( ticket.id && parseInt(event_booking[ticket.id]) > 0 ) {
                                            var booking     = {};
                                            var attributes  = [];
                                            var event_from = new Date(thisthis.booking_details.available_from);
                                            var event_till = new Date(thisthis.booking_details.available_to);
                                            
                                            var event_from_date = event_from.getDate() +' '+ thisthis.months[event_from.getMonth()] + ', ' + event_from.getFullYear();
                                            var event_till_date = event_till.getDate() +' '+ thisthis.months[event_till.getMonth()] + ', ' + event_till.getFullYear();

                                            booking['ticket_id']    =   ticket.id;

                                            attributes = [{
                                                    attribute_name: thisthis.$t('pos_home.pos_products.event_ticket'),
                                                    option_id: 0,
                                                    option_label: ticket.name
                                                },  {
                                                    attribute_name: thisthis.$t('pos_home.pos_products.event_from'),
                                                    option_id: 0,
                                                    option_label: event_from_date
                                                },  {
                                                    attribute_name: thisthis.$t('pos_home.pos_products.event_till'),
                                                    option_id: 0,
                                                    option_label: event_till_date
                                                }];
                                            
                                            var product_additional  = {
                                                    product_id: thisthis.product.id,
                                                    quantity: parseInt(event_booking[ticket.id]),
                                                    booking: booking,
                                                    attributes: attributes,
                                                };
                                            thisthis.product.quantity = parseInt(event_booking[ticket.id]);
                                            thisthis.prepareForCart(thisthis.product, product_additional);
                                        }
                                    });
                                }
                            } else {
                                thisthis.product_additional.booking     = booking;
                                thisthis.product_additional.attributes  = attributes;
                                thisthis.prepareForCart(thisthis.product, thisthis.product_additional);
                            }

                            this.$root.toggleButtonDisable(false);
                            EventBus.$emit('hideCommonModal', thisthis.product.type);
                    } else {
                        this.$root.toggleButtonDisable(false);
                    }
                });
            },

            getFormattedTime(date) {
                var date_hours  = parseInt(date.getHours());
                var date_am_pm  = parseInt(date_hours) > 12 ? "PM" : "AM";

                if (parseInt(date_hours) > 12) {
                    date_hours = parseInt(date_hours) - 12;
                }
                if (parseInt(date_hours) < 10 ) {
                    date_hours = '0' + date_hours;
                }

                var date_minutes = parseInt(date.getMinutes());
                if ( parseInt(date_minutes) == 0 ) {
                    date_minutes = '00';
                } else if (parseInt(date_minutes) < 10 ) {
                    date_minutes = '0' + date_minutes;
                }

                return date_hours + ":" + date_minutes + " " + date_am_pm;
            },

            prepareForCart: function(product, additional) {
                var self                = this;
                var quantity            = 1;
                var pos_qty             = {};
                var product_id          = product.id;
                pos_qty[product_id]     = self.booking_details.qty;

                    if ( this.booking_details.type === 'event' ) {
                        var obj = $.grep(self.booking_details.event_tickets, function(obj){return obj.id === additional.booking.ticket_id;})[0];
                        pos_qty[product_id] = obj.qty;
                        
                        self.base_price     = self.product.price + obj.price;
                        self.converted_price = parseFloat(self.product.converted_price) + parseFloat(obj.converted_price);
                    }
                    if ( product.quantity ) {
                        quantity = product.quantity;
                    }
                    self.product_data   = {
                        id:                 product_id,
                        quantity:           quantity,
                        pos_qty:            JSON.stringify(pos_qty),
                        sku:                product.sku,
                        type:               product.type,
                        name:               product.name,
                        coupon_code:        '',
                        weight:             0,
                        total_weight:       0,
                        base_total_weight:  0,
                        price:              parseFloat(this.converted_price).toFixed(2),
                        formated_price:     this.currency_options + parseFloat(this.converted_price).toFixed(2),
                        base_price:         parseFloat(this.base_price).toFixed(2),
                        custom_price:       0,
                        
                        total:              (1 * parseFloat(this.converted_price).toFixed(2)),
                        base_total:         (1 * parseFloat(this.base_price).toFixed(2)),
                        
                        tax_category_id:    product.tax_category_id,
                        tax_percent:        product.tax_percent,
                        tax_amount:         (((1 * parseFloat(this.converted_price).toFixed(2)) * product.tax_percent) / 100 ),
                        base_tax_amount:    (((1 * parseFloat(this.base_price).toFixed(2)) * product.tax_percent) / 100 ),

                        discount_percent:   0,
                        discount_amount:    0,
                        base_discount_amount: 0,

                        additional:         additional,
                    };
                    
                    EventBus.$emit('cartAddProduct', self.product_data);
            }
        }        
    }
</script>