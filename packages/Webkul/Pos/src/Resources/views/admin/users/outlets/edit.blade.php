@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.users.outlets.edit-outlet-title') }}
@stop

@section('content')

    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                    {{ __('pos::app.admin.users.outlets.edit-outlet-title') }}
                </h1>
            </div>

            <div class="page-action">
                <button type="submit" class="btn btn-lg btn-primary" form="form-outlet-info">
                    {{ __('pos::app.admin.users.outlets.save-btn-title') }}
                </button>
            </div>
        </div>

        <div class="page-content">
    
            <form method="POST" action="{{ route('admin.pos.outlets.update', $outlet->id) }}" id="form-outlet-info" @submit.prevent="onSubmit">
                
                <div class="outlet-container">
                    @csrf()

                    <input name="_method" type="hidden" value="PUT">

                    <accordian :title="'{{ __('pos::app.admin.users.outlets.general') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('pos::app.admin.users.outlets.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.name') }}&quot;" value="{{ $outlet->name }}" />
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>


                            <div class="control-group">
                                <label for="status">{{ __('pos::app.admin.users.outlets.status') }}</label>

                                <select class="control" id="status" name="status">
                                    <option value="0" {{ !$outlet->status ? '' : 'selected'}}>
                                        {{ __('pos::app.admin.users.outlets.inactive') }}
                                    </option>
                                    <option value="1" {{ $outlet->status ? 'selected' : ''}}>
                                        {{ __('pos::app.admin.users.outlets.active') }}
                                    </option>
                                </select>    
                            </div>

                        </div>
                    </accordian>

                    <accordian :title="'{{ __('pos::app.admin.users.outlets.outlet_address') }}'" :active="true">
                        <div slot="body">

                            <div class="control-group" :class="[errors.has('address') ? 'has-error' : '']">
                                <label for="address" class="required">{{ __('pos::app.admin.users.outlets.address') }}</label>
                                <textarea class="control" v-validate="'required|max:250'" id="address" name="address" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.address') }}&quot;">{{ $outlet->address }}</textarea>
                                <span class="control-error" v-if="errors.has('address')">@{{ errors.first('address') }}</span>
                            </div>
                            
                            @include ('pos::admin.users.outlets.country-state', ['countryCode' => old('country'), 'stateCode' => old('state')])

                            <div class="control-group" :class="[errors.has('city') ? 'has-error' : '']">
                                <label for="city" class="required">{{ __('pos::app.admin.users.outlets.city') }}</label>
                                <input type="text" v-validate="'required|max:100'" class="control" id="city" name="city" value="{{ $outlet->city }}" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.city') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('city')">@{{ errors.first('city') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('postcode') ? 'has-error' : '']">
                                <label for="postcode" class="required">{{ __('pos::app.admin.users.outlets.postcode') }}</label>
                                <input type="text" v-validate="'required|max:6'" class="control" id="postcode" name="postcode" value="{{ $outlet->postcode }}" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.postcode') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('postcode')">@{{ errors.first('postcode') }}</span>
                            </div>
                        </div>
                    </accordian>


                    <accordian :title="'{{ __('pos::app.admin.users.outlets.inventory_source') }}'" :active="true">
                        <div slot="body">

                            <div class="control-group" :class="[errors.has('inventory_source_id') ? 'has-error' : '']">
                                <label for="inventory_source" class="required">{{ __('pos::app.admin.users.outlets.create.entry_inventory_source') }}</label>

                                <select class="control" id="inventory_source" name="inventory_source_id" v-validate="'required'" data-vv-as="&quot;{{ __('pos::app.admin.users.outlets.inventory_source') }}&quot;">
                                    @foreach ($inventorySource as $source)
                                        <option value="{{ $source->id }}" {{ $outlet->inventory_source_id != $source->id ? '' : 'selected'}}>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="control-error" v-if="errors.has('inventory_source_id')">@{{ errors.first('inventory_source_id') }}</span>
                            </div>
                        </div>
                    </accordian>
                </div>
            </form>
        </div>
    </div>
@stop

