<template>
    <div class="quantity control-group" :class="[this.errors.has('qty['+controlName+']') ? 'has-error' : '']">
        <label :class="[fieldRequired ? 'required' : '']">Quantity</label>
        <button type="button" class="decrease" @click="decreaseQty()">-</button>

        <input class="control form-field" :name="'qty['+controlName+']'" v-validate="validations" data-vv-as="Quantity" :id="controlName" :value="qty"  />

        <button type="button" class="increase" @click="increaseQty()">+</button>

        <span class="control-error" v-if="errors.has('qty['+controlName+']')">{{ errors.first('qty['+controlName+']') }}</span>
    </div>

</template>

<script type="text/javascript">

    export default {
        inject: ['$validator'],
        
        props: {
            controlName: {
                type: [Number, String],
                default: 'quantity'
            },

            fieldRequired: {
                type: [Number, String],
                default: 1
            },

            quantity: {
                type: [Number, String],
                default: 1
            },

            minQuantity: {
                type: [Number, String],
                default: 1
            },

            validations: {
                type: String,
                default: 'required|numeric|min_value:1'
            }
        },

        data: function () {
            return {
                qty: this.quantity
            }
        },

        watch: {
            quantity: function (val) {
                this.qty = val;
                
                this.$emit('onQtyUpdated', {field: this.controlName, qty: this.qty});
            }
        },

        methods: {
            decreaseQty: function() {
                if (this.qty > this.minQuantity)
                    this.qty = parseInt(this.qty) - 1;
                this.$emit('onQtyUpdated', {field: this.controlName, qty: this.qty});
            },

            increaseQty: function() {
                this.qty = parseInt(this.qty) + 1;

                this.$emit('onQtyUpdated', {field: this.controlName, qty: this.qty});
            }
        }
    }
</script>