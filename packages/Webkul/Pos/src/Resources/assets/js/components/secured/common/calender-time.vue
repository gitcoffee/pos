<template>
	<span>
		<slot>
			<input type="text" :name="name" class="control" :value="value" data-input>
		</slot>
	</span>
</template>

<script>
	import Flatpickr from 'flatpickr';

	export default {
		props: {
			name: String,
			value: String,
		},

		data () {
			return {
				timepicker: null
			}
		},

		mounted () {
			var this_this = this;
			var current_date_time = new Date();

			var element = this.$el.getElementsByTagName('input')[0];
			this.timepicker = new Flatpickr(
				element, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i:s",
					time_24hr: true,
					// minTime: current_date_time.getHours() + ':' + current_date_time.getMinutes() + ':' + current_date_time.getSeconds(),
					onChange: function(selectedDates, dateStr, instance) {
						this_this.$emit('onChange', dateStr)
					},
				});
		}
	};
</script>