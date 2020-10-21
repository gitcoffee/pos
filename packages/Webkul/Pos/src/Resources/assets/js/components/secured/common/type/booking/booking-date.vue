<template>
	<span>
		<slot>
			<input type="text" :name="name" class="control" :value="value" data-input :data-vv-as="erros_msg">
		</slot>
	</span>
</template>

<script>
	import Flatpickr from 'flatpickr';

	export default {
		props: {
			name: String,
			value: String,
			erros_msg: String,
		},

		data () {
			return {
				datepicker: null
			}
		},

		mounted () {
			var this_this = this;
			
			var element = this.$el.getElementsByTagName('input')[0];
			this.datepicker = new Flatpickr(
				element, {
					altFormat: 'Y-m-d',
					dateFormat: 'Y-m-d',
					minDate: 'today',
					onChange: function(selectedDates, dateStr, instance) {
						this_this.$emit('onChange', dateStr)
					},
				});
		}
	};
</script>