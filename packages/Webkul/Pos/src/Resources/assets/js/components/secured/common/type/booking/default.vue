
<template>
    <div class="booking-information" v-if="booking_options.type == 'default'">
        <div class="heading">{{ $t('pos_home.pos_products.book_your_slot') }}</div>
        
        <div class="booking-info-row" v-if="booking_options.default_slot.duration">
            <i class="fa fa-calendar"></i>
            <span class="title">
                {{ $t('pos_home.pos_products.slot_duration') }}
                
                {{ $t('pos_home.pos_products.slot_minutes', {'minutes': booking_options.default_slot.duration}) }}
            </span>
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
    }
</script>