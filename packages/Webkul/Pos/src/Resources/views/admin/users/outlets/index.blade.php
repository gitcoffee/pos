@extends('pos::admin.layouts.content')

@section('page_title')
    {{ __('pos::app.admin.users.outlets.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('pos::app.admin.users.outlets.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('admin.pos.outlets.create') }}" class="btn btn-lg btn-primary">
                    {{ __('pos::app.admin.users.outlets.btn-add-outlet') }}
                </a>
                <a href="{{ config('app.url') . '/pos/login' }}" target="_blank" class="btn btn-lg btn-dark">
                    {{ __('pos::app.admin.system.pos.btn-pos-front') }}
                </a>
            </div>
        </div>

        <div class="page-content">
            
            {!! app('Webkul\Pos\DataGrids\Admin\PosOutletDataGrid')->render() !!}

        </div>
    </div>
@stop