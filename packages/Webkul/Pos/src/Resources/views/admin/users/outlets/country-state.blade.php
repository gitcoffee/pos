<country-state></country-state>

@push('scripts')

    <script type="text/x-template" id="country-state-template">
        <div>
            <div class="control-group" :class="[errors.has('country') ? 'has-error' : '']">
                <label for="country" class="required">
                    {{ __('pos::app.admin.users.outlets.create.country') }}
                </label>

                <?php $countryCode = (isset($outlet->country) && $outlet->country) ? $outlet->country : ''; ?>

                <select type="text" v-validate="'required'" class="control" id="country" name="country" v-model="country" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.create.country') }}&quot;">
                    <option value="">{{ __('pos::app.admin.users.outlets.create.select-country') }}</option>

                    @foreach (core()->countries() as $country)
                        <option value="{{ $country->code }}">{{ $country->name }}</option>
                    @endforeach
                </select>

                <span class="control-error" v-if="errors.has('country')">
                    @{{ errors.first('country') }}
                </span>
            </div>

            <div class="control-group" :class="[errors.has('state') ? 'has-error' : '']">
                <label for="state" class="required">
                    {{ __('pos::app.admin.users.outlets.create.state') }}
                </label>

                <?php $stateCode = (isset($outlet->state) && $outlet->state) ? $outlet->state : ''; ?>

                <input type="text" v-validate="'required'" class="control" id="state" name="state" v-model="state" v-if="!haveStates()" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.create.state') }}&quot;"/>
                <select v-validate="'required'" class="control" id="state" name="state" v-model="state" v-if="haveStates()" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.create.state') }}&quot;">

                    <option value="">{{ __('pos::app.admin.users.outlets.create.select-state') }}</option>

                    <option v-for='(state, index) in countryStates[country]' :value="state.code">
                        @{{ state.default_name }}
                    </option>

                </select>

                <span class="control-error" v-if="errors.has('state')">
                    @{{ errors.first('state') }}
                </span>
            </div>
        </div>
    </script>

    <script>
        Vue.component('country-state', {

            template: '#country-state-template',

            inject: ['$validator'],

            data: () => ({
                country: "{{ $countryCode  }}",

                state: "{{ $stateCode  }}",

                countryStates: @json(core()->groupedStatesByCountries())
            }),

            methods: {
                haveStates() {
                    if (this.countryStates[this.country] && this.countryStates[this.country].length)
                        return true;

                    return false;
                },
            }
        });
    </script>
@endpush