@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.restaurants.tables.title-table-create') }}
@stop

@section('content')
    <div class="content">
        <form method="POST" action="{{ route('admin.pos.restaurants.tables.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                        {{ __('pos::app.admin.restaurants.tables.title-table-create') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('pos::app.admin.restaurants.tables.btn-save-table') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    <accordian :title="'{{ __('pos::app.admin.restaurants.tables.general') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('pos::app.admin.restaurants.tables.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" data-vv-as="&quot;{{ __('pos::app.admin.restaurants.tables.name') }}&quot;" value="{{ old('name') }}" />
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <?php $input_table_type = (isset($type) && $type) ? $type : old('type') ?>

                            <div class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                                <label for="type" class="required">{{ __('pos::app.admin.restaurants.tables.table-type') }}</label>
                                <select class="control" name="type" v-validate="'required'" id="type" data-vv-as="&quot;{{ __('pos::app.admin.restaurants.tables.table-type') }}&quot;">
                                    <option value="">{{ __('pos::app.admin.restaurants.tables.select-type') }}</option>

                                    @foreach ($table_types as $table_type)
                                        <option value="{{ $table_type }}" {{ ($input_table_type == $table_type) ? 'selected' : '' }}>{{ ucfirst(str_replace("-", " ", $table_type)) }}</option>
                                    @endforeach
                                </select>
                                <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="status">{{ __('pos::app.admin.restaurants.tables.status') }}</label>

                                <select class="control" id="status" name="status">
                                    <option value="0" {{ (isset($selectedOption) && $selectedOption) ? '' : 'selected'}}>
                                        {{ __('pos::app.admin.restaurants.tables.inactive') }}
                                    </option>
                                    <option value="1" {{ (isset($selectedOption) && $selectedOption) ? 'selected' : ''}}>
                                        {{ __('pos::app.admin.restaurants.tables.active') }}
                                    </option>
                                </select>    
                            </div>

                            <div class="control-group" :class="[errors.has('position') ? 'has-error' : '']">
                                <label for="position" class="required">{{ __('pos::app.admin.restaurants.tables.position') }}</label>
                                <input type="number" v-validate="'required|numeric'" class="control" id="position" name="position" data-vv-as="&quot;{{ __('pos::app.admin.restaurants.tables.position') }}&quot;"  value="{{ old('position') }}" />
                                <span class="control-error" v-if="errors.has('position')">@{{ errors.first('position') }}</span>
                            </div>

                        </div>
                    </accordian>

                    <accordian :title="'{{ __('pos::app.admin.restaurants.tables.seat-and-agent') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('no_of_seat') ? 'has-error' : '']">
                                <label for="no_of_seat" class="required">{{ __('pos::app.admin.restaurants.tables.total-seat') }}</label>
                                <input type="number" v-validate="'required|numeric|min_value:1|max_value:20'" class="control" id="no_of_seat" name="no_of_seat" data-vv-as="&quot;{{ __('pos::app.admin.restaurants.tables.total-seat') }}&quot;"  value="{{ old('no_of_seat') }}" />
                                <span class="control-error" v-if="errors.has('no_of_seat')">@{{ errors.first('no_of_seat') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('agent_id') ? 'has-error' : '']">
                                <label for="agent_id" class="required">{{ __('pos::app.admin.restaurants.tables.assign-agent') }}</label>

                                <select class="control" name="agent_id" v-validate="'required'" id="agent_id" data-vv-as="&quot;{{ __('pos::app.admin.restaurants.tables.assign-agent') }}&quot;">
                                    <option value="">{{ __('pos::app.admin.restaurants.tables.select-agent') }}</option>

                                    @foreach ($posUsers as $agent)
                                        <option value="{{ $agent->id }}" {{ (isset($agent_id) && $agent_id == $agent->id) ? 'selected' : '' }}>{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                                <span class="control-error" v-if="errors.has('agent_id')">@{{ errors.first('agent_id') }}</span>
                            </div>
                        </div>
                    </accordian>

                </div>
            </div>
        </form>
    </div>
@stop