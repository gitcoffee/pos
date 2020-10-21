@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.users.users.edit-user-title') }}
@stop

@section('content')
    <div class="content">
            <form method="POST" action="{{ route('admin.pos.users.update', $posUser->id) }}" @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/dashboard') }}';"></i>

                        {{ __('pos::app.admin.users.users.edit-user-title') }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('pos::app.admin.users.users.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @method('PUT')

                    @csrf()

                    <accordian :title="'{{ __('pos::app.admin.users.users.general') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('username') ? 'has-error' : '']">
                                <label for="username" class="required">{{ __('pos::app.admin.users.users.username') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="username" name="username" value="{{ $posUser->username }}" readonly data-vv-as="&quot;{{ __('pos::app.admin.users.users.username') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('username')">@{{ errors.first('username') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('firstname') ? 'has-error' : '']">
                                <label for="firstname" class="required">{{ __('pos::app.admin.users.users.firstname') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="firstname" name="firstname" value="{{ $posUser->firstname }}"  data-vv-as="&quot;{{ __('pos::app.admin.users.users.firstname') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('firstname')">@{{ errors.first('firstname') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('lastname') ? 'has-error' : '']">
                                <label for="lastname" class="required">{{ __('pos::app.admin.users.users.lastname') }}</label>
                                <input type="text" v-validate="'required'" class="control" id="lastname" name="lastname" value="{{ $posUser->lastname }}" data-vv-as="&quot;{{ __('pos::app.admin.users.users.lastname') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('lastname')">@{{ errors.first('lastname') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                                <label for="email" class="required">{{ __('pos::app.admin.users.users.email') }}</label>
                                <input type="email" v-validate="'required'" class="control" id="email" name="email" value="{{ $posUser->email }}"  data-vv-as="&quot;{{ __('pos::app.admin.users.users.email') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('email')">@{{ errors.first('email') }}</span>
                            </div>

                        </div>
                    </accordian>

                    <accordian :title="'{{ __('pos::app.admin.users.users.user-image') }}'" :active="true">
                        <div slot="body">
                            <image-wrapper :button-label="'{{ __('pos::app.admin.users.users.add-avatar') }}'" :remove-button-label="'{{ __('pos::app.admin.users.users.remove-avatar') }}'" input-name="avatar" :images='@json($posUser->avatar)' :multiple="false"></image-wrapper>

                        </div>
                    </accordian>

                    <accordian :title="'{{ __('pos::app.admin.users.users.password') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('password') ? 'has-error' : '']">
                                <label for="password" class="required">{{ __('pos::app.admin.users.users.password') }}</label>
                                <input type="password" v-validate="'min:6|max:18'" class="control" id="password" name="password" data-vv-as="&quot;{{ __('pos::app.admin.users.users.password') }}&quot;" ref="password"/>
                                <span class="control-error" v-if="errors.has('password')">@{{ errors.first('password') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('password_confirmation') ? 'has-error' : '']">
                                <label for="password_confirmation" class="required">{{ __('pos::app.admin.users.users.confirm-password') }}</label>
                                <input type="password" v-validate="'min:6|max:18|confirmed:password'" class="control" id="password_confirmation" name="password_confirmation" data-vv-as="&quot;{{ __('pos::app.admin.users.users.confirm-password') }}&quot;"/>
                                <span class="control-error" v-if="errors.has('password_confirmation')">@{{ errors.first('password_confirmation') }}</span>
                            </div>
                        </div>
                    </accordian>

                    <accordian :title="'{{ __('pos::app.admin.users.users.status-outlet') }}'" :active="true">
                        <div slot="body">
                            <div class="control-group" :class="[errors.has('outlet_id') ? 'has-error' : '']">
                                <label for="outlet" class="required">{{ __('pos::app.admin.users.users.outlet') }}</label>

                                <select class="control" name="outlet_id" v-validate="'required'" id="outlet" data-vv-as="&quot;{{ __('pos::app.admin.users.users.outlet') }}&quot;">
                                    <option value="">{{ __('pos::app.admin.users.users.select-outlet') }}</option>

                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}" {{ (isset($posUser->outlet_id) && $posUser->outlet_id == $outlet->id) ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                                <span class="control-error" v-if="errors.has('outlet_id')">@{{ errors.first('outlet_id') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="status">{{ __('pos::app.admin.users.users.status') }}</label>

                                <select class="control" id="status" name="status">
                                    <option value="0" {{ (isset($posUser->status) && $posUser->status) ? '' : 'selected'}}>
                                        {{ __('pos::app.admin.users.users.inactive') }}
                                    </option>
                                    <option value="1" {{ (isset($posUser->status) && $posUser->status) ? 'selected' : ''}}>
                                        {{ __('pos::app.admin.users.users.active') }}
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