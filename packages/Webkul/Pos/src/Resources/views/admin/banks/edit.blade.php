@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.banks.add-title') }}
@stop

@section('content')
    <div class="content">
        <form method="POST" action="{{ route('admin.pos.banks.update', $posBank->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/pos/banks') }}';"></i>

                        {{ __('pos::app.admin.banks.add-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('pos::app.admin.banks.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()
                    <input name="_method" type="hidden" value="PUT">

                    <accordian :title="'{{ __('pos::app.admin.banks.general') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="name" class="required">{{ __('pos::app.admin.banks.name') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="name" name="name" data-vv-as="&quot;{{ __('pos::app.admin.banks.name') }}&quot;" value="{{ old('name') ?: $posBank->name }}" />
                                <span class="control-error" v-if="errors.has('name')">@{{ errors.first('name') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('address') ? 'has-error' : '']">
                                <label for="address" class="required">{{ __('pos::app.admin.banks.address') }}</label>
                                <textarea v-validate="'required'" class="control" id="address" name="address" data-vv-as="&quot;{{ __('pos::app.admin.banks.address') }}&quot;">{{ old('address') ?: $posBank->address }}</textarea>
                                <span class="control-error" v-if="errors.has('address')">@{{ errors.first('address') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                                <label for="email">{{ __('pos::app.admin.banks.email') }}</label>
                                <input type="email" v-validate="'email'" class="control" id="email" name="email" data-vv-as="&quot;{{ __('pos::app.admin.banks.email') }}&quot;" value="{{ old('email') ?: $posBank->email }}" />
                                <span class="control-error" v-if="errors.has('email')">@{{ errors.first('email') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('phone') ? 'has-error' : '']">
                                <label for="phone">{{ __('pos::app.admin.banks.phone') }}</label>
                                <input type="text" v-validate="'numeric|max:13'" class="control" id="phone" name="phone" data-vv-as="&quot;{{ __('pos::app.admin.banks.phone') }}&quot;" value="{{ old('phone') ?: $posBank->phone }}" />
                                <span class="control-error" v-if="errors.has('phone')">@{{ errors.first('phone') }}</span>
                            </div>

                        </div>
                    </accordian>

                    <accordian :title="'{{ __('pos::app.admin.banks.agent-and-status') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('agent_id') ? 'has-error' : '']">
                                <label for="agent_id" class="required">{{ __('pos::app.admin.banks.agent') }}</label>

                                <select class="control" name="agent_id" v-validate="'required'" id="agent_id" data-vv-as="&quot;{{ __('pos::app.admin.banks.agent') }}&quot;">
                                    <option value="">{{ __('pos::app.admin.banks.select-agent') }}</option>

                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ (isset($posBank->agent_id) && $posBank->agent_id == $agent->id) ? 'selected' : '' }}>{{ $agent->firstname .' '. $agent->lastname }}</option>
                                    @endforeach
                                </select>
                                <span class="control-error" v-if="errors.has('agent_id')">@{{ errors.first('agent_id') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="status">{{ __('pos::app.admin.banks.bank-status') }}</label>

                                <select class="control" id="status" name="status">
                                    <option value="0" {{ (isset($posBank->status) && $posBank->status) ? '' : 'selected'}}>
                                        {{ __('pos::app.admin.banks.inactive') }}
                                    </option>
                                    <option value="1" {{ (isset($posBank->status) && $posBank->status) ? 'selected' : ''}}>
                                        {{ __('pos::app.admin.banks.active') }}
                                    </option>
                                </select>    
                            </div>
                        </div>
                    </accordian>

                </div>
            </div>
        </form>
    </div>
@stop