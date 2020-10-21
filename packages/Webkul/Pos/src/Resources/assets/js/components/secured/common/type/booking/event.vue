
<template>
    <div class="booking-information event-booking" v-if="booking_options.type == 'event'">        
        <div class="booking-info-row">
            <i class="fa fa-calendar"></i>
            <span class="title">
                {{ $t('pos_home.pos_products.event_on') }}
            </span>

            <span class="value">
                {{ event_on }}
            </span>
        </div>
        
        <div class="book-slots">
            <div class="heading">
                {{ $t('pos_home.pos_products.book_your_ticket') }}
            </div>

            <div class="ticket-list">
                <div class="ticket-item" v-for="(ticket, index) in tickets" :key="index">
                    <div class="ticket-info">
                        <div class="ticket-name">
                            {{ ticket.name }}
                        </div>

                        <div class="ticket-price">
                            {{ ticket.formatted_converted_price }}
                        </div>
                    </div>

                    <div class="ticket-quantity qty">
                        <div class="ticket-description">{{ ticket.description }}</div>
                        <quantity-panel
                            :control-name="ticket.id"
                            :field-required="'1'"
                            :validations="'required|numeric|min_value:0'"
                            :quantity="0">
                        </quantity-panel>
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
                event_on: '',
                tickets: [],
            }
        },

        created() {
            this.event_on = this.booking_options.event_dates;
            if ( Object.keys(this.booking_options.event_tickets).length > 0) {
                this.tickets  = this.booking_options.event_tickets;
            }
        }     
    }
</script>